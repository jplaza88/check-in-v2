<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read int $location_id
 * @property-read string $date
 * @property-read string|null $open
 * @property-read string|null $close
 * @property-read bool $is_closed
 * @property-read string|null $reason
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read Location $location
 */
#[Fillable(['location_id', 'date', 'open', 'close', 'is_closed', 'reason'])]
class ScheduleException extends Model
{
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    protected function casts(): array
    {
        return [
            'is_closed' => 'boolean',
        ];
    }
}
