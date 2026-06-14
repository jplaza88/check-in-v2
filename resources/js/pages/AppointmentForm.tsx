
import { zodResolver } from '@hookform/resolvers/zod';
import { Head, router } from '@inertiajs/react';
import { format } from 'date-fns';
import { UserIcon } from 'lucide-react';
import { useMemo, useState } from 'react';
import { Controller, useForm } from 'react-hook-form';
import { z } from 'zod';

import { DatePicker, TimePicker } from '@/components/DatePickerTime';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Button } from '@/components/ui/button';
import {
    Field,
    FieldError,
    FieldGroup,
    FieldLabel,
    FieldDescription,
} from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import PublicLayout from '@/layouts/PublicLayout';

// Adjust to your Wayfinder generated routes
//import { route } from '@/routes';

// ── Types ────────────────────────────────────────────────────────────────────

interface AvailabilityWindow {
    date: string;
    firstSlot: string;
    lastSlot: string;
}

interface Location {
    id: string;
    name: string;
    address: string;
    isOpen: boolean;
    todayOpenCloseTime: string | null;
    reason: string | null;
    hasOverride: boolean;
    isOverrideClosure: boolean;
    isClosingSoon: boolean | null;
    availability: AvailabilityWindow[];
    slotIntervalMinutes: number;
}

interface PageProps {
    location: Location;
}

// ── Schema ───────────────────────────────────────────────────────────────────

const schema = z.object({
    location_id: z.string(),
    date: z.string().min(1, 'Please select a date.'),
    time: z.string().min(1, 'Please select a time.'),
    so_number: z
        .string()
        .min(1, 'SO Number is required.')
        .regex(/^SO-\d+$/i, 'Must be in the format SO-00123.'),
    drivers_name: z
        .string()
        .min(2, "Driver's name must be at least 2 characters.")
        .max(100, 'Name is too long.'),
    cellphone: z
        .string()
        .regex(
            /^\(\d{3}\) \d{3}-\d{4}$/,
            'Enter a valid 10-digit US phone number.',
        ),
});

type AppointmentForm = z.infer<typeof schema>;

// ── Step badge ───────────────────────────────────────────────────────────────

function StepBadge({ number }: { number: number }) {
    return (
        <span className="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-brand-green text-[10px] font-bold text-white">
            {number}
        </span>
    );
}

// ── Section header ───────────────────────────────────────────────────────────

function SectionHeader({ step, label }: { step: number; label: string }) {
    return (
        <div className="flex items-center gap-2.5">
            <StepBadge number={step} />
            <span className="text-xs font-bold tracking-widest text-brand-green uppercase">
                {label}
            </span>
        </div>
    );
}

// ── Location + schedule card ─────────────────────────────────────────────────

