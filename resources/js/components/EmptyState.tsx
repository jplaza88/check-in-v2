import type { ComponentType, ReactNode } from 'react';

export default function EmptyState({
    icon: Icon,
    title,
    subtitle,
    action,
    tone = 'neutral',
}: {
    icon: ComponentType<{ className?: string }>;
    title: string;
    subtitle?: string;
    action?: ReactNode;
    tone?: 'neutral' | 'brand';
}) {
    const iconTone =
        tone === 'brand'
            ? 'bg-brand-green/10 text-brand-green'
            : 'bg-gray-100 text-gray-400 dark:bg-gray-700/60 dark:text-gray-500';

    return (
        <div className="flex flex-col items-center gap-1.5 rounded-2xl border border-dashed border-gray-200 bg-gray-50/70 px-6 py-10 text-center dark:border-gray-700/60 dark:bg-gray-800/30">
            <span
                className={`mb-1 flex h-12 w-12 items-center justify-center rounded-full ${iconTone}`}
            >
                <Icon className="h-6 w-6" />
            </span>
            <p className="text-sm font-semibold text-brand-grey dark:text-gray-200">
                {title}
            </p>
            {subtitle && (
                <p className="max-w-xs text-xs text-gray-500 dark:text-gray-400">
                    {subtitle}
                </p>
            )}
            {action && <div className="mt-3">{action}</div>}
        </div>
    );
}
