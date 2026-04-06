<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read string|null $short_name
 * @property-read string $name
 * @property-read int $country_id
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read Country $country
 * @property-read Collection<int, City> $cities
 */
#[Fillable(['short_name', 'name', 'country_id'])]
class State extends Model
{
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
