<?php

declare(strict_types=1);

use App\Models\User;
use App\Notifications\VerifyEmail;

function driver(): User
{
    return User::create([
        'name' => 'John Driver',
        'email' => 'john@example.com',
        'password' => 'secret-password',
    ]);
}

it('builds the branded verification mail', function (): void {
    $mail = (new VerifyEmail)->toMail(driver());

    expect($mail->view)->toBe('mail.verify-email')
        ->and($mail->subject)->toBe(__('messages.verifyEmail.subject'))
        ->and($mail->viewData['url'])->toContain('/email/verify/');
});

it('renders the logo, the personalized greeting, and the verification link', function (): void {
    $mail = (new VerifyEmail)->toMail(driver());

    $html = view($mail->view, $mail->viewData)->render();

    expect($html)
        ->toContain('logo.png')
        ->toContain('John Driver')
        ->toContain(__('messages.verifyEmail.button'))
        ->toContain(e($mail->viewData['url']));
});

it('localizes the verification email for the driver locale', function (): void {
    app()->setLocale('es');

    $mail = (new VerifyEmail)->toMail(driver());
    $html = view($mail->view, $mail->viewData)->render();

    expect($mail->subject)->toBe(__('messages.verifyEmail.subject', [], 'es'))
        ->and($html)->toContain(__('messages.verifyEmail.button', [], 'es'));
});
