<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\AppointmentScheduleOverrideFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 * @property-read int $location_id
 * @property-read Carbon $override_date
 * @property-read string|null $open_time
 * @property-read string|null $close_time
 * @property-read bool $is_closed
 * @property-read string $reason
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read Location $location
 */
#[Fillable(['location_id', 'override_date', 'open_time', 'close_time', 'is_closed', 'reason'])]
final class AppointmentScheduleOverride extends Model
{
    /** @use HasFactory<AppointmentScheduleOverrideFactory> */
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
            'override_date' => 'date',
            'is_closed' => 'boolean',
        ];
    }
}
