import type { ReactNode } from 'react';

export function DetailSection({
    heading,
    children,
}: {
    heading: string;
    children: ReactNode;
}) {
    return (
        <section>
            <h2 className="mb-2 px-1 text-xs font-bold tracking-widest text-brand-green uppercase">
                {heading}
            </h2>
            <dl className="divide-y divide-gray-100 overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm ring-1 ring-black/[0.02] dark:divide-gray-700/50 dark:border-gray-700/60 dark:bg-gray-800/50 dark:ring-white/[0.02]">
                {children}
            </dl>
        </section>
    );
}

export function DetailRow({
    label,
    value,
}: {
    label: string;
    value: ReactNode;
}) {
    if (value === null || value === undefined || value === '') {
        return null;
    }

    return (
        <div className="flex items-start justify-between gap-4 px-4 py-3">
            <dt className="shrink-0 text-sm text-gray-500 dark:text-gray-400">
                {label}
            </dt>
            <dd className="min-w-0 text-right text-sm font-semibold break-words text-brand-grey dark:text-gray-100">
                {value}
            </dd>
        </div>
    );
}
