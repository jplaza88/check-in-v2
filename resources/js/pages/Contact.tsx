import { Head, usePage } from '@inertiajs/react';

import Footer from '@/components/Footer';
import Navbar from '@/components/Navbar';

interface ContactTranslations {
    eyebrow: string;
    title: string;
    subheading: string;
    phoneLabel: string;
    phoneHelp: string;
    emailLabel: string;
    emailHelp: string;
    bannerTitle: string;
}

interface PageProps {
    translations: { contact: ContactTranslations };
    phone: string;
    email: string;
    [key: string]: unknown;
}

function PhoneIcon() {
    return (
        <svg className="size-6" viewBox="0 0 24 24" fill="none" aria-hidden>
            <path
                d="M6.5 4h2.2l1.3 3.3-1.6 1.2a11 11 0 0 0 5.1 5.1l1.2-1.6L18 13.3V15.5A2.5 2.5 0 0 1 15.5 18 12.5 12.5 0 0 1 4 6.5 2.5 2.5 0 0 1 6.5 4Z"
                stroke="currentColor"
                strokeWidth="1.6"
                strokeLinejoin="round"
            />
        </svg>
    );
}

function MailIcon() {
    return (
        <svg className="size-6" viewBox="0 0 24 24" fill="none" aria-hidden>
            <rect x="3.5" y="5.5" width="17" height="13" rx="2.5" stroke="currentColor" strokeWidth="1.6" />
            <path d="m4.5 7 7.5 5.5L19.5 7" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" strokeLinejoin="round" />
        </svg>
    );
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

function ContactCard({
    href,
    icon,
    label,
    value,
    help,
}: {
    href: string;
    icon: React.ReactNode;
    label: string;
    value: string;
    help: string;
}) {
    return (
        <a
            href={href}
            className="group/card relative flex flex-col overflow-hidden rounded-3xl bg-white p-8 ring-1 ring-gray-200/80 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-green/5 hover:ring-brand-green/40 dark:bg-gray-800/40 dark:ring-gray-700/60 dark:hover:ring-brand-green/40"
        >
            <div
                aria-hidden
                className="pointer-events-none absolute -top-16 -right-16 h-40 w-40 rounded-full bg-brand-green/0 blur-2xl transition-colors duration-500 group-hover/card:bg-brand-green/10"
            />

            <div className="relative flex size-12 items-center justify-center rounded-2xl bg-brand-green/10 text-brand-green transition-colors duration-300 group-hover/card:bg-brand-green group-hover/card:text-white">
                {icon}
            </div>

            <p className="relative mt-6 text-xs font-bold tracking-widest text-brand-green uppercase">
                {label}
            </p>
            <p className="relative mt-1.5 text-xl font-bold break-words text-brand-grey dark:text-gray-100">
                {value}
            </p>
            <p className="relative mt-1 text-sm text-gray-500 dark:text-gray-400">{help}</p>

            <div className="relative mt-6 inline-flex items-center gap-2 text-sm font-semibold text-brand-green">
                {label}
                <ArrowIcon />
            </div>
        </a>
    );
}

export default function Contact() {
    const { translations, phone, email } = usePage<PageProps>().props;
    const t = translations.contact;
    const appName = import.meta.env.VITE_APP_NAME;

    const telHref = `tel:${phone.replace(/[^\d+]/g, '')}`;

    return (
        <>
            <Head title={t.title} />

            <div className="flex min-h-dvh flex-col bg-white dark:bg-gray-900">
                <Navbar hideLogin />

                <main className="flex-1">
                    <section className="relative overflow-hidden">
                        <div
                            aria-hidden
                            className="pointer-events-none absolute -top-40 left-1/2 h-[30rem] w-[30rem] -translate-x-1/2 rounded-full bg-brand-green/10 blur-3xl dark:bg-brand-green/5"
                        />

                        <div className="mx-auto max-w-3xl px-6 pt-16 pb-10 text-center">
                            <div className="flex items-center justify-center gap-2.5">
                                <span className="size-1.5 rounded-full bg-brand-green" />
                                <span className="text-xs font-bold tracking-widest text-brand-green uppercase">
                                    {t.eyebrow}
                                </span>
                            </div>

                            <h1 className="mt-4 text-4xl font-bold tracking-tight text-brand-grey sm:text-5xl dark:text-gray-50">
                                {t.title}
                            </h1>
                            <p className="mx-auto mt-4 max-w-xl text-base text-gray-500 dark:text-gray-400">
                                {t.subheading}
                            </p>
                        </div>
                    </section>

                    <section className="mx-auto max-w-3xl px-6 pb-12">
                        <div className="grid gap-5 sm:grid-cols-2">
                            <ContactCard
                                href={telHref}
                                icon={<PhoneIcon />}
                                label={t.phoneLabel}
                                value={phone}
                                help={t.phoneHelp}
                            />
                            <ContactCard
                                href={`mailto:${email}`}
                                icon={<MailIcon />}
                                label={t.emailLabel}
                                value={email}
                                help={t.emailHelp}
                            />
                        </div>

                        {/* Decorative brand banner */}
                        <div className="relative mt-5 overflow-hidden rounded-3xl ring-1 ring-black/5 dark:ring-white/10">
                            <div className="absolute inset-0 bg-side-image bg-cover bg-center" />
                            <div className="absolute inset-0 bg-gradient-to-t from-black/65 via-black/25 to-black/10" />
                            <div className="relative flex min-h-52 flex-col items-center justify-end gap-3 p-8 text-center">
                                <span className="inline-flex items-center gap-2 rounded-full bg-white/90 px-4 py-1.5 text-xs font-semibold text-brand-grey shadow-lg backdrop-blur-sm dark:bg-gray-900/85 dark:text-gray-100">
                                    <span className="size-1.5 rounded-full bg-brand-green" />
                                    {appName}
                                </span>
                                <p className="max-w-md text-2xl font-bold tracking-tight text-white">
                                    {t.bannerTitle}
                                </p>
                            </div>
                        </div>
                    </section>
                </main>

                <Footer hideLogin />
            </div>
        </>
    );
}
