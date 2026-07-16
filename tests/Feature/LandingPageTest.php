<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia;

it('renders the landing page with translated hero copy and public navigation', function (): void {
    $this->get(route('home'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->component('Home')
            ->has('translations.home.headingLine2')
            ->has('translations.home.checkInCardCta')
            ->has('translations.publicNavigation.checkIn'));
});

it('exposes the deployed commit as a short 7-char sha on the landing page', function (): void {
    config()->set('app.commit', 'a1b2c3d4e5f6a7b8c9d0');

    $this->get(route('home'))
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->where('commit', 'a1b2c3d'));
});

it('exposes a null commit when none is baked into the build', function (): void {
    config()->set('app.commit');

    $this->get(route('home'))
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->where('commit', null));
});
