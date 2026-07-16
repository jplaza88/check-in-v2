<?php

declare(strict_types=1);

it('renders the contact page without errors', function (): void {
    $page = visit(route('contact'));

    $page->assertSee('Contact us')
        ->assertNoJavascriptErrors();
});
