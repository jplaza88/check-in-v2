import type { ReactNode } from 'react';

export default function HistoryList({ children }: { children: ReactNode }) {
    return (
        <ul className="divide-y divide-gray-100 overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm ring-1 ring-black/[0.02] dark:divide-gray-700/50 dark:border-gray-700/60 dark:bg-gray-800/50 dark:ring-white/[0.02]">
            {children}
        </ul>
    );
}

export function HistoryDivider({ label }: { label: string }) {
    return (
        <li className="bg-gray-50/80 px-4 py-1.5 text-[10px] font-bold tracking-widest text-gray-400 uppercase dark:bg-gray-800/60 dark:text-gray-500">
            {label}
        </li>
    );
}
