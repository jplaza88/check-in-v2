<?php

declare(strict_types=1);

use App\Mail\RecordDocumentMail;
use App\Models\CheckIn;
use App\Models\Location;
use App\Models\User;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;

/**
 * Renders a branded mail view and returns the absolute URL of its <img> source.
 *
 * @param  array<string, mixed>  $viewData
 */
function mailLogoUrl(string $view, array $viewData): string
{
    $html = view($view, $viewData)->render();

    expect($html)->toMatch('/<img [^>]*src="[^"]+"/');

    preg_match('/<img [^>]*src="([^"]+)"/', $html, $matches);

    return html_entity_decode($matches[1]);
}

function mailRecipient(): User
{
    return User::query()->create([
        'name' => 'Jane Driver',
        'email' => 'jane@example.com',
        'password' => 'secret-password',
    ]);
}

/**
 * Mail clients fetch the header logo over HTTP, so the file has to be part of
 * the repository and therefore part of the deployed image. It was previously
 * untracked, which left it present locally (bind mounted by Sail) but absent
 * from the CI-built image, so every branded email rendered a broken image.
 */
it('ships the logo the mail templates point at', function (): void {
    expect(public_path('logo.png'))->toBeReadableFile();

    $dimensions = getimagesize(public_path('logo.png'));

    expect($dimensions)->not->toBeFalse()
        ->and($dimensions[2])->toBe(IMAGETYPE_PNG);
});

it('points the branded emails at a logo that exists on disk', function (string $view, array $viewData): void {
    $url = mailLogoUrl($view, $viewData);

    expect($url)->toStartWith(mb_rtrim((string) config('app.url'), '/'));

    $path = public_path(mb_ltrim((string) parse_url($url, PHP_URL_PATH), '/'));

    expect($path)->toBeReadableFile();
})->with([
    'verify email' => fn (): array => [
        'mail.verify-email',
        (new VerifyEmail)->toMail(mailRecipient())->viewData,
    ],
    'reset password' => fn (): array => [
        'mail.reset-password',
        new ResetPassword('token')->toMail(mailRecipient())->viewData,
    ],
    // Shared by RecordDocumentMail, AppointmentBooked and CheckInCompleted, so
    // one case covers the brand header on all three.
    'record document' => fn (): array => [
        'mail.notification',
        new RecordDocumentMail(
            CheckIn::factory()->atLocation(Location::factory()->create())->create(),
        )->content()->with,
    ],
]);
