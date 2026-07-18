import { Head, usePage } from '@inertiajs/react';
import { useState } from 'react';

import Footer from '@/components/Footer';
import Navbar from '@/components/Navbar';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';

interface ScheduleDay {
    weekday: string;
    shortWeekday: string;
    dayOfMonth: number;
    isToday: boolean;
    hours: string | null;
    hasOverride: boolean;
    isOverrideClosure: boolean;
    reason: string | null;
}

interface LocationWeek {
    id: string;
    name: string;
    address: string;
    timezoneAbbr: string;
    checkInEnabled: boolean;
    appointmentEnabled: boolean;
    isOpenNowCheckIn: boolean;
    isOpenNowAppointment: boolean;
    checkInWeek: ScheduleDay[];
    appointmentWeek: ScheduleDay[];
}

interface ScheduleTranslations {
    eyebrow: string;
    title: string;
    subheading: string;
    openNow: string;
    closed: string;
    today: string;
    todaysHours: string;
    notAvailableCheckIn: string;
    notAvailableAppointment: string;
    noLocations: string;
    directions: string;
}

interface PageProps {
    translations: {
        schedule: ScheduleTranslations;
        publicNavigation: { checkIn: string; appointment: string };
    };
    locations: LocationWeek[];
    [key: string]: unknown;
}

type Mode = 'checkin' | 'appointment';

function TruckIcon() {
    return (
        <svg className="size-4" viewBox="0 0 24 24" fill="none" aria-hidden>
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
        <svg className="size-4" viewBox="0 0 24 24" fill="none" aria-hidden>
            <rect x="3.5" y="5" width="17" height="15" rx="2.5" stroke="currentColor" strokeWidth="1.6" />
            <path d="M3.5 9.5h17M8 3.5v3M16 3.5v3" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" />
        </svg>
    );
}

function MapPinIcon() {
    return (
        <svg viewBox="0 0 384 512" className="size-4 fill-current" aria-hidden>
            <path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z" />
        </svg>
    );
}

function ExternalLinkIcon() {
    return (
        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
            className="size-3.5"
            aria-hidden
        >
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
            <polyline points="15 3 21 3 21 9" />
            <line x1="10" y1="14" x2="21" y2="3" />
        </svg>
    );
}

function LivePulse() {
    return (
        <span className="relative flex size-1.5">
            <span className="absolute inline-flex size-full animate-ping rounded-full bg-brand-green/70" />
            <span className="relative inline-flex size-1.5 rounded-full bg-brand-green" />
        </span>
    );
}

function DayRow({ day, t }: { day: ScheduleDay; t: ScheduleTranslations }) {
    return (
        <div
            className={`flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm transition-colors ${
                day.isToday
                    ? 'bg-brand-green/10 dark:bg-brand-green/15'
                    : 'hover:bg-gray-50 dark:hover:bg-gray-800/40'
            }`}
        >
            <div className="flex min-w-0 items-baseline gap-2">
                <span
                    className={
                        day.isToday
                            ? 'font-semibold text-brand-grey dark:text-gray-100'
                            : 'text-gray-600 dark:text-gray-300'
                    }
                >
                    {day.weekday}
                </span>
                <span className="text-xs text-gray-400 dark:text-gray-500">{day.dayOfMonth}</span>
                {day.isToday && (
                    <span className="rounded-full bg-brand-green px-2 py-0.5 text-[10px] font-bold tracking-wide text-white uppercase">
                        {t.today}
                    </span>
                )}
            </div>

            <div className="flex min-w-0 items-center justify-end gap-2 text-right">
                {day.hasOverride && day.reason && (
                    <span
                        title={day.reason}
                        className={`max-w-[8rem] truncate rounded-full px-2 py-0.5 text-[11px] font-medium ${
                            day.isOverrideClosure
                                ? 'bg-red-500/15 text-red-700 dark:text-red-400'
                                : 'bg-amber-500/15 text-amber-700 dark:text-amber-400'
                        }`}
                    >
                        {day.reason}
                    </span>
                )}
                <span
                    className={`tabular-nums whitespace-nowrap ${
                        day.hours
                            ? day.isToday
                                ? 'font-semibold text-brand-grey dark:text-gray-100'
                                : 'text-gray-600 dark:text-gray-300'
                            : 'text-gray-400 dark:text-gray-500'
                    }`}
                >
                    {day.hours ?? t.closed}
                </span>
            </div>
        </div>
    );
}

