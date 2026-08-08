<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;

function guestLocaleDriver(?string $locale = null): User
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

/*
 * A driver who switches to Spanish partway through a guest check-in and then registers should not be
 * dropped back into English. Fortify fires Login on registration too, so this covers both paths.
 */
it('carries a locale chosen while signed out onto the account', function (): void {
    $driver = guestLocaleDriver();
    session(['locale' => 'es']);

    Event::dispatch(new Login('web', $driver, false));

    expect($driver->refresh()->locale)->toBe('es');
});

it('does not overwrite a locale the account already states', function (): void {
    $driver = guestLocaleDriver('fr');
    session(['locale' => 'es']);

    Event::dispatch(new Login('web', $driver, false));

    expect($driver->refresh()->locale)->toBe('fr');
});

it('does nothing when the session holds no locale', function (): void {
    $driver = guestLocaleDriver();

    Event::dispatch(new Login('web', $driver, false));

    expect($driver->refresh()->locale)->toBeNull();
});

it('refuses to adopt a locale the app does not ship', function (): void {
    $driver = guestLocaleDriver();
    session(['locale' => 'de']);

    Event::dispatch(new Login('web', $driver, false));

    expect($driver->refresh()->locale)->toBeNull();
});
