<?php

declare(strict_types=1);

use App\Models\User;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('renders the login page for guests', function (): void {
    get(route('login'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->component('Auth/Login')
            ->has('translations.login.title')
            ->has('translations.login.signIn'));
});

it('redirects authenticated users away from the login page', function (): void {
    $this->actingAs(new User())
        ->get(route('login'))
        ->assertRedirect();
});

it('sends a driver to their account after logging in', function (): void {
    User::query()->create([
        'name' => 'John Driver',
        'email' => 'john@example.com',
        'password' => 'Password123!',
    ]);

    post('/login', [
        'email' => 'john@example.com',
        'password' => 'Password123!',
    ])->assertRedirect(route('account'));
});
