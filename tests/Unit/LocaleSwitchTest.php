<?php

use Tests\TestCase;

uses(TestCase::class);

it('switches locale when locale is available', function () {
    $targetUrl = route('checkIn.selectLocation');

    $response = $this
        ->from($targetUrl)
        ->post(route('localeSwitch', ['locale' => 'es']));

    $response->assertRedirect($targetUrl);
    $response->assertSessionHas('locale', 'es');
});

it('does not switch locale when locale is not available', function () {
    $targetUrl = route('checkIn.selectLocation');

    $response = $this
        ->from($targetUrl)
        ->post(route('localeSwitch', ['locale' => 'de']));

    $response->assertNotFound();
    $response->assertSessionMissing('locale');
});
