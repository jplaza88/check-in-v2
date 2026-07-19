<?php

declare(strict_types=1);

use App\Auth\RegistrationGate;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\get;

function allowRegistration(): array
{
    return [RegistrationGate::SESSION_KEY => now()->addMinutes(5)->timestamp];
}

it('renders the register page while the registration window is open', function (): void {
    $this->withSession(allowRegistration())
        ->get(route('register'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->component('Auth/Register')
            ->has('translations.register.title')
            ->has('translations.register.createAccount'));
});

it('redirects to home when the registration window is not open', function (): void {
    get(route('register'))->assertRedirect(route('home'));
});

it('prefills the register form name from the carried profile', function (): void {
    $this->withSession([
        RegistrationGate::SESSION_KEY => now()->addMinutes(5)->timestamp,
        RegistrationGate::NAME_KEY => 'John Driver',
    ])
        ->get(route('register'))
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->component('Auth/Register')
            ->where('defaultName', 'John Driver'));
});

it('redirects to home when the registration window has expired', function (): void {
    $this->withSession([RegistrationGate::SESSION_KEY => now()->subMinute()->timestamp])
        ->get(route('register'))
        ->assertRedirect(route('home'));
});

it('redirects authenticated users away from the register page', function (): void {
    $this->actingAs(new User())
        ->withSession(allowRegistration())
        ->get(route('register'))
        ->assertRedirect();
});
