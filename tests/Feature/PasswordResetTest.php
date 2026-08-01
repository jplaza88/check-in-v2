<?php

declare(strict_types=1);

use App\Models\User;
use App\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Sleep;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

function resetUser(): User
{
    return User::query()->create([
        'name' => 'John Driver',
        'email' => 'john@example.com',
        'password' => 'old-password-123',
    ]);
}

it('renders the forgot password page', function (): void {
    get('/forgot-password')
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->component('Auth/ForgotPassword'));
});

it('renders the reset password page with the token and email from the link', function (): void {
    get('/reset-password/the-token?email=john@example.com')
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->component('Auth/ResetPassword')
            ->where('token', 'the-token')
            ->where('email', 'john@example.com'));
});

it('builds a reset link that lands on our reset password route', function (): void {
    expect(route('password.reset', ['token' => 'abc123', 'email' => 'john@example.com']))
        ->toContain('/reset-password/abc123')
        ->toContain('email=john%40example.com');
});

it('emails a reset link through the Fortify endpoint', function (): void {
    Notification::fake();
    $user = resetUser();

    post('/forgot-password', ['email' => $user->email])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('status');

    Notification::assertSentTo($user, ResetPassword::class);
});

it('sends the reset email in the sender selected locale', function (): void {
    Notification::fake();
    $user = resetUser();

    $this->withSession(['locale' => 'es'])
        ->post('/forgot-password', ['email' => $user->email])
        ->assertSessionHasNoErrors();

    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        fn (ResetPassword $notification): bool => $notification->locale === 'es',
    );
});

it('answers an unknown email like a known one so accounts cannot be enumerated', function (): void {
    Notification::fake();

    // No such user: Fortify would normally return a validation error revealing
    // the address is unknown. We answer with the same generic success instead.
    post('/forgot-password', ['email' => 'nobody@example.com'])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('status');
});

it('throttles repeated forgot-password requests', function (): void {
    Notification::fake();

    foreach (range(1, 20) as $ignored) {
        post('/forgot-password', ['email' => 'nobody@example.com'])->assertStatus(302);
    }

    post('/forgot-password', ['email' => 'nobody@example.com'])->assertStatus(429);
});

it('holds the forgot-password response to a constant-time floor', function (): void {
    // 5s floor so our sleep is unmistakable next to the password broker's own
    // (smaller) internal timing-attack sleep.
    config(['fortify.auth_timebox_microseconds' => 5_000_000]);
    Sleep::fake();
    Notification::fake();

    post('/forgot-password', ['email' => 'nobody@example.com'])
        ->assertSessionHasNoErrors();

    Sleep::assertSlept(fn ($duration): bool => $duration->totalSeconds >= 4, 1);
});

it('builds the branded reset password mail', function (): void {
    $mail = (new ResetPassword('tok-123'))->toMail(resetUser());

    expect($mail->view)->toBe('mail.reset-password')
        ->and($mail->subject)->toBe(__('messages.resetPasswordEmail.subject'))
        ->and($mail->viewData['url'])->toContain('/reset-password/tok-123');
});

it('renders the logo, greeting, button, and reset link', function (): void {
    $mail = (new ResetPassword('tok-123'))->toMail(resetUser());

    $html = view($mail->view, $mail->viewData)->render();

    expect($html)
        ->toContain('logo.png')
        ->toContain('John Driver')
        ->toContain(__('messages.resetPasswordEmail.button'))
        ->toContain(e($mail->viewData['url']));
});

it('localizes the reset password mail for the driver locale', function (): void {
    app()->setLocale('es');

    $mail = (new ResetPassword('tok-123'))->toMail(resetUser());
    $html = view($mail->view, $mail->viewData)->render();

    expect($mail->subject)->toBe(__('messages.resetPasswordEmail.subject', [], 'es'))
        ->and($html)->toContain(__('messages.resetPasswordEmail.button', [], 'es'));
});

it('resets the password through the Fortify endpoint', function (): void {
    $user = resetUser();
    $token = Password::createToken($user);

    post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'brand-new-password-123',
        'password_confirmation' => 'brand-new-password-123',
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('login'))
        ->assertSessionHas('status');

    expect(Hash::check('brand-new-password-123', $user->refresh()->password))->toBeTrue();
});

it('passes the flashed status to the login screen so the banner can render', function (): void {
    $this->withSession(['status' => 'Your password has been reset.'])
        ->get('/login')
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->component('Auth/Login')
            ->where('status', 'Your password has been reset.'));
});
