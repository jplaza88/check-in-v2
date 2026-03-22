<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class State extends Model
{
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
