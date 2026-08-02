import { router } from '@inertiajs/react';
import { useCallback, useState } from 'react';

import { history as historyRoute } from '@/routes/account';

export type HistoryTabKey = 'check-ins' | 'appointments';
export type HistoryPeriodKey = '30d' | '90d' | '12m' | 'all';

interface Options {
    tab: HistoryTabKey;
    period: HistoryPeriodKey;
    currentPage: number;
    hasMore: boolean;
}

/**
 * Owns the Inertia merge-prop contract for the history list. The flags below are
 * load-bearing and easy to get wrong, so no page code should set them directly.
 */
export function useHistoryFilters({
    tab,
    period,
    currentPage,
    hasMore,
}: Options) {
    const [loadingMore, setLoadingMore] = useState(false);
    const [switching, setSwitching] = useState(false);

    const applyFilters = useCallback(
        (next: { tab?: HistoryTabKey; period?: HistoryPeriodKey }) => {
            router.get(
                historyRoute.url(),
                // Never send `page` here. Load-more keeps it out of the URL via
                // preserveUrl, so it cannot be smuggled back in by the
                // query-string merge and strand the user mid-list.
                { tab: next.tab ?? tab, period: next.period ?? period },
                {
                    only: ['history', 'filters'],
                    // Tells the server to omit mergeProps for this path, so the
                    // list is replaced instead of appended. Without it, changing
                    // a filter concatenates onto the existing rows.
                    reset: ['history'],
                    // Inertia defaults preserveState to false for GET; without
                    // this the page remounts and loses local state.
                    preserveState: true,
                    preserveScroll: true,
                    replace: true,
                    onStart: () => setSwitching(true),
                    onFinish: () => setSwitching(false),
                },
            );
        },
        [tab, period],
    );

    const loadMore = useCallback(() => {
        if (loadingMore || !hasMore) {
            return;
        }

        setLoadingMore(true);

        router.reload({
            // Required. Merge props only apply to partial reloads; without
            // `only` the rows are replaced rather than appended.
            only: ['history'],
            data: { page: currentPage + 1 },
            // Keeps ?page= out of the address bar so a refresh or shared link
            // never renders an orphaned page 3.
            preserveUrl: true,
            onFinish: () => setLoadingMore(false),
        });
    }, [loadingMore, hasMore, currentPage]);

    return { loadingMore, switching, applyFilters, loadMore };
}
