<?php

declare(strict_types=1);

namespace App\Notifications;

use App\History\RecordNotification;
use App\Mail\CheckInCopyMail;
use App\Models\CheckIn;
use App\Models\User;
use App\Notifications\Channels\SmsChannel;
use App\Sms\SmsMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

/**
 * The driver's own confirmation of a check-in.
 *
 * A notification rather than a plain mailable because the driver chooses the
 * channel. The two channels carry deliberately different payloads: a text
 * cannot hold a PDF, so it carries the reference and a link to the record
 * instead.
 */
final class CheckInCopy extends Notification implements RecordNotification, ShouldQueue
{
    use SerializesModels;

    public function __construct(public CheckIn $checkIn) {}

    public function historyRecord(): CheckIn
    {
        return $this->checkIn;
    }

    public function historySubject(): string
    {
        return 'check-in-copy';
    }

    /**
     * @return list<string>
     */
    public function via(User $notifiable): array
    {
        return $notifiable->notificationChannels();
    }

    /**
     * Only the mail path renders a PDF, so a text-only driver never occupies
     * the dedicated Browsershot supervisor.
     *
     * @return array<string, string>
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'pdf',
            SmsChannel::class => 'default',
        ];
    }

    public function toMail(User $notifiable): Mailable
    {
        return new CheckInCopyMail($this->checkIn)->to($notifiable->email);
    }

    /**
     * The link points at the account history detail page, which
     * {@see \App\Account\HistoryRecordFinder} already scopes to the owning
     * driver. That is why no signed URL is involved: a signed URL would open
     * the record for anyone the text was forwarded to, where this 404s.
     *
     * It goes out shortened, via {@see \App\ShortLink\ShortLinkUrlGenerator},
     * because the account history path plus a uuid ran to about 87 characters
     * of a 160-character segment. Shortening changes the length and nothing
     * else: the code expands to this same auth-gated URL.
     */
    public function toSms(User $notifiable): SmsMessage
    {
        return new SmsMessage(
            to: (string) $notifiable->cellphone,
            body: __('messages.checkInCopySms.body', [
                'app' => (string) config('app.name'),
                'reference' => $this->checkIn->reference_number,
                'url' => $this->checkIn->shortUrl(),
            ]),
        );
    }
}
