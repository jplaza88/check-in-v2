<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\AppointmentScheduleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read int $location_id
 * @property-read int $day_of_week
 * @property-read string $open_time
 * @property-read string $close_time
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read Location $location
 */
#[Fillable(['location_id', 'day_of_week', 'open_time', 'close_time'])]
final class AppointmentSchedule extends Model
{
    /** @use HasFactory<AppointmentScheduleFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Location, $this>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    protected function casts(): array
    {
        return [
            'open_time' => 'string',
            'close_time' => 'string',
        ];
    }
}
