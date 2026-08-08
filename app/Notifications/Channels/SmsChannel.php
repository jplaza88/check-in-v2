<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use App\Sms\SmsMessage;
use App\Sms\SmsSender;
use Illuminate\Notifications\Notification;

/**
 * Delivers a notification's toSms() payload through whichever
 * {@see SmsSender} is bound.
 *
 * Named as a channel class rather than a string alias so `via()` can reference
 * it without a service-provider registration.
 */
final readonly class SmsChannel
{
    public function __construct(private SmsSender $sender) {}

    public function send(mixed $notifiable, Notification $notification): void
    {
        $number = $notifiable->routeNotificationFor('sms', $notification);

        // A driver can clear their number between the notification being queued
        // and the worker picking it up, so this is not unreachable.
        if (! is_string($number) || $number === '') {
            return;
        }

        if (! method_exists($notification, 'toSms')) {
            return;
        }

        $message = $notification->toSms($notifiable);

        if (! $message instanceof SmsMessage) {
            return;
        }

        $this->sender->send($message);
    }
}
