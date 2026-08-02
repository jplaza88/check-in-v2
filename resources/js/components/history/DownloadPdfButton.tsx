import { FileDown } from 'lucide-react';

/**
 * Placeholder for the not-yet-built PDF export. Disabled and badged so it reads
 * as deliberate rather than broken, and stays out of the tab order.
 */
export default function DownloadPdfButton({
    label,
    badge,
}: {
    label: string;
    badge: string;
}) {
    return (
        <button
            type="button"
            disabled
            aria-disabled
            className="flex w-full cursor-not-allowed items-center justify-center gap-2 rounded-4xl border border-gray-200 bg-gray-50 px-5 py-2.5 text-sm font-semibold text-gray-400 dark:border-gray-700/60 dark:bg-gray-800/40 dark:text-gray-500"
        >
            <FileDown className="h-4 w-4" />
            {label}
            <span className="rounded-full bg-gray-200 px-2 py-0.5 text-[10px] font-bold tracking-wide uppercase dark:bg-gray-700 dark:text-gray-400">
                {badge}
            </span>
        </button>
    );
}
