export default function HistorySkeleton({ rows = 5 }: { rows?: number }) {
    return (
        <ul
            aria-hidden
            className="divide-y divide-gray-100 overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm ring-1 ring-black/[0.02] dark:divide-gray-700/50 dark:border-gray-700/60 dark:bg-gray-800/50 dark:ring-white/[0.02]"
        >
            {Array.from({ length: rows }).map((_, index) => (
                <li
                    key={index}
                    className="flex items-center gap-3.5 px-4 py-3.5"
                >
                    <span className="h-9 w-9 shrink-0 animate-pulse rounded-full bg-gray-200 dark:bg-gray-700" />
                    <div className="min-w-0 flex-1 space-y-2">
                        <span className="block h-3.5 w-2/5 animate-pulse rounded bg-gray-200 dark:bg-gray-700" />
                        <span className="block h-3 w-3/5 animate-pulse rounded bg-gray-200/70 dark:bg-gray-700/70" />
                    </div>
                    <span className="h-6 w-20 shrink-0 animate-pulse rounded-full bg-gray-200 dark:bg-gray-700" />
                </li>
            ))}
        </ul>
    );
}
