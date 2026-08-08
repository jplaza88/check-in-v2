<?php

declare(strict_types=1);

use App\Models\User;

it('switches locale when locale is available', function (): void {
    $targetUrl = route('checkIn.selectLocation');

    $response = $this
        ->from($targetUrl)
        ->post(route('localeSwitch', ['locale' => 'es']));

    $response->assertRedirect($targetUrl);
    $response->assertSessionHas('locale', 'es');
});

it('does not switch locale when locale is not available', function (): void {
    $targetUrl = route('checkIn.selectLocation');

    $response = $this
        ->from($targetUrl)
        ->post(route('localeSwitch', ['locale' => 'de']));

    $response->assertNotFound();
    $response->assertSessionMissing('locale');
});

it('persists the locale onto the account so it follows the driver to other devices', function (): void {
    $driver = User::query()->create([
        'name' => 'John Driver',
        'email' => 'driver@example.com',
        'password' => 'secret-password',
    ]);

    $this->actingAs($driver)
        ->from(route('account.settings'))
        ->post(route('localeSwitch', ['locale' => 'fr']))
        ->assertSessionHas('locale', 'fr');

    expect($driver->refresh()->locale)->toBe('fr');
});

it('leaves guests on the session alone, with nothing to persist to', function (): void {
    $this->from(route('checkIn.selectLocation'))
        ->post(route('localeSwitch', ['locale' => 'es']))
        ->assertSessionHas('locale', 'es');

    expect(User::query()->count())->toBe(0);
});
