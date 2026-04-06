<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $location_id
 * @property string|null $sunday_open
 * @property string|null $sunday_close
 * @property string|null $monday_open
 * @property string|null $monday_close
 * @property string|null $tuesday_open
 * @property string|null $tuesday_close
 * @property string|null $wednesday_open
 * @property string|null $wednesday_close
 * @property string|null $thursday_open
 * @property string|null $thursday_close
 * @property string|null $friday_open
 * @property string|null $friday_close
 * @property string|null $saturday_open
 * @property string|null $saturday_close
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read Location $location
 */
class Schedule extends Model
{
    protected $fillable = [
        'location_id',
        'sunday_open',
        'sunday_close',
        'monday_open',
        'monday_close',
        'tuesday_open',
        'tuesday_close',
        'wednesday_open',
        'wednesday_close',
        'thursday_open',
        'thursday_close',
        'friday_open',
        'friday_close',
        'saturday_open',
        'saturday_close',
    ];

    protected function casts(): array
    {
        return [
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

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
