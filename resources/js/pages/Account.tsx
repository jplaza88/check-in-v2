import { Head, Link, usePage } from '@inertiajs/react';
import {
    CalendarDays,
    CalendarPlus,
    ChevronRight,
    Clock,
    MapPin,
    PackageOpen,
    Truck,
} from 'lucide-react';

import AppLayout from '@/layouts/AppLayout';

// ── Types ────────────────────────────────────────────────────────────────────

interface NextAppointment {
    referenceNumber: string;
    locationName: string;
    monthShort: string;
    day: string;
    time: string;
}

interface RecentCheckIn {
    referenceNumber: string;
    locationName: string;
    date: string;
    status: string;
}

interface AccountTranslations {
    greetingMorning: string;
    greetingAfternoon: string;
    greetingEvening: string;
    checkInNow: string;
    checkInSubtitle: string;
    nextAppointmentTitle: string;
    scheduled: string;
    noAppointment: string;
    noAppointmentSubtitle: string;
    bookAppointment: string;
    recentTitle: string;
    noRecent: string;
    noRecentSubtitle: string;
    reference: string;
    statusPending: string;
    statusCompleted: string;
    statusCancelled: string;
}

interface PageProps {
    auth: { user: { name: string } };
    currentLocale: string;
    nextAppointment: NextAppointment | null;
    recentCheckIns: RecentCheckIn[];
    translations: { account: AccountTranslations };
    [key: string]: unknown;
}

// ── Status badge ─────────────────────────────────────────────────────────────

function StatusBadge({
    status,
    t,
}: {
    status: string;
    t: AccountTranslations;
}) {
    const map: Record<
        string,
        { label: string; className: string; dot: string }
    > = {
        pending: {
            label: t.statusPending,
            className: 'bg-amber-500/12 text-amber-700 dark:text-amber-400',
            dot: 'bg-amber-500',
        },
        completed: {
            label: t.statusCompleted,
            className: 'bg-brand-green/12 text-brand-green',
            dot: 'bg-brand-green',
        },
        cancelled: {
            label: t.statusCancelled,
            className: 'bg-gray-400/15 text-gray-500 dark:text-gray-400',
            dot: 'bg-gray-400',
        },
    };

    const badge = map[status] ?? map.pending;

    return (
        <span
            className={`inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ${badge.className}`}
        >
            <span className={`h-1.5 w-1.5 rounded-full ${badge.dot}`} />
            {badge.label}
        </span>
    );
}

// ── Section heading ──────────────────────────────────────────────────────────

function SectionHeading({ children }: { children: string }) {
    return (
        <h2 className="mb-3 px-1 text-xs font-bold tracking-widest text-brand-green uppercase">
            {children}
        </h2>
    );
}

// ── Page ─────────────────────────────────────────────────────────────────────

