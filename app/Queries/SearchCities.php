<?php

declare(strict_types=1);

namespace App\Queries;

use Illuminate\Support\Facades\DB;

final readonly class SearchCities
{
    /**
     * Case-insensitive prefix search across cities, joined to their state and
     * country. LOWER(...) LIKE keeps it portable across Postgres (prod) and the
     * SQLite test database.
     *
     * @return list<array{city: string, state: string, stateCode: ?string, country: string, countryCode: string}>
     */
    public function execute(string $query): array
    {
        // Escape LIKE wildcards so a stray % / _ can't broaden the match.
        $needle = mb_strtolower(str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $query)).'%';

        return DB::table('cities')
            ->join('states', 'cities.state_id', '=', 'states.id')
            ->join('countries', 'states.country_id', '=', 'countries.id')
            ->whereRaw('LOWER(cities.name) LIKE ?', [$needle])
            ->orderBy('cities.name')
            ->limit(8)
            ->get([
                'cities.name as city',
                'states.name as state',
                'states.short_name as stateCode',
                'countries.name as country',
                'countries.short_name as countryCode',
            ])
            ->map(fn (object $row): array => (array) $row)
            ->all();
    }
}
