import { Head, useForm } from '@inertiajs/react';
import { format } from 'date-fns';
import React, { useEffect } from 'react';
import { DatePicker, TimePicker } from '@/components/DatePickerTime';
import { Field, FieldDescription, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import PublicLayout from '@/layouts/PublicLayout';

interface Location {
    id: string;
    name: string;
    address: string;
}

interface PageProps {
    location: Location;
}

interface AppointmentForm {
    location_id: string;
    date: string;
    time: string;
    so_number: string;
    drivers_name: string;
    cellphone: string;
}

function LocationCard({ location }: { location: Location }) {
    return (
        <div className="flex items-center gap-3 rounded-lg border border-brand-green/40 bg-brand-green/5 px-4 py-3 dark:border-brand-green/30 dark:bg-brand-green/10">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 384 512"
                className="h-4 w-4 shrink-0 fill-current text-brand-green"
            >
                <path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z" />
            </svg>
            <div>
                <p className="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    {location.name}
                </p>
                <p className="text-xs text-gray-500 dark:text-gray-400">
                    {location.address}
                </p>
            </div>
        </div>
    );
}

export default function ScheduleAppointment({ location }: PageProps) {
    const { data, setData, post, processing, errors } =
        useForm<AppointmentForm>({
            location_id: location.id,
            date: '',
            time: '',
            so_number: '',
            drivers_name: '',
            cellphone: '',
        });

    // Keep location_id in sync if location prop ever changes
    useEffect(() => {
        setData('location_id', location.id);
    }, [location.id]);

    const [selectedDate, setSelectedDate] = React.useState<Date | undefined>(
        undefined,
    );

    const handleDateChange = (d: Date | undefined) => {
        setSelectedDate(d);
        setData('date', d ? format(d, 'yyyy-MM-dd') : '');
    };

    const handlePhoneInput = (e: React.ChangeEvent<HTMLInputElement>) => {
        const input = e.target.value.replace(/\D/g, '').substring(0, 10);
        const area = input.substring(0, 3);
        const mid = input.substring(3, 6);
        const last = input.substring(6, 10);
        let formatted = '';

        if (input.length > 6) {
            formatted = `(${area}) ${mid}-${last}`;
        } else if (input.length > 3) {
            formatted = `(${area}) ${mid}`;
        } else if (input.length > 0) {
            formatted = `(${area}`;
        }

        setData('cellphone', formatted);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/appointments/review');
    };

    return (
        <PublicLayout>
            <Head title="Book an Appointment" />

            <div className="px-4 py-3">
                <div className="mx-auto max-w-md lg:max-w-xl">
                    <h1 className="mb-6 text-3xl font-bold text-brand-grey dark:text-gray-100">
                        Book an Appointment:
                    </h1>

                    <div className="space-y-3">
                        <div className="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700/60 dark:bg-gray-800">
                            <form onSubmit={handleSubmit} noValidate>

                                {/* ── Appointment Details ── */}
                                <div className="px-4 pt-4 pb-3">
                                    <p className="mb-3 text-[10px] font-bold tracking-widest text-brand-green uppercase">
                                        Appointment Details
                                    </p>

                                    {/* Location */}
                                    <div className="mb-4">
                                        <LocationCard location={location} />
                                    </div>

                                    {/* Date + Time */}
                                    <div className="mb-4 grid grid-cols-2 gap-3">
                                        <div>
                                            {/*<Field>*/}
                                            <FieldLabel
                                                htmlFor="date"
                                                className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                            >
                                                Date
                                            </FieldLabel>

                                            <DatePicker
                                                date={selectedDate}
                                                onDateChange={handleDateChange}
                                            />
                                            {errors.date && (
                                                <p className="mt-1 text-xs text-red-500">
                                                    {errors.date}
                                                </p>
                                            )}
                                            {/*</Field>*/}
                                        </div>
                                        <div>
                                            <FieldLabel className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Time
                                            </FieldLabel>
                                            <TimePicker
                                                value={data.time || '10:00:00'}
                                                onChange={(t) =>
                                                    setData('time', t)
                                                }
                                            />
                                            {errors.time && (
                                                <p className="mt-1 text-xs text-red-500">
                                                    {errors.time}
                                                </p>
                                            )}
                                        </div>
                                    </div>

                                    {/* SO Number */}
                                    <div className="mb-1">
                                        <FieldLabel
                                            htmlFor="so_number"
                                            className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                        >
                                            SO Number
                                        </FieldLabel>
                                        <Input
                                            type="text"
                                            id="so_number"
                                            value={data.so_number}
                                            onChange={(e) =>
                                                setData(
                                                    'so_number',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="e.g. SO-00123"
                                            className={`w-full rounded-md border px-3 py-2 text-sm focus:outline-none ${
                                                errors.so_number
                                                    ? 'border-red-400 dark:border-red-500'
                                                    : 'border-gray-300 dark:border-gray-600'
                                            }`}
                                        />
                                        {errors.so_number && (
                                            <p className="mt-1 text-xs text-red-500">
                                                {errors.so_number}
                                            </p>
                                        )}
                                    </div>
                                </div>

                                {/*<hr className="border-gray-200 dark:border-gray-700" />*/}

                                {/* ── Driver Information ── */}
                                <div className="px-4 pt-3 pb-4">
                                    <p className="mb-3 text-[10px] font-bold tracking-widest text-brand-green uppercase">
                                        Driver Information
                                    </p>

                                    {/* Driver Name */}
                                    <div className="mb-4">
                                        <FieldLabel
                                            htmlFor="drivers_name"
                                            className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                        >
                                            Driver's Name
                                        </FieldLabel>
                                        <Input
                                            type="text"
                                            id="drivers_name"
                                            value={data.drivers_name}
                                            onChange={(e) =>
                                                setData(
                                                    'drivers_name',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="Full name"
                                            className={`w-full rounded-md border px-3 py-2 text-sm focus:outline-none ${
                                                errors.drivers_name
                                                    ? 'border-red-400 dark:border-red-500'
                                                    : 'border-gray-300 dark:border-gray-600'
                                            }`}
                                        />
                                        {errors.drivers_name && (
                                            <p className="mt-1 text-xs text-red-500">
                                                {errors.drivers_name}
                                            </p>
                                        )}
                                    </div>

                                    {/* Cellphone */}
                                    <div className="mb-4">
                                        <FieldLabel
                                            htmlFor="cellphone"
                                            className="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                        >
                                            Cellphone Number
                                        </FieldLabel>
                                        <div className="flex">
                                            <span className="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                                +1
                                            </span>
                                            <Input
                                                type="tel"
                                                id="cellphone"
                                                value={data.cellphone}
                                                onChange={handlePhoneInput}
                                                placeholder="(555) 000-0000"
                                                maxLength={14}
                                                className={`w-full rounded-md border px-3 py-2 text-sm focus:outline-none ${
                                                    errors.cellphone
                                                        ? 'border-red-400 dark:border-red-500'
                                                        : 'border-gray-300 dark:border-gray-600'
                                                }`}
                                            />
                                        </div>
                                        {errors.cellphone && (
                                            <p className="mt-1 text-xs text-red-500">
                                                {errors.cellphone}
                                            </p>
                                        )}
                                        <p className="mt-2 text-xs text-gray-400 dark:text-gray-500">
                                            By providing your number, you
                                            consent to receive SMS and email
                                            communications. Message and data
                                            rates may apply. Reply STOP to opt
                                            out.
                                        </p>
                                    </div>

                                    {/* Submit */}
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="w-full rounded-md bg-brand-green px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-green/90 focus:ring-2 focus:ring-brand-green/50 focus:outline-none disabled:opacity-60"
                                    >
                                        {processing
                                            ? 'Submitting…'
                                            : 'Review Appointment'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
