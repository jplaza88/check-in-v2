import { Loader2 } from 'lucide-react';

export default function LoadMoreButton({
    hasMore,
    loading,
    isEmpty,
    labels,
    onClick,
}: {
    hasMore: boolean;
    loading: boolean;
    isEmpty: boolean;
    labels: { loadMore: string; loading: string; endOfList: string };
    onClick: () => void;
}) {
    if (!hasMore) {
        if (isEmpty) {
            return null;
        }

        return (
            <p className="pt-1 text-center text-xs text-gray-400 dark:text-gray-500">
                {labels.endOfList}
            </p>
        );
    }

    return (
        <button
            type="button"
            onClick={onClick}
            disabled={loading}
            className="flex w-full cursor-pointer items-center justify-center gap-2 rounded-4xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-brand-grey transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-700/60 dark:bg-gray-800/50 dark:text-gray-200 dark:hover:bg-gray-800"
        >
            {loading && <Loader2 className="h-4 w-4 animate-spin" />}
            {loading ? labels.loading : labels.loadMore}
        </button>
    );
}