function LocationScheduleCard({ location }: { location: Location }) {
    return (
        <div className="border-gray-150/50 overflow-hidden rounded-lg border bg-gray-100/50 dark:border-gray-900/50 dark:bg-gray-950/30">
            {/* Location row */}
            <div className="relative flex items-start gap-3 overflow-hidden px-4 py-3">
                <span className="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-gray-100/70 dark:bg-gray-900/40">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 384 512"
                        className="h-3.5 w-3.5 fill-current text-brand-green"
                    >
                        <path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z" />
                    </svg>
                </span>
                <div className="min-w-0 flex-1">
                    <p className="text-sm leading-tight font-semibold text-gray-900 dark:text-gray-100">
                        {location.name}
                    </p>
                    <p className="mt-1 truncate text-xs leading-tight text-gray-500 dark:text-gray-400">
                        {location.address}
                    </p>
                </div>

                <a
                    href={`https://maps.google.com/?q=${encodeURIComponent(location.address)}`}
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Open in Maps"
                    className="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-md text-gray-400 transition-colors hover:bg-gray-200/60 hover:text-gray-600 dark:text-gray-500 dark:hover:bg-gray-700/40 dark:hover:text-gray-300"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        className="h-3.5 w-3.5"
                    >
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                        <polyline points="15 3 21 3 21 9" />
                        <line x1="10" y1="14" x2="21" y2="3" />
                    </svg>
                </a>
            </div>

            {/* Today's hours */}
            <Accordion
                type="single"
                collapsible={true}
                defaultValue="schedule"
                className="rounded-none border-0"
            >
                <AccordionItem
                    value="schedule"
                    className="border-0 data-[state=open]:bg-transparent"
                >
                    <AccordionTrigger className="cursor-pointer py-2.5 pr-4.5 pl-14 text-xs font-semibold text-gray-600 hover:bg-transparent hover:no-underline data-[state=open]:bg-transparent dark:text-gray-400">
                        Today's Hours
                    </AccordionTrigger>
                    <AccordionContent>
                        <div className="flex items-start justify-between gap-4 py-2 pr-4 pl-11">
                            <div className="flex shrink-0 items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                {location.todayOpenCloseTime && (
                                    <span>{location.todayOpenCloseTime}</span>
                                )}
                                {location.isOpen && (
                                    <span className="inline-flex rounded-full bg-green-500/10 px-2.5 py-1 text-xs font-medium text-brand-green">
                                        Open
                                    </span>
                                )}
                            </div>
                            {location.hasOverride && location.reason && (
                                <span
                                    className={`self-start rounded-full px-2.5 py-1 text-xs font-medium ${
                                        location.isOverrideClosure
                                            ? 'bg-red-500/15 text-red-700 dark:text-red-400'
                                            : 'bg-amber-500/15 text-amber-700 dark:text-amber-400'
                                    }`}
                                >
                                    {location.reason}
                                </span>
                            )}
                        </div>
                    </AccordionContent>
                </AccordionItem>
            </Accordion>
        </div>
    );
}

// ── Page ─────────────────────────────────────────────────────────────────────

