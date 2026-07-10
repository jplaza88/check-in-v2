import { Head, Link, usePage } from '@inertiajs/react';

import Footer from '@/components/Footer';
import Navbar from '@/components/Navbar';
import appointment from '@/routes/appointment';
import checkIn from '@/routes/checkIn';

interface HomeTranslations {
    eyebrow: string;
    headingLine1: string;
    headingLine2: string;
    subheading: string;
    checkInCardTitle: string;
    checkInCardDescription: string;
    checkInCardCta: string;
    appointmentCardTitle: string;
    appointmentCardDescription: string;
    appointmentCardCta: string;
    howItWorks: string;
    step1Title: string;
    step1Description: string;
    step2Title: string;
    step2Description: string;
    step3Title: string;
    step3Description: string;
}

interface PageProps {
    translations: { home: HomeTranslations };
    [key: string]: unknown;
}

function ArrowIcon() {
    return (
        <svg
            className="size-4 transition-transform duration-300 group-hover/card:translate-x-1"
            viewBox="0 0 20 20"
            fill="none"
            aria-hidden
        >
            <path
                d="M4 10h12m0 0-5-5m5 5-5 5"
                stroke="currentColor"
                strokeWidth="1.75"
                strokeLinecap="round"
                strokeLinejoin="round"
            />
        </svg>
    );
}

function TruckIcon() {
    return (
        <svg className="size-6" viewBox="0 0 24 24" fill="none" aria-hidden>
            <path
                d="M3 6.5A1.5 1.5 0 0 1 4.5 5h9A1.5 1.5 0 0 1 15 6.5V16H3V6.5ZM15 9h3.6a1.5 1.5 0 0 1 1.3.75L21.5 12.5V16H15V9Z"
                stroke="currentColor"
                strokeWidth="1.6"
                strokeLinejoin="round"
            />
            <circle cx="7" cy="17.5" r="2" stroke="currentColor" strokeWidth="1.6" />
            <circle cx="17.5" cy="17.5" r="2" stroke="currentColor" strokeWidth="1.6" />
        </svg>
    );
}

function CalendarIcon() {
    return (
        <svg className="size-6" viewBox="0 0 24 24" fill="none" aria-hidden>
            <rect
                x="3.5"
                y="5"
                width="17"
                height="15"
                rx="2.5"
                stroke="currentColor"
                strokeWidth="1.6"
            />
            <path
                d="M3.5 9.5h17M8 3.5v3M16 3.5v3"
                stroke="currentColor"
                strokeWidth="1.6"
                strokeLinecap="round"
            />
            <path
                d="m9 14.5 2 2 4-4"
                stroke="currentColor"
                strokeWidth="1.6"
                strokeLinecap="round"
                strokeLinejoin="round"
            />
        </svg>
    );
}

function ActionCard({
    href,
    icon,
    title,
    description,
    cta,
}: {
    href: string;
    icon: React.ReactNode;
    title: string;
    description: string;
    cta: string;
}) {
    return (
        <Link
            href={href}
            prefetch
            className="group/card relative flex flex-col justify-between overflow-hidden rounded-3xl bg-white p-8 ring-1 ring-gray-200/80 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-green/5 hover:ring-brand-green/40 dark:bg-gray-800/40 dark:ring-gray-700/60 dark:hover:ring-brand-green/40"
        >
            {/* Soft corner glow on hover */}
            <div
                aria-hidden
                className="pointer-events-none absolute -top-16 -right-16 h-40 w-40 rounded-full bg-brand-green/0 blur-2xl transition-colors duration-500 group-hover/card:bg-brand-green/10"
            />

            <div className="relative">
                <div className="flex size-12 items-center justify-center rounded-2xl bg-brand-green/10 text-brand-green transition-colors duration-300 group-hover/card:bg-brand-green group-hover/card:text-white">
                    {icon}
                </div>

                <h2 className="mt-6 text-2xl font-bold tracking-tight text-brand-grey dark:text-gray-100">
                    {title}
                </h2>
                <p className="mt-2.5 text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                    {description}
                </p>
            </div>

            <div className="relative mt-8 inline-flex items-center gap-2 text-sm font-semibold text-brand-green">
                {cta}
                <ArrowIcon />
            </div>
        </Link>
    );
}

