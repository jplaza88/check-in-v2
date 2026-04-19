<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ScheduleType;
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
#[Fillable(['uuid', 'address_id', 'max_distance_allowed', 'name', 'abbreviation', 'timezone', 'phone', 'phone_ext', 'email', 'is_active', 'is_checkins_enabled', 'is_appointments_enabled', 'additional_fields'])]
#[Hidden(['id'])]
final class Location extends Model
{
    /** @use HasFactory<LocationFactory> */
    use HasFactory;

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
    public function checkInSchedule(): HasOne
    {
        return $this->hasOne(Schedule::class)->where('type', ScheduleType::CheckIn);
    }

    /**
     * @return HasOne<Schedule, $this>
     */
    public function appointmentSchedule(): HasOne
    {
        return $this->hasOne(Schedule::class)->where('type', ScheduleType::Appointment);
    }

    /**
     * @return HasMany<ScheduleException, $this>
     */
    public function checkInScheduleExceptions(): HasMany
    {
        return $this->hasMany(ScheduleException::class)->where('type', ScheduleType::CheckIn);
    }

    /**
     * @return HasMany<ScheduleException, $this>
     */
    public function appointmentScheduleExceptions(): HasMany
    {
        return $this->hasMany(ScheduleException::class)->where('type', ScheduleType::Appointment);
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected function casts(): array
    {
        return [
            'uuid' => 'string',
            'is_active' => 'boolean',
            'is_checkins_enabled' => 'boolean',
            'is_appointments_enabled' => 'boolean',
            'additional_fields' => 'boolean',
        ];
    }
}
