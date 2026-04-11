import { Head, usePage } from '@inertiajs/react';
import axios from 'axios';
import { useEffect, useState } from 'react';
import LocationDistanceController from '@/actions/App/Http/Controllers/LocationDistanceController';
import { useGeolocation } from '@/hooks/useGeolocation';
import PublicLayout from '@/layouts/PublicLayout';

type Context = 'checkin' | 'appointment';

interface CheckInSelectLocation {
    selectACheckInLocation: string;
    closed: string;
    open: string;
    nearest: string;
}

interface AppointmentSelectLocation {
    selectAnAppointmentLocation: string;
    closed: string;
    open: string;
    nearest: string;
}

interface Translations {
    checkInSelectLocation: CheckInSelectLocation;
    appointmentSelectLocation: AppointmentSelectLocation;
}

interface PageProps {
    locations: Location[];
    translations: Translations;
    context: Context;
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
    distance?: number | null; // Retrieved after page render
}

type LocationTexts = {
    selectLocation: string;
    closed: string;
    open: string;
    nearest: string;
};

function getSelectLocationTexts(props: {
    translations: Translations;
    context: Context;
}): LocationTexts {
    const { context, translations } = props;

    if (context === 'checkin') {
        const translation = translations.checkInSelectLocation;

        return {
            selectLocation: translation.selectACheckInLocation,
            closed: translation.closed,
            open: translation.open,
            nearest: translation.nearest,
        };
    } else {
        const translation = translations.appointmentSelectLocation;

        return {
            selectLocation: translation.selectAnAppointmentLocation,
            closed: translation.closed,
            open: translation.open,
            nearest: translation.nearest,
        };
    }
}

export default function SelectLocation() {
    const { translations, context, locations } = usePage<PageProps>().props;
    const pageTranslations = getSelectLocationTexts({ translations, context });
    const [sortedLocations, setSortedLocations] = useState<Location[] | null>(null);
    const { coords, loading } = useGeolocation({ enabled: context === 'checkin' });

    useEffect(() => {
        if (!coords) {
            return;
        }

        axios
            .post(LocationDistanceController().url, {
                latitude: coords.latitude,
                longitude: coords.longitude,
            })
            .then(({ data }) => {

                // Attach the user distance to locations and keep the same sort order
                const distanceMap = new Map(
                    data.locations.map(
                        ({
                            id,
                            distance,
                        }: {
                            id: string;
                            distance: number;
                        }) => [id, distance],
                    ),
                );

                const merged = data.locations
                    .map(({ id }: { id: string }) => {
                        const location = locations.find((l) => l.id === id);

                        return location
                            ? {
                                  ...location,
                                  distance: distanceMap.get(id) ?? null,
                              }
                            : null;
                    })
                    .filter(Boolean);

                setSortedLocations(merged);
            });
    }, [coords]);

    return (
        <PublicLayout>
            <Head title="Check-In: Select Location" />

            <div className="px-4 py-3">
                <div className="mx-auto max-w-md">
                    <h1 className="mb-6 text-3xl font-bold text-brand-grey dark:text-gray-100">
                        {pageTranslations.selectLocation}:
                    </h1>

                    <div className="space-y-3">
                        {/* Skeleton */}
                        {(loading || sortedLocations === null) &&
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
                        {sortedLocations !== null &&
                            sortedLocations.map((location, idx) => (
                                <button
                                    key={location.id}
                                    className="relative block w-full focus:outline-none"
                                    aria-label={`Check-in at ${location.name}, ${location.address}`}
                                >
                                    <div
                                        className={`flex min-h-22 items-stretch rounded-lg border p-4 text-sm font-medium shadow-sm transition ${
                                            idx === 0
                                                ? 'border-brand-green/40 bg-white dark:border-brand-green/30 dark:bg-gray-800'
                                                : 'border-gray-200 bg-white hover:border-gray-300 dark:border-gray-700/60 dark:bg-gray-800 dark:hover:border-gray-600'
                                        }`}
                                    >
                                        {/* Icon + Distance */}
                                        <div className="mr-4 flex w-10 shrink-0 flex-col items-center justify-center">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 384 512"
                                                className={`h-6 w-6 fill-current ${idx === 0 ? 'text-brand-green' : 'text-gray-400 dark:text-gray-500'}`}
                                            >
                                                <path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z" />
                                            </svg>
                                            <span
                                                aria-label={`Distance: ${location.distance} miles`}
                                                className="mt-1 min-w-13 text-center text-xs text-gray-500 dark:text-gray-400"
                                            >
                                                {location.distance != null
                                                    ? location.distance.toLocaleString(
                                                          'en-US',
                                                          {
                                                              minimumFractionDigits: 1,
                                                              maximumFractionDigits: 1,
                                                          },
                                                      ) + ' mi'
                                                    : '- mi'}
                                            </span>
                                        </div>

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

                                                {idx === 0 && (
                                                    <span
                                                        aria-label="Nearest location"
                                                        className="inline-flex rounded-full bg-green-500/10 px-2.5 py-1 text-xs font-medium text-brand-green"
                                                    >
                                                        {
                                                            pageTranslations.nearest
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
