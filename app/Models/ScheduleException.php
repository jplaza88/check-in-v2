<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ScheduleType;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read int $location_id
 * @property-read string $type
 * @property-read string $date
 * @property-read string|null $open
 * @property-read string|null $close
 * @property-read bool $is_closed
 * @property-read string|null $reason
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read Location $location
 */
#[Fillable(['location_id', 'type', 'date', 'open', 'close', 'is_closed', 'reason'])]
final class ScheduleException extends Model
{
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
            'is_closed' => 'boolean',
        ];
    }
}
