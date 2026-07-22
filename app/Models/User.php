<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\Encrypted;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'cellphone', 'drivers_license_number', 'drivers_license_state', 'drivers_license_expiration_date'])]
#[Hidden(['password', 'remember_token', 'drivers_license_number'])]
final class User extends Authenticatable implements MustVerifyEmail
{
    use HasRoles;
    use Notifiable;

    /**
     * Defaults for the optional license columns so a freshly-built model always
     * exposes them (as null) under strict attribute access, matching a row
     * loaded from the database.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'drivers_license_number' => null,
        'drivers_license_state' => null,
        'drivers_license_expiration_date' => null,
    ];

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
        $this->notify((new ResetPassword($token))->locale(app()->getLocale()));
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
        ];
    }
}
