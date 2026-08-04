<?php

declare(strict_types=1);

use App\Account\HistoryFilters;
use App\Enums\HistoryPeriod;
use App\Enums\HistoryTab;
use App\Http\Requests\HistoryFilterRequest;
use Illuminate\Support\Facades\Date;

function filtersFromQuery(array $query): HistoryFilters
{
    $request = HistoryFilterRequest::create('/account/history', 'GET', $query);
    $request->setContainer(app())->validateResolved();

    return HistoryFilters::fromRequest($request);
}

it('defaults to the check-ins tab over all time', function (): void {
    $filters = filtersFromQuery([]);

    expect($filters->tab)->toBe(HistoryTab::CheckIns)
        ->and($filters->period)->toBe(HistoryPeriod::AllTime)
        ->and($filters->since())->toBeNull();
});

it('reads the tab and period from validated input', function (): void {
    $filters = filtersFromQuery(['tab' => 'appointments', 'period' => '90d']);

    expect($filters->tab)->toBe(HistoryTab::Appointments)
        ->and($filters->period)->toBe(HistoryPeriod::NinetyDays)
        ->and($filters->toArray())->toBe(['tab' => 'appointments', 'period' => '90d']);
});

it('maps each period preset to its lower bound', function (HistoryPeriod $period, ?int $daysAgo): void {
    $since = $period->since();

    if ($daysAgo === null) {
        expect($since)->toBeNull();

        return;
    }

    expect($since)->not->toBeNull()
        ->and($since->equalTo(Date::now()->subDays($daysAgo)))->toBeTrue();
})->with([
    'thirty days' => [HistoryPeriod::ThirtyDays, 30],
    'ninety days' => [HistoryPeriod::NinetyDays, 90],
    'twelve months' => [HistoryPeriod::TwelveMonths, 365],
    'all time' => [HistoryPeriod::AllTime, null],
]);