function LocationCard({
    location,
    mode,
    t,
}: {
    location: LocationWeek;
    mode: Mode;
    t: ScheduleTranslations;
}) {
    const week = mode === 'checkin' ? location.checkInWeek : location.appointmentWeek;
    const enabled = mode === 'checkin' ? location.checkInEnabled : location.appointmentEnabled;
    const isOpenNow = mode === 'checkin' ? location.isOpenNowCheckIn : location.isOpenNowAppointment;
    const unavailable = mode === 'checkin' ? t.notAvailableCheckIn : t.notAvailableAppointment;
    const today = week.find((day) => day.isToday);

    return (
        <Accordion
            type="single"
            collapsible
            className="rounded-3xl border-0 bg-white ring-1 ring-gray-200/80 transition-shadow duration-300 data-open:shadow-lg data-open:shadow-brand-green/5 dark:bg-gray-800/40 dark:ring-gray-700/60"
        >
            <AccordionItem value="week" disabled={!enabled} className="border-0 data-open:bg-transparent">
                <AccordionTrigger className="cursor-pointer items-start gap-4 px-5 py-4 hover:bg-gray-50/70 hover:no-underline dark:hover:bg-gray-800/40">
                    <div className="flex min-w-0 flex-1 items-start gap-3">
                        <span className="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-xl bg-brand-green/10 text-brand-green">
                            <MapPinIcon />
                        </span>

                        <div className="min-w-0 flex-1">
                            <div className="flex min-w-0 flex-col items-start gap-1 sm:flex-row sm:items-center sm:gap-2">
                                <h2 className="min-w-0 truncate text-base font-bold text-brand-grey dark:text-gray-100">
                                    {location.name}
                                </h2>
                                {enabled && isOpenNow && (
                                    <span className="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-brand-green/10 px-2.5 py-1 text-xs font-semibold text-brand-green">
                                        <LivePulse />
                                        {t.openNow}
                                    </span>
                                )}
                            </div>

                            <p className="mt-1 truncate text-xs font-normal text-gray-500 dark:text-gray-400">
                                {location.address}
                                <span className="text-gray-400 dark:text-gray-500"> · {location.timezoneAbbr}</span>
                            </p>

                            <p className="mt-2 text-sm font-normal">
                                <span className="text-gray-400 dark:text-gray-500">{t.todaysHours}: </span>
                                <span
                                    className={
                                        enabled && today?.hours
                                            ? 'font-semibold text-brand-grey dark:text-gray-100'
                                            : 'text-gray-400 dark:text-gray-500'
                                    }
                                >
                                    {enabled ? (today?.hours ?? t.closed) : unavailable}
                                </span>
                            </p>
                        </div>
                    </div>
                </AccordionTrigger>

                <AccordionContent className="px-3 pb-3">
                    <div className="flex flex-col gap-0.5 border-t border-gray-100 pt-2 dark:border-gray-700/60">
                        {week.map((day, index) => (
                            <DayRow key={index} day={day} t={t} />
                        ))}
                        <a
                            href={`https://maps.google.com/?q=${encodeURIComponent(location.address)}`}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="mt-2 inline-flex items-center gap-1.5 self-start px-3 text-xs font-semibold text-brand-green! no-underline! hover:opacity-80"
                        >
                            <ExternalLinkIcon />
                            {t.directions}
                        </a>
                    </div>
                </AccordionContent>
            </AccordionItem>
        </Accordion>
    );
}

export default function Schedule() {
    const { translations, locations } = usePage<PageProps>().props;
    const t = translations.schedule;
    const nav = translations.publicNavigation;

    const [mode, setMode] = useState<Mode>('checkin');

    const modes: { key: Mode; label: string; icon: React.ReactNode }[] = [
        { key: 'checkin', label: nav.checkIn, icon: <TruckIcon /> },
        { key: 'appointment', label: nav.appointment, icon: <CalendarIcon /> },
    ];

    return (
        <>
            <Head title={t.title} />

            <div className="flex min-h-dvh flex-col bg-white dark:bg-gray-900">
                <Navbar hideLogin />

                <main className="flex-1">
                    {/* Header */}
                    <section className="relative overflow-hidden">
                        <div
                            aria-hidden
                            className="pointer-events-none absolute -top-40 left-1/2 h-[30rem] w-[30rem] -translate-x-1/2 rounded-full bg-brand-green/10 blur-3xl dark:bg-brand-green/5"
                        />

                        <div className="mx-auto max-w-7xl px-6 pt-16 pb-8 text-center">
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

                            {/* Segmented toggle */}
                            <div className="mt-8 inline-flex rounded-full bg-gray-100 p-1 ring-1 ring-gray-200/70 dark:bg-gray-800/60 dark:ring-gray-700/60">
                                {modes.map((m) => (
                                    <button
                                        key={m.key}
                                        type="button"
                                        onClick={() => setMode(m.key)}
                                        aria-pressed={mode === m.key}
                                        className={`inline-flex cursor-pointer items-center gap-2 rounded-full px-5 py-2 text-sm font-semibold transition-all duration-300 ${
                                            mode === m.key
                                                ? 'bg-brand-green text-white shadow-sm shadow-brand-green/30'
                                                : 'text-gray-500 hover:text-brand-grey dark:text-gray-400 dark:hover:text-gray-200'
                                        }`}
                                    >
                                        {m.icon}
                                        {m.label}
                                    </button>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* Location list */}
                    <section className="mx-auto max-w-2xl px-6 pb-20">
                        {locations.length === 0 ? (
                            <p className="py-16 text-center text-gray-400 dark:text-gray-500">
                                {t.noLocations}
                            </p>
                        ) : (
                            <div className="flex flex-col gap-3">
                                {locations.map((location) => (
                                    <LocationCard
                                        key={location.id}
                                        location={location}
                                        mode={mode}
                                        t={t}
                                    />
                                ))}
                            </div>
                        )}
                    </section>
                </main>

                <Footer hideLogin />
            </div>
        </>
    );
}
