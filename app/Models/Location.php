<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read int $id
 * @property-read string $uuid
 * @property-read int $address_id
 * @property-read int $max_distance_allowed
 * @property-read string $name
 * @property-read string $abbreviation
 * @property-read string $timezone
 * @property-read string $phone
 * @property-read string|null $phone_ext
 * @property-read string $email
 * @property-read float $latitude
 * @property-read float $longitude
 * @property-read bool $is_active
 * @property-read bool $is_checkins_enabled
 * @property-read bool $is_appointments_enabled
 * @property-read bool $additional_fields
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read Address $address
 * @property-read Schedule $schedule
 * @property-read ScheduleException $scheduleExceptions
 */
#[Fillable(['uuid', 'address_id', 'max_distance_allowed', 'name', 'abbreviation', 'timezone', 'phone', 'phone_ext', 'email', 'latitude', 'longitude', 'is_active', 'is_checkins_enabled', 'is_appointments_enabled', 'additional_fields'])]
#[Hidden(['id'])]
class Location extends Model
{
    /** @use HasFactory<LocationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'uuid' => 'string',
            'is_active' => 'boolean',
            'is_checkins_enabled' => 'boolean',
            'is_appointments_enabled' => 'boolean',
            'additional_fields' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * @return BelongsTo<Address, $this>
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * @return HasOne<Schedule, $this>
     */
    public function schedule(): HasOne
    {
        return $this->hasOne(Schedule::class);
    }

    /**
     * @return HasMany<ScheduleException, $this>
     */
    public function scheduleExceptions(): HasMany
    {
        return $this->hasMany(ScheduleException::class);
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
