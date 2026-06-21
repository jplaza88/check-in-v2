<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 * @property-read array<string, string>|null $config
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read CarbonImmutable|null $deleted_at
 * @property-read Address $address
 * @property-read Collection<int, CheckInSchedule> $checkinSchedule
 * @property-read Collection<int, CheckInScheduleOverride> $checkinScheduleOverrides
 * @property-read Collection<int, AppointmentSchedule> $appointmentSchedule
 * @property-read Collection<int, AppointmentScheduleOverride> $appointmentScheduleOverrides
 */
#[Fillable(['uuid', 'address_id', 'max_distance_allowed', 'name', 'abbreviation', 'timezone', 'phone', 'phone_ext', 'email', 'is_active', 'is_checkins_enabled', 'is_appointments_enabled', 'config'])]
#[Hidden(['id'])]
final class Location extends Model
{
    /** @use HasFactory<LocationFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * @return BelongsTo<Address, $this>
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * @return HasMany<CheckInSchedule, $this>
     */
    public function checkinSchedule(): HasMany
    {
        return $this->hasMany(CheckInSchedule::class);
    }

    /**
     * @return HasMany<AppointmentSchedule, $this>
     */
    public function appointmentSchedule(): HasMany
    {
        return $this->HasMany(AppointmentSchedule::class);
    }

    /**
     * @return HasMany<CheckInScheduleOverride, $this>
     */
    public function checkinScheduleOverrides(): HasMany
    {
        return $this->hasMany(CheckInScheduleOverride::class);
    }

    /**
     * @return HasMany<AppointmentScheduleOverride, $this>
     */
    public function appointmentScheduleOverrides(): HasMany
    {
        return $this->hasMany(AppointmentScheduleOverride::class);
    }

    /**
     * @return MorphMany<PurchaseOrder, $this>
     */
    public function purchaseOrders(): MorphMany
    {
        return $this->morphMany(PurchaseOrder::class, 'purchasable');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function appointmentMaxBookingDaysAhead(): int
    {
        $cap = (int) config('app.appointment_booking.max_days_ahead');

        return max(1, min((int) data_get($this->config, 'appointment.max_booking_days_ahead', $cap), $cap));
    }

    public function appointmentSlotIntervalMinutes(): int
    {
        $default = (int) config('app.appointment_booking.slot_interval_minutes.default');
        $allowed = config('app.appointment_booking.slot_interval_minutes.allowed');
        $value = (int) data_get($this->config, 'appointment.slot_interval_minutes', $default);

        return in_array($value, $allowed, true) ? $value : $default;
    }

    public function appointmentMinLeadTimeMinutes(): int
    {
        $default = (int) config('app.appointment_booking.lead_time_minutes.default');
        $max = (int) config('app.appointment_booking.lead_time_minutes.max');

        return max(0, min((int) data_get($this->config, 'appointment.min_lead_time_minutes', $default), $max));
    }

    public function appointmentBufferBeforeCloseMinutes(): int
    {
        $default = (int) config('app.appointment_booking.buffer_before_close_minutes.default');
        $max = (int) config('app.appointment_booking.buffer_before_close_minutes.max');

        return max(0, min((int) data_get($this->config, 'appointment.buffer_before_close_minutes', $default), $max));
    }

    protected function casts(): array
    {
        return [
            'uuid' => 'string',
            'is_active' => 'boolean',
            'is_checkins_enabled' => 'boolean',
            'is_appointments_enabled' => 'boolean',
            'config' => 'array',
        ];
    }
}
