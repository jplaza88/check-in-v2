<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property int $state_id
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 * @property-read State $state
 */
class City extends Model {}
