<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property-read string $street1
 * @property-read string|null $street2
 * @property-read string $city
 * @property-read string $state
 * @property-read string $zip_code
 * @property-read string $zip_code_4
 * @property-read string $country
 * @property-read float|null $latitude
 * @property-read float|null $longitude
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read Location $location
 */
#[Fillable(['street1', 'street2', 'city', 'state', 'zip_code', 'zip_code_4', 'country', 'latitude', 'longitude'])]
class Address extends Model {}
