<?php

declare(strict_types=1);

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Database\Eloquent\Model;

/**
 * Encrypts a string attribute at rest using the dedicated database encrypter
 * (keyed by DB_ENCRYPTION_KEY), independent of APP_KEY.
 *
 * @implements CastsAttributes<string|null, string|null>
 */
final class Encrypted implements CastsAttributes
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return $this->encrypter()->decryptString($value);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return $this->encrypter()->encryptString((string) $value);
    }

    private function encrypter(): Encrypter
    {
        return app('db.encrypter');
    }
}
