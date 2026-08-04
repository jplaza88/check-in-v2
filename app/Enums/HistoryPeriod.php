<?php

declare(strict_types=1);

namespace App\Enums;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;

enum HistoryPeriod: string
{
    case ThirtyDays = '30d';
    case NinetyDays = '90d';
    case TwelveMonths = '12m';
    case AllTime = 'all';

    /**
     * The lower bound for this preset, or null when unbounded.
     *
     * Deliberately a lower bound only: a future-dated appointment must stay
     * visible whichever preset is selected.
     */
    public function since(): ?CarbonImmutable
    {
        $now = Date::now();

        return match ($this) {
            self::ThirtyDays => $now->subDays(30),
            self::NinetyDays => $now->subDays(90),
            self::TwelveMonths => $now->subMonths(12),
            self::AllTime => null,
        };
    }
}
