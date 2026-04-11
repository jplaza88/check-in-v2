import { Head, usePage } from '@inertiajs/react';
import PublicLayout from '@/layouts/PublicLayout';

interface AppointmentSelectLocation {
    selectAnAppointmentLocation: string;
    closed: string;
    open: string;
}

interface Translations {
    appointmentSelectLocation: AppointmentSelectLocation;
}

interface PageProps {
    locations: Location[];
    translations: Translations;
    [key: string]: unknown;
}

interface Location {
    id: number | string;
    name: string;
    address: string;
    todayOpenCloseTime: string | null;
    isOpen: boolean;
    hasException: boolean;
    reason: string | null;
    isExceptionClosure: boolean;
    isClosingSoon: boolean;
}

export default function AppointmentSelectLocation() {
    const { translations, locations } = usePage<PageProps>().props;
    const pageTranslations: AppointmentSelectLocation = translations.appointmentSelectLocation;


    return (
        <PublicLayout>
            <Head title="Appointment: Select Location" />

            <div className="px-4 py-3">
                <div className="mx-auto max-w-md">
                    <h1 className="mb-6 text-3xl font-bold text-brand-grey dark:text-gray-100">
                        {pageTranslations.selectAnAppointmentLocation}:
                    </h1>

                    <div className="space-y-3">
                        {/* Skeleton */}
                        {locations.length === 0 &&
                            Array.from({ length: 3 }).map((_, i) => (
                                <div
                                    key={i}
                                    className="mb-3 flex animate-pulse items-start justify-between space-x-4 rounded-lg border border-gray-200 bg-white p-4 shadow dark:border-gray-700/60 dark:bg-gray-800"
                                >
                                    <div className="mr-6 flex w-6 flex-col items-center">
                                        <div className="h-6 w-6 rounded-full bg-gray-300 dark:bg-gray-700" />
                                        <div className="mt-2 h-3 w-10 rounded bg-gray-300 dark:bg-gray-700" />
                                    </div>
                                    <div className="flex-1 space-y-2">
                                        <div className="h-4 w-3/4 rounded bg-gray-300 dark:bg-gray-700" />
                                        <div className="h-3 w-1/2 rounded bg-gray-300 dark:bg-gray-700" />
                                        <div className="h-2 w-1/4 rounded bg-gray-300 dark:bg-gray-700" />
                                    </div>
                                    <div className="h-5 w-14 self-start rounded-full bg-gray-300 dark:bg-gray-700" />
                                </div>
                            ))}

                        {/* Locations List */}
                        {locations.length > 0 &&
                            locations.map((location, idx) => (
                                <button
                                    key={location.id}
                                    className="relative block w-full focus:outline-none"
                                    aria-label={`Book appointment at ${location.name}, ${location.address}`}
                                >
                                    <div
                                        className={`'border-gray-200 dark:hover:border-gray-600'} flex min-h-22 items-stretch rounded-lg border bg-white p-4 text-sm font-medium shadow-sm transition hover:border-gray-300 dark:border-gray-700/60 dark:bg-gray-800`}
                                    >
                                        {/* Text */}
                                        <div className="flex flex-1 flex-col justify-center gap-0.5 text-left">
                                            {/* Exception / Closing Soon badge — mutually exclusive, above name */}
                                            {location.isExceptionClosure ? (
                                                <div className="mb-3">
                                                    <span
                                                        title={
                                                            location.reason ??
                                                            'Closed today'
                                                        }
                                                        aria-label={`Closed today: ${location.reason ?? 'Closed today'}`}
                                                        className="inline-flex items-center gap-1 rounded-full bg-red-500/15 px-2.5 py-1 text-xs font-medium text-red-700 dark:text-red-400"
                                                    >
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 16 16"
                                                            className="h-3 w-3 fill-current"
                                                        >
                                                            <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm0 3.5a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-1.5 0v-3A.75.75 0 0 1 8 4.5zm0 6.5a.875.875 0 1 1 0-1.75A.875.875 0 0 1 8 11z" />
                                                        </svg>
                                                        {location.reason ??
                                                            'Closed today'}
                                                    </span>
                                                </div>
                                            ) : location.hasException ? (
                                                <div className="mb-3">
                                                    <span
                                                        title={
                                                            location.reason ??
                                                            'Special hours today'
                                                        }
                                                        aria-label={`Schedule exception: ${location.reason ?? 'Special hours today'}`}
                                                        className="inline-flex items-center gap-1 rounded-full bg-amber-500/15 px-2.5 py-1 text-xs font-medium text-amber-700 dark:text-amber-400"
                                                    >
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 16 16"
                                                            className="h-3 w-3 fill-current"
                                                        >
                                                            <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm0 3.5a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-1.5 0v-3A.75.75 0 0 1 8 4.5zm0 6.5a.875.875 0 1 1 0-1.75A.875.875 0 0 1 8 11z" />
                                                        </svg>
                                                        Special hours
                                                    </span>
                                                </div>
                                            ) : location.isClosingSoon ? (
                                                <div className="mb-3">
                                                    <span
                                                        aria-label="Closing soon"
                                                        className="inline-flex items-center gap-1 rounded-full bg-orange-500/15 px-2.5 py-1 text-xs font-medium text-orange-700 dark:text-orange-400"
                                                    >
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 16 16"
                                                            className="h-3 w-3 fill-current"
                                                        >
                                                            <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm0 2a.75.75 0 0 1 .75.75V8a.75.75 0 0 1-.22.53l-2 2a.75.75 0 1 1-1.06-1.06L7.25 7.69V3.75A.75.75 0 0 1 8 3z" />
                                                        </svg>
                                                        Closing soon
                                                    </span>
                                                </div>
                                            ) : null}

                                            <div
                                                className={`font-semibold ${idx === 0 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-800 dark:text-gray-100'}`}
                                            >
                                                {location.name}
                                            </div>
                                            <div className="text-sm text-gray-600 dark:text-gray-300">
                                                {location.address}
                                            </div>

                                            {/* Hours + Open/Closed + Nearest */}
                                            <div className="mt-1 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                                <span
                                                    aria-label={`Hours of operation: ${location.todayOpenCloseTime}`}
                                                >
                                                    {location.todayOpenCloseTime ??
                                                        (location.hasException
                                                            ? 'Closed today'
                                                            : 'Hours unavailable')}
                                                </span>

                                                {location.isOpen ? (
                                                    <span className="inline-flex rounded-full bg-green-500/20 px-2.5 py-1 text-xs font-medium text-green-700">
                                                        {pageTranslations.open}
                                                    </span>
                                                ) : (
                                                    <span className="inline-flex rounded-full bg-red-500/20 px-2.5 py-1 text-xs font-medium text-red-700">
                                                        {
                                                            pageTranslations.closed
                                                        }
                                                    </span>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            ))}
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
