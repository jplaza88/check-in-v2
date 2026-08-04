<?php

declare(strict_types=1);

namespace App\Account;

use App\Enums\HistoryPeriod;
use App\Enums\HistoryTab;
use App\Http\Requests\HistoryFilterRequest;
use Carbon\CarbonImmutable;

final readonly class HistoryFilters
{
    public function __construct(
        public HistoryTab $tab = HistoryTab::CheckIns,
        public HistoryPeriod $period = HistoryPeriod::AllTime,
    ) {}

    /**
     * Built only from validated input, so both properties are guaranteed to be
     * enum cases before they can select a query or a date bound.
     */
    public static function fromRequest(HistoryFilterRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            tab: HistoryTab::tryFrom((string) ($validated['tab'] ?? '')) ?? HistoryTab::CheckIns,
            period: HistoryPeriod::tryFrom((string) ($validated['period'] ?? '')) ?? HistoryPeriod::AllTime,
        );
    }

    /**
     * Null means unbounded, i.e. the "all time" preset.
     */
    public function since(): ?CarbonImmutable
    {
        return $this->period->since();
    }

    /**
     * @return array{tab: string, period: string}
     */
    public function toArray(): array
    {
        return [
            'tab' => $this->tab->value,
            'period' => $this->period->value,
        ];
    }
}
