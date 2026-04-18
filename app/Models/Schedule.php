<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ScheduleType;
use Carbon\CarbonImmutable;
use Database\Factories\ScheduleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read int $location_id
 * @property-read string $type
 * @property-read string|null $sunday_open
 * @property-read string|null $sunday_close
 * @property-read string|null $monday_open
 * @property-read string|null $monday_close
 * @property-read string|null $tuesday_open
 * @property-read string|null $tuesday_close
 * @property-read string|null $wednesday_open
 * @property-read string|null $wednesday_close
 * @property-read string|null $thursday_open
 * @property-read string|null $thursday_close
 * @property-read string|null $friday_open
 * @property-read string|null $friday_close
 * @property-read string|null $saturday_open
 * @property-read string|null $saturday_close
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read Location $location
 */
#[Fillable(['location_id', 'type', 'sunday_open', 'sunday_close', 'monday_open', 'monday_close', 'tuesday_open', 'tuesday_close', 'wednesday_open', 'wednesday_close', 'thursday_open', 'thursday_close', 'friday_open', 'friday_close', 'saturday_open', 'saturday_close'])]
final class Schedule extends Model
{
    /** @use HasFactory<ScheduleFactory> */
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
            'type' => ScheduleType::class,
            'sunday_open' => 'string',
            'sunday_close' => 'string',
            'monday_open' => 'string',
            'monday_close' => 'string',
            'tuesday_open' => 'string',
            'tuesday_close' => 'string',
            'wednesday_open' => 'string',
            'wednesday_close' => 'string',
            'thursday_open' => 'string',
            'thursday_close' => 'string',
            'friday_open' => 'string',
            'friday_close' => 'string',
            'saturday_open' => 'string',
            'saturday_close' => 'string',
        ];
    }
}