export default function Welcome() {
    const { translations } = usePage<PageProps>().props;
    const t = translations.home;
    const appName = import.meta.env.VITE_APP_NAME;

    const steps = [
        { title: t.step1Title, description: t.step1Description },
        { title: t.step2Title, description: t.step2Description },
        { title: t.step3Title, description: t.step3Description },
    ];

    return (
        <>
            <Head title={appName} />

            <div className="flex min-h-dvh flex-col bg-white dark:bg-gray-900">
                <Navbar hideLogin />

                <main className="flex-1">
                    {/* Hero */}
                    <section className="relative overflow-hidden">
                        {/* Ambient brand glow */}
                        <div
                            aria-hidden
                            className="pointer-events-none absolute -top-48 -right-24 h-[36rem] w-[36rem] rounded-full bg-brand-green/10 blur-3xl dark:bg-brand-green/5"
                        />

                        <div className="mx-auto grid max-w-7xl items-center gap-12 px-6 py-16 lg:grid-cols-2 lg:py-24">
                            {/* Copy */}
                            <div className="relative">
                                <div className="flex items-center gap-2.5">
                                    <span className="size-1.5 rounded-full bg-brand-green" />
                                    <span className="text-xs font-bold tracking-widest text-brand-green uppercase">
                                        {t.eyebrow}
                                    </span>
                                </div>

                                <h1 className="mt-5 text-5xl leading-[1.02] font-bold tracking-tight text-brand-grey sm:text-6xl lg:text-7xl dark:text-gray-50">
                                    {t.headingLine1}
                                    <br />
                                    <span className="text-brand-green">
                                        {t.headingLine2}
                                    </span>
                                </h1>

                                <p className="mt-6 max-w-md text-lg leading-relaxed text-gray-500 dark:text-gray-400">
                                    {t.subheading}
                                </p>
                            </div>

                            {/* Image panel */}
                            <div className="relative">
                                <div className="relative aspect-[4/3] w-full overflow-hidden rounded-[2rem] shadow-2xl ring-1 ring-black/5 lg:aspect-[5/6] dark:ring-white/10">
                                    <div className="absolute inset-0 bg-side-image bg-cover bg-center" />
                                    <div className="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent" />

                                    {/* Floating brand pill */}
                                    <div className="absolute bottom-5 left-5 flex items-center gap-2 rounded-full bg-white/90 px-4 py-2 text-xs font-semibold text-brand-grey shadow-lg backdrop-blur-sm dark:bg-gray-900/85 dark:text-gray-100">
                                        <span className="relative flex size-2">
                                            <span className="absolute inline-flex size-full animate-ping rounded-full bg-brand-green/70" />
                                            <span className="relative inline-flex size-2 rounded-full bg-brand-green" />
                                        </span>
                                        {appName}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Action cards */}
                    <section className="mx-auto max-w-7xl px-6 pb-4">
                        <div className="grid gap-5 sm:grid-cols-2">
                            <ActionCard
                                href={checkIn.selectLocation().url}
                                icon={<TruckIcon />}
                                title={t.checkInCardTitle}
                                description={t.checkInCardDescription}
                                cta={t.checkInCardCta}
                            />
                            <ActionCard
                                href={appointment.selectLocation().url}
                                icon={<CalendarIcon />}
                                title={t.appointmentCardTitle}
                                description={t.appointmentCardDescription}
                                cta={t.appointmentCardCta}
                            />
                        </div>
                    </section>

                    {/* How it works */}
                    <section className="mx-auto max-w-7xl px-6 py-16 lg:py-24">
                        <span className="text-xs font-bold tracking-widest text-brand-green uppercase">
                            {t.howItWorks}
                        </span>

                        <div className="mt-8 grid gap-x-8 gap-y-10 sm:grid-cols-3">
                            {steps.map((step, index) => (
                                <div key={index} className="relative">
                                    <span className="flex size-9 items-center justify-center rounded-full bg-brand-green text-sm font-bold text-white shadow-sm shadow-brand-green/30">
                                        {index + 1}
                                    </span>
                                    <h3 className="mt-4 text-lg font-semibold text-brand-grey dark:text-gray-100">
                                        {step.title}
                                    </h3>
                                    <p className="mt-1.5 text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                                        {step.description}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </section>
                </main>

                <Footer hideLogin />
            </div>
        </>
    );
}
