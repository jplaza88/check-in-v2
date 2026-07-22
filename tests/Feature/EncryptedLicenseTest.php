<?php

declare(strict_types=1);

use App\Models\CheckIn;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

it('round-trips the driver license number transparently', function (): void {
    $checkIn = CheckIn::factory()->create(['drivers_license_number' => 'D1234567']);

    expect($checkIn->fresh()->drivers_license_number)->toBe('D1234567');
});

it('stores the driver license number encrypted at rest', function (): void {
    $checkIn = CheckIn::factory()->create(['drivers_license_number' => 'D1234567']);

    $raw = DB::table('check_ins')->where('id', $checkIn->id)->value('drivers_license_number');

    expect($raw)
        ->not->toBe('D1234567')
        ->not->toContain('D1234567');

    // Plaintext queries can't match ciphertext (as expected for an encrypted column).
    expect(CheckIn::query()->where('drivers_license_number', 'D1234567')->exists())->toBeFalse();
});

it('uses the dedicated database key, not APP_KEY', function (): void {
    $checkIn = CheckIn::factory()->create(['drivers_license_number' => 'D1234567']);

    $raw = DB::table('check_ins')->where('id', $checkIn->id)->value('drivers_license_number');

    // The default (APP_KEY) encrypter must not be able to decrypt it.
    expect(fn (): string => Crypt::decryptString($raw))->toThrow(DecryptException::class);
});
