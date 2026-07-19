<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * The framework's email-verification notification, queued so it never blocks
 * the request that triggered it (registration or an email change), rendered
 * with the branded resources/views/mail/verify-email.blade.php template.
 */
final class VerifyEmail extends BaseVerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * @param  User  $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('messages.verifyEmail.subject'))
            ->view('mail.verify-email', [
                'url' => $this->verificationUrl($notifiable),
                'name' => $notifiable->name,
                'expiresIn' => (int) config('auth.verification.expire', 60),
            ]);
    }
}
