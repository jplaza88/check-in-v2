<?php

declare(strict_types=1);

use App\Phone\PhoneFormatter;

it('formats a US E.164 number for readability', function (): void {
    $formatter = new PhoneFormatter;

    expect($formatter->format('+15551234567'))->toBe('+1 (555) 123-4567');
});

it('returns null when the number is null', function (): void {
    $formatter = new PhoneFormatter;

    expect($formatter->format(null))->toBeNull();
});

it('returns null when the number is an empty string', function (): void {
    $formatter = new PhoneFormatter;

    expect($formatter->format(''))->toBeNull();
});

it('leaves an international number untouched', function (): void {
    $formatter = new PhoneFormatter;

    expect($formatter->format('+442071234567'))->toBe('+442071234567');
});

it('leaves a partially entered number untouched', function (): void {
    $formatter = new PhoneFormatter;

    expect($formatter->format('555123'))->toBe('555123');
});
