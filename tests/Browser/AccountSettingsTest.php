<?php

declare(strict_types=1);

use App\Models\User;

/**
 * The Feature test only asserts the Inertia payload. This one actually renders
 * the page, so a React crash in the new Radix controls, a missing translation
 * key or a bad prop shape surfaces here rather than in production.
 */
it('renders every settings section without console errors', function (): void {
    $driver = User::query()->create([
        'name' => 'John Driver',
        'email' => 'settings-browser@example.com',
        'password' => 'secret-password',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($driver);

    $page = visit('/account/settings');

    $page->assertSee('Settings')
        ->assertSee('Preferences')
        ->assertSee('Notifications')
        ->assertSee('Security')
        ->assertSee('Delete account')
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();
});
