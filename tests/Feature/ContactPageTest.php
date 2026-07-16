<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia;

it('renders the contact page with the configured phone and email', function (): void {
    config([
        'app.contact_phone' => '(800) 555-0100',
        'app.contact_email' => 'dispatch@martorifarms.com',
    ]);

    $this->get(route('contact'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->component('Contact')
            ->where('phone', '(800) 555-0100')
            ->where('email', 'dispatch@martorifarms.com')
            ->has('translations.contact.title'));
});
