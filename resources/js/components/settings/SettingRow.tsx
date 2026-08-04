import type { ComponentType, ReactNode } from 'react';

/**
 * One settings row.
 *
 * Type matches the rest of the account pages (text-sm label, text-xs
 * description) so this page does not read as a different app. Row height stays
 * generous for gloved thumbs, which is where the touch-target margin lives.
 */
export default function SettingRow({
    icon: Icon,
    label,
    description,
    control,
    badge,
    disabled = false,
    stacked = false,
}: {
    icon?: ComponentType<{ className?: string }>;
    label: string;
    description?: string;
    control?: ReactNode;
    badge?: string;
    disabled?: boolean;
    stacked?: boolean;
}) {
    return (
        <div
            className={`flex min-h-13 gap-3 px-6 py-3.5 ${
                stacked ? 'flex-col' : 'items-center'
            } ${disabled ? 'opacity-60' : ''}`}
        >
            <div
                className={`flex items-center gap-3 ${stacked ? '' : 'min-w-0 flex-1'}`}
            >
                {Icon && (
                    <Icon className="h-5 w-5 shrink-0 text-gray-400 dark:text-gray-500" />
                )}
                <div className="min-w-0 flex-1">
                    <div className="flex flex-wrap items-center gap-2">
                        <span className="text-sm font-semibold text-brand-grey dark:text-gray-100">
                            {label}
                        </span>
                        {badge && (
                            <span className="rounded-full bg-gray-200 px-2 py-0.5 text-[10px] font-bold tracking-wide text-gray-500 uppercase dark:bg-gray-700 dark:text-gray-400">
                                {badge}
                            </span>
                        )}
                    </div>
                    {description && (
                        <p className="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                            {description}
                        </p>
                    )}
                </div>
            </div>

            {control && (
                <div className={stacked ? 'w-full' : 'shrink-0'}>{control}</div>
            )}
        </div>
    );
}
