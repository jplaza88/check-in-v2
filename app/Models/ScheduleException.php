<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $location_id
 * @property string $date
 * @property string|null $open
 * @property string|null $close
 * @property bool $is_closed
 * @property string|null $reason
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read Location $location
 */
class ScheduleException extends Model
{
    protected $fillable = [
        'location_id',
        'date',
        'open',
        'close',
        'is_closed',
        'reason',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_closed' => 'boolean',
        ];
    }
}