export default function ScheduleAppointment({ location }: PageProps) {
    const [processing, setProcessing] = useState(false);

    const availability = location.availability ?? [];
    const hasAvailability = availability.length > 0;

    const availableDates = useMemo(
        () => new Set(availability.map((w) => w.date)),
        [availability],
    );
    const windowByDate = useMemo(
        () => new Map(availability.map((w) => [w.date, w])),
        [availability],
    );

    const firstWindow = availability[0];

    const { control, handleSubmit, setError, watch, setValue } =
        useForm<AppointmentForm>({
            resolver: zodResolver(schema),
            defaultValues: {
                location_id: location.id,
                date: firstWindow?.date ?? '',
                time: firstWindow?.firstSlot ?? '10:00:00',
                so_number: '',
                drivers_name: '',
                cellphone: '',
            },
        });

    const selectedDate = watch('date');
    const currentWindow = selectedDate
        ? windowByDate.get(selectedDate)
        : undefined;

    const formatPhone = (raw: string) => {
        const digits = raw.replace(/\D/g, '').substring(0, 10);
        const area = digits.substring(0, 3);
        const mid = digits.substring(3, 6);
        const last = digits.substring(6, 10);

        if (digits.length > 6) {
return `(${area}) ${mid}-${last}`;
}

        if (digits.length > 3) {
return `(${area}) ${mid}`;
}

        if (digits.length > 0) {
return `(${area}`;
}

        return '';
    };

    const onSubmit = (values: AppointmentForm) => {
        setProcessing(true);
        /*router.post(router('appointments.review'), values, {
            onError: (backendErrors) => {
                (
                    Object.keys(backendErrors) as Array<keyof AppointmentForm>
                ).forEach((field) => {
                    setError(field, {
                        type: 'server',
                        message: backendErrors[field],
                    });
                });
            },
            onFinish: () => setProcessing(false),
        });*/
    };

    return (
        <PublicLayout>
            <Head title="Book an Appointment" />

            <div className="px-4 py-6 sm:py-10">
                <div className="mx-auto max-w-md lg:max-w-xl">
                    {/* Page title */}
                    <div className="mb-6">
                        <p className="mb-1 text-[10px] font-bold tracking-widest text-brand-green uppercase">
                            New Appointment
                        </p>
                        <h1 className="text-2xl font-bold text-brand-grey dark:text-gray-100">
                            Book an Appointment
                        </h1>
                    </div>

                    {/* Card */}
                    <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700/60 dark:bg-gray-800">
                        {/* Green accent bar at top */}
                        <div className="h-1 w-full bg-brand-green" />

                        <form onSubmit={handleSubmit(onSubmit)} noValidate>
                            {/* ── Section 1: Appointment Details ───────── */}
                            <div className="px-5 pt-5 pb-6">
                                <SectionHeader
                                    step={1}
                                    label="Appointment Details"
                                />

                                {/* Location + today's hours */}
                                <div className="mt-4">
                                    <LocationScheduleCard location={location} />
                                </div>

                                {/* Date + Time */}
                                {hasAvailability ? (
                                    <div className="mt-5 space-y-5">
                                        <Controller
                                            name="date"
                                            control={control}
                                            render={({ field, fieldState }) => (
                                                <Field
                                                    data-invalid={
                                                        fieldState.invalid
                                                    }
                                                >
                                                    <FieldLabel htmlFor="date">
                                                        Date
                                                    </FieldLabel>
                                                    <DatePicker
                                                        availableDates={
                                                            availableDates
                                                        }
                                                        date={
                                                            field.value
                                                                ? new Date(
                                                                      field.value +
                                                                          'T00:00:00',
                                                                  )
                                                                : undefined
                                                        }
                                                        onDateChange={(d) => {
                                                            const ds = d
                                                                ? format(
                                                                      d,
                                                                      'yyyy-MM-dd',
                                                                  )
                                                                : '';
                                                            field.onChange(ds);
                                                            const w =
                                                                windowByDate.get(
                                                                    ds,
                                                                );
                                                            if (w) {
                                                                setValue(
                                                                    'time',
                                                                    w.firstSlot,
                                                                    {
                                                                        shouldValidate:
                                                                            true,
                                                                    },
                                                                );
                                                            }
                                                        }}
                                                    />
                                                    {fieldState.invalid && (
                                                        <FieldError
                                                            errors={[
                                                                fieldState.error,
                                                            ]}
                                                        />
                                                    )}
                                                </Field>
                                            )}
                                        />

                                        <Controller
                                            name="time"
                                            control={control}
                                            render={({ field, fieldState }) => (
                                                <Field
                                                    data-invalid={
                                                        fieldState.invalid
                                                    }
                                                >
                                                    <FieldLabel>
                                                        Time
                                                    </FieldLabel>
                                                    <TimePicker
                                                        value={field.value}
                                                        onChange={
                                                            field.onChange
                                                        }
                                                        firstSlot={
                                                            currentWindow?.firstSlot
                                                        }
                                                        lastSlot={
                                                            currentWindow?.lastSlot
                                                        }
                                                        intervalMinutes={
                                                            location.slotIntervalMinutes
                                                        }
                                                    />
                                                    {fieldState.invalid && (
                                                        <FieldError
                                                            errors={[
                                                                fieldState.error,
                                                            ]}
                                                        />
                                                    )}
                                                </Field>
                                            )}
                                        />
                                    </div>
                                ) : (
                                    <div className="mt-5 rounded-lg border border-amber-300/60 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">
                                        No appointment times are currently
                                        available. Please check back later or
                                        contact the location.
                                    </div>
                                )}

                                {/* SO Number */}
                                <div className="mt-4">
                                    <Controller
                                        name="so_number"
                                        control={control}
                                        render={({ field, fieldState }) => (
                                            <Field
                                                data-invalid={
                                                    fieldState.invalid
                                                }
                                            >
                                                <FieldLabel htmlFor="so_number">
                                                    SO Number
                                                </FieldLabel>
                                                <Input
                                                    {...field}
                                                    id="so_number"
                                                    aria-invalid={
                                                        fieldState.invalid
                                                    }
                                                    placeholder="e.g. SO-00123"
                                                />
                                                {fieldState.invalid && (
                                                    <FieldError
                                                        errors={[
                                                            fieldState.error,
                                                        ]}
                                                    />
                                                )}
                                            </Field>
                                        )}
                                    />
                                </div>
                            </div>

                            {/* Divider */}
                            <div className="mx-5 border-t border-dashed border-gray-200 dark:border-gray-700" />

                            {/* ── Section 2: Driver Information ─────────── */}
                            <div className="px-5 pt-5 pb-5">
                                <SectionHeader
                                    step={2}
                                    label="Driver Information"
                                />

                                <FieldGroup className="mt-4">
                                    {/* Driver Name */}
                                    <Controller
                                        name="drivers_name"
                                        control={control}
                                        render={({ field, fieldState }) => (
                                            <Field
                                                data-invalid={
                                                    fieldState.invalid
                                                }
                                            >
                                                <FieldLabel htmlFor="drivers_name">
                                                    Driver's Name
                                                </FieldLabel>
                                                <div className="relative">
                                                    <UserIcon className="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                                    <Input
                                                        {...field}
                                                        id="drivers_name"
                                                        aria-invalid={
                                                            fieldState.invalid
                                                        }
                                                        placeholder="Full name"
                                                        className="pl-9"
                                                    />
                                                </div>
                                                {fieldState.invalid && (
                                                    <FieldError
                                                        errors={[
                                                            fieldState.error,
                                                        ]}
                                                    />
                                                )}
                                            </Field>
                                        )}
                                    />

                                    {/* Cellphone */}
                                    <Controller
                                        name="cellphone"
                                        control={control}
                                        render={({ field, fieldState }) => (
                                            <Field
                                                data-invalid={
                                                    fieldState.invalid
                                                }
                                            >
                                                <FieldLabel htmlFor="cellphone">
                                                    Cellphone Number
                                                </FieldLabel>
                                                <div className="flex rounded-md shadow-xs">
                                                    <span className="inline-flex items-center rounded-l-md border border-r-0 border-input bg-muted px-3 text-sm text-muted-foreground dark:bg-input/30">
                                                        +1
                                                    </span>
                                                    <Input
                                                        id="cellphone"
                                                        type="tel"
                                                        value={field.value}
                                                        onChange={(e) =>
                                                            field.onChange(
                                                                formatPhone(
                                                                    e.target
                                                                        .value,
                                                                ),
                                                            )
                                                        }
                                                        onBlur={field.onBlur}
                                                        name={field.name}
                                                        ref={field.ref}
                                                        aria-invalid={
                                                            fieldState.invalid
                                                        }
                                                        placeholder="(555) 000-0000"
                                                        maxLength={14}
                                                        className="rounded-l-none"
                                                    />
                                                </div>
                                                <FieldDescription>
                                                    By providing your number,
                                                    you consent to receive SMS
                                                    and email communications.
                                                    Reply STOP to opt out.
                                                </FieldDescription>
                                                {fieldState.invalid && (
                                                    <FieldError
                                                        errors={[
                                                            fieldState.error,
                                                        ]}
                                                    />
                                                )}
                                            </Field>
                                        )}
                                    />
                                </FieldGroup>
                            </div>

                            {/* ── Submit footer ─────────────────────────── */}
                            <div className="border-t border-gray-100 bg-gray-50 px-5 py-4 dark:border-gray-700/60 dark:bg-gray-800/60">
                                <Button
                                    type="submit"
                                    disabled={processing || !hasAvailability}
                                    className="w-full bg-brand-green text-white hover:bg-brand-green/90 focus-visible:ring-brand-green/50"
                                >
                                    {processing
                                        ? 'Submitting…'
                                        : 'Review Appointment →'}
                                </Button>
                                <p className="mt-2.5 text-center text-[11px] text-gray-400 dark:text-gray-500">
                                    You'll be able to review before confirming.
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