export default function Account() {
    const { auth, currentLocale, nextAppointment, recentCheckIns, translations } =
        usePage<PageProps>().props;
    const t = translations.account;
    const firstName = auth.user.name.split(' ')[0];

    const hour = new Date().getHours();
    const greeting =
        hour < 12
            ? t.greetingMorning
            : hour < 18
              ? t.greetingAfternoon
              : t.greetingEvening;

    const today = new Intl.DateTimeFormat(currentLocale, {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
    }).format(new Date());

    return (
        <AppLayout>
            <Head title={t.checkInNow} />

            {/* Hero */}
            <div className="relative overflow-hidden">
                <div
                    aria-hidden
                    className="pointer-events-none absolute -top-16 left-1/2 h-56 w-[36rem] max-w-full -translate-x-1/2 rounded-full bg-brand-green/10 blur-3xl dark:bg-brand-green/[0.07]"
                />
                <div className="relative mx-auto max-w-2xl px-6 pt-8 pb-2">
                    <p className="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {greeting},
                    </p>
                    <h1 className="mt-0.5 text-3xl font-bold tracking-tight text-brand-grey dark:text-gray-50">
                        {firstName}
                    </h1>
                    <p className="mt-1 text-sm text-gray-400 capitalize dark:text-gray-500">
                        {today}
                    </p>
                </div>
            </div>

            <div className="mx-auto max-w-2xl space-y-7 px-6 pt-4 pb-12">
                {/* Primary CTA */}
                <Link
                    href="/check-in/select-location"
                    className="group relative flex items-center gap-4 overflow-hidden rounded-3xl bg-gradient-to-br from-brand-green to-green-600 p-5 text-white shadow-lg shadow-brand-green/25 transition-all active:scale-[0.99]"
                >
                    <div
                        aria-hidden
                        className="pointer-events-none absolute -top-10 -right-8 h-32 w-32 rounded-full bg-white/15 blur-2xl"
                    />
                    <span className="relative flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/20 ring-1 ring-white/25 backdrop-blur-sm">
                        <Truck className="h-7 w-7" />
                    </span>
                    <span className="relative min-w-0 flex-1">
                        <span className="block text-lg font-bold">
                            {t.checkInNow}
                        </span>
                        <span className="mt-0.5 block text-sm text-white/85">
                            {t.checkInSubtitle}
                        </span>
                    </span>
                    <ChevronRight className="relative h-5 w-5 shrink-0 text-white/85 transition-transform group-hover:translate-x-0.5" />
                </Link>

                {/* Next appointment */}
                <section>
                    <SectionHeading>{t.nextAppointmentTitle}</SectionHeading>
                    {nextAppointment ? (
                        <div className="flex items-stretch gap-4 rounded-2xl border border-gray-200/80 bg-white p-4 shadow-sm ring-1 ring-black/[0.02] dark:border-gray-700/60 dark:bg-gray-800/50 dark:ring-white/[0.02]">
                            {/* Event date chip */}
                            <div className="flex w-16 shrink-0 flex-col items-center justify-center rounded-xl bg-brand-green/10 py-2.5 text-brand-green">
                                <span className="text-[11px] font-bold tracking-wide uppercase">
                                    {nextAppointment.monthShort}
                                </span>
                                <span className="text-2xl leading-none font-bold">
                                    {nextAppointment.day}
                                </span>
                            </div>
                            {/* Details */}
                            <div className="flex min-w-0 flex-1 flex-col justify-center">
                                <div className="flex items-center gap-2">
                                    <Clock className="h-4 w-4 shrink-0 text-gray-400" />
                                    <span className="font-semibold text-brand-grey dark:text-gray-100">
                                        {nextAppointment.time}
                                    </span>
                                    <span className="rounded-full bg-brand-green/12 px-2 py-0.5 text-[10px] font-bold tracking-wide text-brand-green uppercase">
                                        {t.scheduled}
                                    </span>
                                </div>
                                <p className="mt-1.5 flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
                                    <MapPin className="h-3.5 w-3.5 shrink-0" />
                                    <span className="truncate">
                                        {nextAppointment.locationName}
                                    </span>
                                </p>
                                <p className="mt-1 font-mono text-[11px] text-gray-400 dark:text-gray-500">
                                    {t.reference} #
                                    {nextAppointment.referenceNumber}
                                </p>
                            </div>
                        </div>
                    ) : (
                        <div className="flex flex-col items-center gap-1.5 rounded-2xl border border-dashed border-gray-200 bg-gray-50/70 px-6 py-8 text-center dark:border-gray-700/60 dark:bg-gray-800/30">
                            <span className="mb-1 flex h-12 w-12 items-center justify-center rounded-full bg-brand-green/10 text-brand-green">
                                <CalendarDays className="h-6 w-6" />
                            </span>
                            <p className="text-sm font-semibold text-brand-grey dark:text-gray-200">
                                {t.noAppointment}
                            </p>
                            <p className="max-w-xs text-xs text-gray-500 dark:text-gray-400">
                                {t.noAppointmentSubtitle}
                            </p>
                            <Link
                                href="/appointment/select-location"
                                className="mt-3 inline-flex items-center gap-1.5 rounded-full bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-brand-green/30 transition-colors hover:bg-brand-green/90"
                            >
                                <CalendarPlus className="h-4 w-4" />
                                {t.bookAppointment}
                            </Link>
                        </div>
                    )}
                </section>

                {/* Recent activity */}
                <section>
                    <SectionHeading>{t.recentTitle}</SectionHeading>
                    {recentCheckIns.length > 0 ? (
                        <ul className="divide-y divide-gray-100 overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm ring-1 ring-black/[0.02] dark:divide-gray-700/50 dark:border-gray-700/60 dark:bg-gray-800/50 dark:ring-white/[0.02]">
                            {recentCheckIns.map((checkIn) => (
                                <li
                                    key={checkIn.referenceNumber}
                                    className="flex items-center gap-3.5 px-4 py-3.5 transition-colors hover:bg-gray-50/60 dark:hover:bg-gray-800/40"
                                >
                                    <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-green/10 text-brand-green">
                                        <MapPin className="h-4 w-4" />
                                    </span>
                                    <div className="min-w-0 flex-1">
                                        <p className="truncate text-sm font-semibold text-brand-grey dark:text-gray-100">
                                            {checkIn.locationName}
                                        </p>
                                        <p className="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                                            {checkIn.date} · {t.reference} #
                                            {checkIn.referenceNumber}
                                        </p>
                                    </div>
                                    <StatusBadge status={checkIn.status} t={t} />
                                </li>
                            ))}
                        </ul>
                    ) : (
                        <div className="flex flex-col items-center gap-1.5 rounded-2xl border border-dashed border-gray-200 bg-gray-50/70 px-6 py-10 text-center dark:border-gray-700/60 dark:bg-gray-800/30">
                            <span className="mb-1 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-700/60 dark:text-gray-500">
                                <PackageOpen className="h-6 w-6" />
                            </span>
                            <p className="text-sm font-semibold text-brand-grey dark:text-gray-200">
                                {t.noRecent}
                            </p>
                            <p className="max-w-xs text-xs text-gray-500 dark:text-gray-400">
                                {t.noRecentSubtitle}
                            </p>
                        </div>
                    )}
                </section>
            </div>
        </AppLayout>
    );
}
