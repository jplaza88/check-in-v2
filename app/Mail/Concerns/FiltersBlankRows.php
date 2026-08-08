<?php

declare(strict_types=1);

namespace App\Mail\Concerns;

/**
 * Drops summary rows that have no value.
 *
 * Most check-in fields are gated by per-location config and are frequently
 * null, so a body that rendered every row would be full of holes - the same
 * reason pdf.partials.row collapses. Filtered here rather than in the Blade so
 * $loop->last still lands on a row that renders, and so the two
 * shipping-department mails keep their existing output untouched.
 */
trait FiltersBlankRows
{
    /**
     * @param  array<int, array{label: string, value: mixed, sub?: string|null}>  $rows
     * @return array<int, array{label: string, value: mixed, sub?: string|null}>
     */
    protected function presentRows(array $rows): array
    {
        return array_values(array_filter(
            $rows,
            static fn (array $row): bool => filled($row['value']),
        ));
    }
}
