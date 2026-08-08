<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\RecordHistoryAction;
use App\Enums\RecordHistoryEvent;
use App\History\ChannelName;
use App\History\RecordNotification;
use App\Models\User;
use Illuminate\Mail\SentMessage;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Events\NotificationSent;
use Throwable;

/**
 * Writes one trail row per notification per channel.
 *
 * One row per channel is the point: a driver on Both produces a mail row and an
 * sms row, which is exactly the question an admin is asking when they open the
 * record. Anything implementing {@see RecordNotification} is picked up here, so
 * new message types need no change to this class.
 */
final readonly class RecordNotificationHistory
{
    public function __construct(
        private RecordHistoryAction $history,
        private ChannelName $channels,
    ) {}

    public function handleSent(NotificationSent $event): void
    {
        if (! $event->notification instanceof RecordNotification) {
            return;
        }

        $this->record($event->notification, $event->channel, RecordHistoryEvent::NotificationSent, [
            'messageId' => $this->messageId($event->response),
        ]);
    }

    public function handleFailed(NotificationFailed $event): void
    {
        if (! $event->notification instanceof RecordNotification) {
            return;
        }

        $exception = $event->data['exception'] ?? null;

        $this->record($event->notification, $event->channel, RecordHistoryEvent::NotificationFailed, [
            'reason' => $exception instanceof Throwable ? $exception->getMessage() : null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function record(
        RecordNotification $notification,
        string $channel,
        RecordHistoryEvent $event,
        array $context,
    ): void {
        $record = $notification->historyRecord();
        $record->loadMissing('user');

        $user = $record->user;

        $this->history->handle(
            record: $record,
            event: $event,
            subject: $notification->historySubject(),
            channel: $this->channels->forChannel($channel),
            // The locale the notification was pinned to when it was queued, which
            // is what the driver actually received, not whatever the worker has
            // set at the moment this listener runs.
            locale: $notification->locale ?? $record->locale,
            user: $user instanceof User ? $user : null,
            context: array_filter($context, static fn (mixed $value): bool => $value !== null),
        );
    }

    /**
     * Worth keeping: it is what support needs to trace a message with the ESP
     * when a driver says the email never arrived.
     */
    private function messageId(mixed $response): ?string
    {
        return $response instanceof SentMessage ? $response->getMessageId() : null;
    }
}
