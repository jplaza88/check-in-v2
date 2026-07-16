<?php

declare(strict_types=1);

use App\Models\Location;

it('renders the weekly schedule page without errors', function (): void {
    Location::factory()->create(['name' => 'Everglades Watermelons']);

    $page = visit(route('schedule'));

    $page->assertSee('Everglades Watermelons')
        ->assertNoJavascriptErrors();
});
