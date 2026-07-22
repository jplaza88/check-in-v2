<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia;

it('renders the privacy policy page with public navigation', function (): void {
    $this->get(route('privacy'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->component('PrivacyPolicy')
            ->has('translations.publicNavigation.privacyPolicy'));
});

it('renders the terms of service page with public navigation', function (): void {
    $this->get(route('terms'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->component('TermsOfService')
            ->has('translations.publicNavigation.termsOfService'));
});
