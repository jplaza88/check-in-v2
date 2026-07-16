<?php

declare(strict_types=1);

namespace App\Queries;

use Illuminate\Support\Facades\DB;

final readonly class SearchStates
{
    /**
     * Case-insensitive prefix search across states, joined to their country.
     *
     * @return array<int, array{state: string, stateCode: string, country: string, countryCode: string}>
     */
    public function execute(string $query): array
    {
        // Escape LIKE wildcards so a stray % / _ can't broaden the match.
        $needle = mb_strtolower(str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $query)).'%';

        return DB::table('states')
            ->join('countries', 'states.country_id', '=', 'countries.id')
            ->whereRaw('LOWER(states.name) LIKE ?', [$needle])
            ->orderBy('states.name')
            ->limit(8)
            ->get([
                'states.name as state',
                'states.short_name as stateCode',
                'countries.name as country',
                'countries.short_name as countryCode',
            ])
            ->map(fn (object $row): array => [
                'state' => $row->state,
                'stateCode' => $row->stateCode,
                'country' => $row->country,
                'countryCode' => $row->countryCode,
            ])
            ->all();
    }
}
