<?php

declare(strict_types=1);

namespace App\Notifications;

use App\History\RecordNotification;
use App\Mail\AppointmentCopyMail;
use App\Models\Appointment;
use App\Models\User;
use App\Notifications\Channels\SmsChannel;
use App\Sms\SmsMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

/**
 * The driver's own confirmation of a booking. See {@see CheckInCopy} for why
 * this is a notification and why the text carries no signed URL.
 */
final class AppointmentCopy extends Notification implements RecordNotification, ShouldQueue
{
    use SerializesModels;

    public function __construct(public Appointment $appointment) {}

    public function historyRecord(): Appointment
    {
        return $this->appointment;
    }

    public function historySubject(): string
    {
        return 'appointment-copy';
    }

    /**
     * @return list<string>
     */
    public function via(User $notifiable): array
    {
        return $notifiable->notificationChannels();
    }

    /**
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
        return new AppointmentCopyMail($this->appointment)->to($notifiable->email);
    }

    /** Auth-gated and shortened for the same reasons as {@see CheckInCopy::toSms()}. */
    public function toSms(User $notifiable): SmsMessage
    {
        return new SmsMessage(
            to: (string) $notifiable->cellphone,
            body: __('messages.appointmentCopySms.body', [
                'app' => (string) config('app.name'),
                'reference' => $this->appointment->reference_number,
                'url' => $this->appointment->shortUrl(),
            ]),
        );
    }
}
