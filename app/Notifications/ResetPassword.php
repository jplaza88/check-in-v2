<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * The framework's password-reset notification, queued so it never blocks the
 * request that triggered it, rendered with the branded
 * resources/views/mail/reset-password.blade.php template.
 */
final class ResetPassword extends BaseResetPassword implements ShouldQueue
{
    use Queueable;

    /**
     * @param  User  $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('messages.resetPasswordEmail.subject'))
            ->view('mail.reset-password', [
                'url' => $this->resetUrl($notifiable),
                'name' => $notifiable->name,
                'expiresIn' => (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60),
            ]);
    }
}
