<?php

declare(strict_types=1);

use App\Locale\LocaleManager;
use App\Models\User;
use Illuminate\Http\Request;

function localeDriver(?string $locale = null): User
{
    $user = User::query()->create([
        'name' => 'John Driver',
        'email' => 'driver@example.com',
        'password' => 'secret-password',
    ]);

    if ($locale !== null) {
        $user->forceFill(['locale' => $locale])->save();
    }

    return $user;
}

function localeRequest(?string $acceptLanguage = null, ?User $user = null): Request
{
    $request = Request::create('/account/settings');

    if ($acceptLanguage !== null) {
        $request->headers->set('Accept-Language', $acceptLanguage);
    }

    if ($user instanceof User) {
        $request->setUserResolver(fn (): User => $user);
    }

    return $request;
}

it('prefers the account over everything else', function (): void {
    session(['locale' => 'fr']);

    $locale = resolve(LocaleManager::class)
        ->getLocale(localeRequest('es-MX,es', localeDriver('en')));

    expect($locale)->toBe('en');
});

it('falls back to the session when the account has no locale', function (): void {
    session(['locale' => 'fr']);

    $locale = resolve(LocaleManager::class)
        ->getLocale(localeRequest('es-MX,es', localeDriver()));

    expect($locale)->toBe('fr');
});

it('falls back to the browser when there is no account and no session', function (): void {
    $locale = resolve(LocaleManager::class)->getLocale(localeRequest('es-MX,es'));

    expect($locale)->toBe('es');
});

it('falls back to the configured locale when the browser offers nothing', function (): void {
    $locale = resolve(LocaleManager::class)->getLocale(localeRequest());

    expect($locale)->toBe(config('app.locale'));
});

/*
 * The browser sends whatever the operating system is set to, so an unsupported tag has to be rejected
 * rather than accepted and then quietly resolved against a lang/ directory that does not exist.
 */
it('ignores a browser language the app does not ship', function (): void {
    $locale = resolve(LocaleManager::class)->getLocale(localeRequest('de-DE,de'));

    expect($locale)->toBe(config('app.locale'));
});

it('ignores an unsupported locale stored on the account or in the session', function (): void {
    session(['locale' => 'de']);

    expect(resolve(LocaleManager::class)->getLocale(localeRequest()))
        ->toBe(config('app.locale'));

    expect(resolve(LocaleManager::class)->getLocale(localeRequest(null, localeDriver('de'))))
        ->toBe(config('app.locale'));
});

it('returns no translations for a locale that ships no messages file', function (): void {
    expect(resolve(LocaleManager::class)->getTranslationsForRoute('account.settings', 'de'))
        ->toBe([]);
});

it('loads only the keys mapped to the route', function (): void {
    $translations = resolve(LocaleManager::class)
        ->getTranslationsForRoute('account.settings', 'en');

    expect($translations)->toHaveKey('accountSettings')
        ->and($translations['accountSettings'])->toHaveKey('themeSystem')
        ->and($translations)->not->toHaveKey('checkInSelectLocation');
});
