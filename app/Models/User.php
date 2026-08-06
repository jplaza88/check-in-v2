<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\Encrypted;
use App\Enums\Theme;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Override;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $email
 * @property-read CarbonImmutable|null $email_verified_at
 * @property-read string $password
 * @property-read string|null $cellphone
 * @property-read string|null $drivers_license_number
 * @property-read string|null $drivers_license_state
 * @property-read CarbonImmutable|null $drivers_license_expiration_date
 * @property-read string|null $locale
 * @property-read Theme|null $theme
 * @property-read int|null $location_id
 * @property-read Location|null $location
 * @property-read int|null $pending_check_in_id
 * @property-read int|null $pending_appointment_id
 * @property-read string|null $two_factor_secret
 * @property-read string|null $two_factor_recovery_codes
 * @property-read CarbonImmutable|null $two_factor_confirmed_at
 * @property-read string|null $remember_token
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read Collection<int, CheckIn> $checkIns
 * @property-read Collection<int, Appointment> $appointments
 */
#[Fillable(['name', 'email', 'password', 'cellphone', 'drivers_license_number', 'drivers_license_state', 'drivers_license_expiration_date', 'locale', 'theme'])]
#[Hidden(['password', 'remember_token', 'drivers_license_number', 'location_id'])]
final class User extends Authenticatable implements MustVerifyEmail
{
    use HasRoles;
    use Notifiable;

    /**
     * Defaults for the optional profile columns so a freshly-built model always
     * exposes them (as null) under strict attribute access, matching a row
     * loaded from the database.
     *
     * @var array<string, mixed>
     */
    #[Override]
    protected $attributes = [
        'cellphone' => null,
        'drivers_license_number' => null,
        'drivers_license_state' => null,
        'drivers_license_expiration_date' => null,
        'locale' => null,
        'theme' => null,
        'location_id' => null,
    ];

    /**
     * The location the driver books appointments at by default.
     *
     * Nullable, and the row it points at may since have been soft-deleted or had
     * appointments disabled, so callers must resolve it through
     * {@see \App\Queries\AppointmentLocations} rather than trusting the relation.
     *
     * @return BelongsTo<Location, $this>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * A null column means the driver has never chosen, which behaves the same as
     * explicitly following the operating system.
     */
    public function resolvedTheme(): Theme
    {
        return $this->theme ?? Theme::System;
    }

    /**
     * @return HasMany<CheckIn, $this>
     */
    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    /**
     * @return HasMany<Appointment, $this>
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Send the (queued) email verification notification in the driver's
     * current locale, captured now so the worker renders the right language.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify((new VerifyEmail)->locale(app()->getLocale()));
    }

    /**
     * Send the (queued) password reset notification in the driver's current
     * locale, captured now so the worker renders the right language.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token)->locale(app()->getLocale()));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'drivers_license_number' => Encrypted::class,
            'drivers_license_expiration_date' => 'date:Y-m-d',
            'theme' => Theme::class,
        ];
    }
}
