import { Head, usePage } from '@inertiajs/react';
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
    locations: [];
    translations: Translations;
    context: Context;
    [key: string]: unknown;
}

interface Location {
    id: number | string;
    locationName: string;
    address: string;
    userDistance: number;
    schedule: string;
    isOpen: boolean;
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

const STATIC_LOCATIONS: Location[] = [
    {
        id: 'eddystone-penn-terminals',
        locationName: 'Eddystone, PA - Penn Terminals',
        address: '1 Saville Avenue, Eddystone, PA 19022',
        userDistance: 155.0,
        schedule: '07:00 AM - 11:00 PM EDT',
        isOpen: false,
    },
    {
        id: 'pompano-sol-group',
        locationName: 'Pompano Beach, FL - Sol Group Marketing',
        address: '1751 SW 8th Street, Pompano Beach, FL 33069',
        userDistance: 1117.2,
        schedule: '07:00 AM - 07:00 AM EDT',
        isOpen: false,
    },
    {
        id: 'oxnard-channel-islands',
        locationName: 'Oxnard Beach, CA - Channel Islands Logistics',
        address: '5655 Arcturus Avenue, Oxnard Beach, CA 93033',
        userDistance: 2536.0,
        schedule: '07:00 AM - 07:00 AM PDT',
        isOpen: false,
    },
    {
        id: 'baytown-foremost-fresh-direct',
        locationName: 'Baytown, TX - Foremost Fresh Direct',
        address: '4203 Cedar Boulevard, Baytown, TX 77523',
        userDistance: 0,
        schedule: '07:00 AM - 07:00 AM CDT',
        isOpen: false,
    },
    {
        id: 'firebaugh-ca',
        locationName: 'Firebaugh, CA',
        address: '6879 N. Washoe Avenue, Firebaugh, CA 93622',
        userDistance: 0,
        schedule: '07:00 AM - 07:00 AM PDT',
        isOpen: false,
    },
    {
        id: 'aguila-az',
        locationName: 'Aguila, AZ',
        address: '51240 Valley Road, Aguila, AZ 85320',
        userDistance: 0,
        schedule: '07:00 AM - 07:00 AM MST',
        isOpen: false,
    },
    {
        id: 'maricopa-az',
        locationName: 'Maricopa, AZ',
        address: '9254 N. Ralston Road, Maricopa, AZ 85139',
        userDistance: 0,
        schedule: '07:00 AM - 07:00 AM MST',
        isOpen: false,
    },
    {
        id: 'tonopah-az-court-house',
        locationName: 'Tonopah, AZ - Court House',
        address: '53931 W. Lower Buckey Road, Tonopah, AZ 85354',
        userDistance: 0,
        schedule: '07:00 AM - 07:00 AM MST',
        isOpen: false,
    },
    {
        id: 'salinas-ca',
        locationName: 'Salinas, CA',
        address: '850 Work Street, Salinas, CA 93901',
        userDistance: 0,
        schedule: '07:00 AM - 07:00 AM PDT',
        isOpen: false,
    },
];

export default function SelectLocation() {
    const { translations, context, locations } = usePage<PageProps>().props;

    console.log(locations)

    // Get all context-specific texts
    const pageTranslations = getSelectLocationTexts({ translations, context });

    return (
        <PublicLayout>
            <Head title="Check-In: Select Location" />

            <div className="px-4 py-3">
                <div className="mx-auto max-w-md">
                    <h1 className="mb-6 text-3xl font-bold text-brand-grey dark:text-gray-100">
                        { pageTranslations.selectLocation }:
                    </h1>

                    <div className="space-y-3">
                        {/* Skeleton */}
                        {STATIC_LOCATIONS.length === 0 &&
                            Array.from({ length: 3 }).map((_, i) => (
                                <div
                                    key={i}
                                    className="mb-3 flex animate-pulse items-start justify-between space-x-4 rounded-lg
                                        border border-gray-200 bg-white p-4 shadow dark:border-gray-700/60
                                        dark:bg-gray-800"
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
                        {STATIC_LOCATIONS.map((location, idx) => (
                            <button
                                key={location.id}
                                className="relative block w-full focus:outline-none"
                                aria-label={`Check-in at ${location.locationName}, ${location.address}`}
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
                                            aria-label={`Distance: ${location.userDistance} miles`}
                                            className="mt-1 min-w-13 text-center text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            {location.userDistance.toLocaleString(
                                                'en-US',
                                                {
                                                    minimumFractionDigits: 1,
                                                    maximumFractionDigits: 1,
                                                },
                                            )}{' '}
                                            mi
                                        </span>
                                    </div>

                                    {/* Text */}
                                    <div className="flex flex-1 flex-col justify-center gap-0.5 text-left">
                                        <div
                                            className={`font-semibold ${idx === 0 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-800 dark:text-gray-100'}`}
                                        >
                                            {location.locationName}
                                        </div>
                                        <div className="text-sm text-gray-600 dark:text-gray-300">
                                            {location.address}
                                        </div>
                                        <div className="mt-1 flex items-center text-xs text-gray-500 dark:text-gray-400">
                                            <div className="flex items-center space-x-3">
                                                <span
                                                    aria-label={`Hours of operation: ${location.schedule}`}
                                                >
                                                    {location.schedule}
                                                </span>
                                                {location.isOpen ? (
                                                    <span
                                                        aria-label="Location is open"
                                                        className="inline-flex rounded-full bg-green-500/20 px-2.5 py-1 text-xs font-medium text-green-700"
                                                    >
                                                        {pageTranslations.open}
                                                    </span>
                                                ) : (
                                                    <span
                                                        aria-label="Location is closed"
                                                        className="inline-flex rounded-full bg-red-500/20 px-2.5 py-1 text-xs font-medium text-red-700"
                                                    >
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
                                </div>
                            </button>
                        ))}
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
