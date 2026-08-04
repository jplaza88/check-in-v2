import type { FormEvent, ReactNode } from 'react';

const SHELL =
    'border-gray-200/80 bg-white shadow-sm ring-1 ring-black/[0.02] dark:border-gray-700/60 dark:bg-gray-800/50 dark:ring-white/[0.02]';

const DESTRUCTIVE_SHELL =
    'border-destructive/25 bg-white shadow-sm ring-1 ring-black/[0.02] dark:bg-gray-800/50 dark:ring-white/[0.02]';

/**
 * The account-area card.
 *
 * Renders a <form> when given onSubmit, otherwise a <section>, so sections made
 * of rows rather than fields can reuse the same shell.
 *
 * `bleed` takes the card edge-to-edge on phones and insets it from sm: up, which
 * suits full-width tappable rows. Left off, the card keeps the inset rounded
 * look used on the profile page.
 */
export default function SectionCard({
    icon,
    heading,
    subheading,
    onSubmit,
    tone = 'brand',
    bleed = false,
    children,
}: {
    icon: ReactNode;
    heading: string;
    subheading: string;
    onSubmit?: (event: FormEvent) => void;
    tone?: 'brand' | 'destructive';
    bleed?: boolean;
    children: ReactNode;
}) {
    const edges = bleed
        ? '-mx-6 border-y sm:mx-0 sm:rounded-2xl sm:border'
        : 'rounded-2xl border';

    const chip =
        tone === 'destructive'
            ? 'bg-destructive/10 text-destructive'
            : 'bg-brand-green/10 text-brand-green';

    const shell = tone === 'destructive' ? DESTRUCTIVE_SHELL : SHELL;

    const header = (
        <div className="flex items-center gap-3">
            <span
                className={`flex h-10 w-10 shrink-0 items-center justify-center rounded-xl ${chip}`}
            >
                {icon}
            </span>
            <div className="min-w-0">
                <h2 className="text-base font-bold text-brand-grey dark:text-gray-100">
                    {heading}
                </h2>
                <p className="text-sm text-gray-500 dark:text-gray-400">
                    {subheading}
                </p>
            </div>
        </div>
    );

    if (onSubmit) {
        return (
            <form onSubmit={onSubmit} className={`${edges} ${shell} p-6`}>
                {header}
                {children}
            </form>
        );
    }

    // Rows manage their own horizontal padding so their tap target and dividers
    // reach the full width of the card.
    return (
        <section className={`${edges} ${shell} pt-6`}>
            <div className="px-6">{header}</div>
            {children}
        </section>
    );
}
