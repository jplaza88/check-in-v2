import type { HistoryTabKey } from '@/hooks/useHistoryFilters';

interface Tab {
    key: HistoryTabKey;
    label: string;
}

export default function HistoryTabs({
    active,
    tabs,
    onChange,
}: {
    active: HistoryTabKey;
    tabs: Tab[];
    onChange: (tab: HistoryTabKey) => void;
}) {
    return (
        <div
            role="tablist"
            className="flex gap-1 rounded-2xl bg-gray-100/80 p-1 dark:bg-gray-800/50"
        >
            {tabs.map((tab) => {
                const isActive = tab.key === active;

                return (
                    <button
                        key={tab.key}
                        type="button"
                        role="tab"
                        aria-selected={isActive}
                        onClick={() => onChange(tab.key)}
                        className={`min-w-0 flex-1 cursor-pointer truncate rounded-xl px-3 py-2 text-sm font-semibold transition-colors ${
                            isActive
                                ? 'bg-brand-green text-white shadow-sm shadow-brand-green/25'
                                : 'text-gray-500 hover:text-brand-grey dark:text-gray-400 dark:hover:text-gray-200'
                        }`}
                    >
                        {tab.label}
                    </button>
                );
            })}
        </div>
    );
}
