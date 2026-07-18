import type { ReactNode } from 'react';

// ── Document shell ───────────────────────────────────────────────────────────

export function LegalDocument({
    eyebrow,
    title,
    effectiveDate,
    intro,
    children,
}: {
    eyebrow: string;
    title: string;
    effectiveDate: string;
    intro?: ReactNode;
    children: ReactNode;
}) {
    return (
        <div>
            {/* Hero */}
            <section className="relative overflow-hidden">
                <div
                    aria-hidden
                    className="pointer-events-none absolute -top-40 left-1/2 h-[30rem] w-[30rem] -translate-x-1/2 rounded-full bg-brand-green/10 blur-3xl dark:bg-brand-green/5"
                />

                <div className="mx-auto max-w-3xl px-6 pt-16 pb-8">
                    <p className="mb-2 text-xs font-bold tracking-widest text-brand-green uppercase">
                        {eyebrow}
                    </p>
                    <h1 className="text-3xl font-bold tracking-tight text-brand-grey sm:text-4xl dark:text-gray-50">
                        {title}
                    </h1>
                    <p className="mt-3 text-sm text-gray-500 dark:text-gray-400">
                        Effective date: {effectiveDate}
                    </p>
                    {intro && (
                        <div className="mt-6 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                            {intro}
                        </div>
                    )}
                </div>
            </section>

            {/* Body */}
            <section className="mx-auto max-w-3xl px-6 pb-20">
                <div className="space-y-10">{children}</div>
            </section>
        </div>
    );
}

// ── Section ──────────────────────────────────────────────────────────────────

export function LegalSection({
    number,
    title,
    children,
}: {
    number: number;
    title: string;
    children: ReactNode;
}) {
    return (
        <section>
            <h2 className="mb-3 flex items-baseline gap-2 text-lg font-bold text-brand-grey dark:text-gray-100">
                <span className="text-brand-green">{number}.</span>
                {title}
            </h2>
            <div className="space-y-3 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                {children}
            </div>
        </section>
    );
}

// ── Bulleted list ────────────────────────────────────────────────────────────

export function LegalList({ items }: { items: ReactNode[] }) {
    return (
        <ul className="list-disc space-y-1.5 pl-5 marker:text-brand-green">
            {items.map((item, index) => (
                <li key={index}>{item}</li>
            ))}
        </ul>
    );
}

// ── Callout (disclaimer / contact block) ─────────────────────────────────────

export function LegalCallout({
    title,
    children,
}: {
    title: string;
    children: ReactNode;
}) {
    return (
        <div className="rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-700/60 dark:bg-gray-800/50">
            <p className="mb-1.5 text-sm font-semibold text-brand-grey dark:text-gray-100">
                {title}
            </p>
            <div className="text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                {children}
            </div>
        </div>
    );
}

// ── Placeholder marker (fill before publishing) ──────────────────────────────

export function Placeholder({ children }: { children: ReactNode }) {
    return (
        <span className="rounded bg-amber-100 px-1 font-medium text-amber-800 dark:bg-amber-500/20 dark:text-amber-300">
            {children}
        </span>
    );
}
