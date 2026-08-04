import type { HistoryPeriodKey } from '@/hooks/useHistoryFilters';

interface Chip {
    key: HistoryPeriodKey;
    label: string;
}

export default function PeriodChips({
    active,
    chips,
    onChange,
}: {
    active: HistoryPeriodKey;
    chips: Chip[];
    onChange: (period: HistoryPeriodKey) => void;
}) {
    return (
        // Scrolls rather than wraps, so the row stays one line on a phone.
        <div className="-mx-6 flex [scrollbar-width:none] gap-2 overflow-x-auto px-6 pb-0.5 [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
            {chips.map((chip) => {
                const isActive = chip.key === active;

                return (
                    <button
                        key={chip.key}
                        type="button"
                        aria-pressed={isActive}
                        onClick={() => onChange(chip.key)}
                        className={`shrink-0 cursor-pointer rounded-full border px-3.5 py-1.5 text-xs font-semibold whitespace-nowrap transition-colors ${
                            isActive
                                ? 'border-brand-green bg-brand-green/10 text-brand-green'
                                : 'border-gray-200 text-gray-500 hover:text-brand-grey dark:border-gray-700/60 dark:text-gray-400 dark:hover:text-gray-200'
                        }`}
                    >
                        {chip.label}
                    </button>
                );
            })}
        </div>
    );
}
