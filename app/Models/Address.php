<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $street1
 * @property string|null $street2
 * @property string $city
 * @property string $state
 * @property string $zip_code
 * @property string $zip_code_4
 * @property string $country
 * @property float|null $latitude
 * @property float|null $longitude
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read Location $location
 */
class Address extends Model
{
    protected $fillable = [
        'street1',
        'street2',
        'city',
        'state',
        'zip_code',
        'zip_code_4',
        'country',
        'latitude',
        'longitude',
    ];
}
