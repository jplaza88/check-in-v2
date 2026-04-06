<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $uuid
 * @property int $address_id
 * @property int $distance
 * @property string $name
 * @property string $abbreviation
 * @property string $timezone
 * @property string $phone
 * @property string|null $phone_ext
 * @property string $email
 * @property bool $is_active
 * @property bool $is_checkins_enabled
 * @property bool $is_appointments_enabled
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read Address $address
 * @property-read Schedule $schedule
 * @property-read Collection<int, ScheduleException> $scheduleExceptions
 */
class Location extends Model
{
    protected $fillable = [
        'uuid',
        'address_id',
        'distance',
        'name',
        'abbreviation',
        'timezone',
        'phone',
        'phone_ext',
        'email',
        'is_active',
        'is_checkins_enabled',
        'is_appointments_enabled',
    ];

    protected function casts(): array
    {
        return [
            'uuid' => 'string',
            'is_active' => 'boolean',
            'is_checkins_enabled' => 'boolean',
            'is_appointments_enabled' => 'boolean',
        ];
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function schedule(): HasOne
    {
        return $this->hasOne(Schedule::class);
    }
}
