<?php

declare(strict_types=1);

use App\Notifications\Channels\SmsChannel;
use App\Sms\SmsMessage;
use App\Sms\SmsSender;
use Illuminate\Notifications\Notification;

/**
 * Records what it was handed instead of sending it, so the channel can be
 * tested without a carrier.
 */
final class RecordingSmsSender implements SmsSender
{
    /** @var list<SmsMessage> */
    public array $sent = [];

    public function send(SmsMessage $message): void
    {
        $this->sent[] = $message;
    }
}

final class TextableNotification extends Notification
{
    public function toSms(mixed $notifiable): SmsMessage
    {
        return new SmsMessage(to: '+12015550123', body: 'Ref ABCD2345');
    }
}

/** A notification with no SMS payload, which the channel must simply skip. */
final class MailOnlyNotification extends Notification {}

function notifiableWith(?string $number): object
{
    return new readonly class($number)
    {
        public function __construct(private ?string $number) {}

        public function routeNotificationFor(string $driver, mixed $notification = null): ?string
        {
            return $this->number;
        }
    };
}

it('hands the message to the sender', function (): void {
    $sender = new RecordingSmsSender;

    new SmsChannel($sender)->send(notifiableWith('+12015550123'), new TextableNotification);

    expect($sender->sent)->toHaveCount(1)
        ->and($sender->sent[0]->to)->toBe('+12015550123')
        ->and($sender->sent[0]->body)->toBe('Ref ABCD2345');
});

/*
 * A driver can clear their number between the notification being queued and the
 * worker picking it up, so this path is reachable in production.
 */
it('sends nothing when the notifiable has no number', function (): void {
    $sender = new RecordingSmsSender;

    new SmsChannel($sender)->send(notifiableWith(null), new TextableNotification);

    expect($sender->sent)->toBeEmpty();
});

it('sends nothing when the notification has no text payload', function (): void {
    $sender = new RecordingSmsSender;

    new SmsChannel($sender)->send(notifiableWith('+12015550123'), new MailOnlyNotification);

    expect($sender->sent)->toBeEmpty();
});
