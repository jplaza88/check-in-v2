
import { zodResolver } from '@hookform/resolvers/zod';
import { Head, router, usePage } from '@inertiajs/react';
import { format } from 'date-fns';
import { UserIcon } from 'lucide-react';
import { useMemo, useState } from 'react';
import { Controller, useFieldArray, useForm } from 'react-hook-form';
import { z } from 'zod';

import AlertBanner from '@/components/AlertBanner';
import { DatePicker, TimePicker } from '@/components/DatePickerTime';
import PurchaseOrderInputs, {
    type PurchaseOrderTranslations,
    poNumberSchema,
} from '@/components/PurchaseOrderInputs';
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

interface AppointmentFormTranslations {
    pageTitle: string;
    newAppointment: string;
    appointmentDetails: string;
    driverInformation: string;
    dateLabel: string;
    dateRequired: string;
    timeLabel: string;
    timeRequired: string;
    driversNameLabel: string;
    driversNamePlaceholder: string;
    driversNameMin: string;
    driversNameMax: string;
    cellphoneLabel: string;
    cellphonePlaceholder: string;
    cellphoneInvalid: string;
    cellphoneConsent: string;
    todaysHours: string;
    noAvailability: string;
    reviewButton: string;
    submitButton: string;
    submitting: string;
    reviewNote: string;
    reviewTitle: string;
    editButton: string;
    open: string;
}

interface Translations {
    appointmentForm: AppointmentFormTranslations;
    purchaseOrders: PurchaseOrderTranslations;
}

interface PageProps {
    location: Location;
    translations: Translations;
    [key: string]: unknown;
}

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

function LocationScheduleCard({
    location,
    todaysHoursLabel,
    openLabel,
}: {
    location: Location;
    openLabel: string;
    todaysHoursLabel: string;
}) {
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
                        {todaysHoursLabel}
                    </AccordionTrigger>
                    <AccordionContent>
                        <div className="flex items-start justify-between gap-4 py-2 pr-4 pl-11">
                            <div className="flex shrink-0 items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                {location.todayOpenCloseTime && (
                                    <span>{location.todayOpenCloseTime}</span>
                                )}
                                {location.isOpen && (
                                    <span className="inline-flex rounded-full bg-green-500/10 px-2.5 py-1 text-xs font-medium text-brand-green">
                                        {openLabel}
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

// ── Review summary ──────────────────────────────────────────────────────────

function ReviewSummary({
    location,
    values,
    t,
    poTranslations,
}: {
    location: Location;
    values: {
        date: string;
        time: string;
        po_numbers: { value: string }[];
        drivers_name: string;
        drivers_cellphone: string;
    };
    t: AppointmentFormTranslations;
    poTranslations: PurchaseOrderTranslations;
}) {
    const formattedDate = values.date
        ? format(new Date(values.date + 'T00:00:00'), 'MMMM d, yyyy')
        : '';

    const formattedTime = values.time
        ? format(new Date(`2000-01-01T${values.time}`), 'h:mm a')
        : '';

    return (
        <div className="space-y-5 px-5 pt-5 pb-6">
            <SectionHeader step={1} label={t.reviewTitle} />

            <div className="space-y-4">
                <div className="border-gray-150/50 overflow-hidden rounded-lg border bg-gray-100/50 dark:border-gray-900/50 dark:bg-gray-950/30">
                    <dl className="divide-y divide-gray-200 dark:divide-gray-700/60">
                        <div className="flex justify-between gap-4 px-4 py-3">
                            <dt className="text-xs font-medium text-gray-500 dark:text-gray-400">
                                {t.appointmentDetails}
                            </dt>
                            <dd className="text-right text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {location.name}
                            </dd>
                        </div>
                        <div className="flex justify-between gap-4 px-4 py-3">
                            <dt className="text-xs font-medium text-gray-500 dark:text-gray-400">
                                {t.dateLabel}
                            </dt>
                            <dd className="text-sm text-gray-900 dark:text-gray-100">
                                {formattedDate}
                            </dd>
                        </div>
                        <div className="flex justify-between gap-4 px-4 py-3">
                            <dt className="text-xs font-medium text-gray-500 dark:text-gray-400">
                                {t.timeLabel}
                            </dt>
                            <dd className="text-sm text-gray-900 dark:text-gray-100">
                                {formattedTime}
                            </dd>
                        </div>
                        <div className="flex justify-between gap-4 px-4 py-3">
                            <dt className="text-xs font-medium text-gray-500 dark:text-gray-400">
                                {poTranslations.label}
                            </dt>
                            <dd className="text-right text-sm text-gray-900 dark:text-gray-100">
                                {values.po_numbers
                                    .map((po) => po.value)
                                    .join(', ')}
                            </dd>
                        </div>
                        <div className="flex justify-between gap-4 px-4 py-3">
                            <dt className="text-xs font-medium text-gray-500 dark:text-gray-400">
                                {t.driversNameLabel}
                            </dt>
                            <dd className="text-sm text-gray-900 dark:text-gray-100">
                                {values.drivers_name}
                            </dd>
                        </div>
                        <div className="flex justify-between gap-4 px-4 py-3">
                            <dt className="text-xs font-medium text-gray-500 dark:text-gray-400">
                                {t.cellphoneLabel}
                            </dt>
                            <dd className="text-sm text-gray-900 dark:text-gray-100">
                                +1 {values.drivers_cellphone}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    );
}

// ── Page ─────────────────────────────────────────────────────────────────────

export default function ScheduleAppointment({ location }: PageProps) {
    const { translations } = usePage<PageProps>().props;
    const t = translations.appointmentForm;
    const poT = translations.purchaseOrders;

    const [processing, setProcessing] = useState(false);
    const [step, setStep] = useState<'form' | 'review'>('form');
    const [serverError, setServerError] = useState<string | null>(null);

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

    const schema = useMemo(
        () =>
            z.object({
                location_id: z.string(),
                date: z.string().min(1, t.dateRequired),
                time: z.string().min(1, t.timeRequired),
                po_numbers: poNumberSchema(poT),
                drivers_name: z
                    .string()
                    .min(2, t.driversNameMin)
                    .max(100, t.driversNameMax),
                drivers_cellphone: z
                    .string()
                    .regex(/^\(\d{3}\) \d{3}-\d{4}$/, t.cellphoneInvalid),
            }),
        [t, poT],
    );

    type FormValues = z.infer<typeof schema>;

    const { control, handleSubmit, setError, watch, setValue, getValues } =
        useForm<FormValues>({
            resolver: zodResolver(schema),
            defaultValues: {
                location_id: location.id,
                date: firstWindow?.date ?? '',
                time: firstWindow?.firstSlot ?? '10:00:00',
                po_numbers: [{ value: '' }],
                drivers_name: '',
                drivers_cellphone: '',
            },
        });

    const { fields, append, remove } = useFieldArray({
        control,
        name: 'po_numbers',
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

    const onReview = () => {
        handleSubmit(() => setStep('review'))();
    };

    const formFieldKeys: Array<keyof FormValues> = [
        'drivers_name',
        'drivers_cellphone',
        'date',
        'time',
    ];

    const onSubmit = () => {
        const values = getValues();
        setProcessing(true);
        setServerError(null);

        const payload = {
            datetime: `${values.date} ${values.time}`,
            po_numbers: values.po_numbers.map((po) => po.value),
            drivers_name: values.drivers_name,
            drivers_cellphone: values.drivers_cellphone,
        };

        router.post(`/appointment/${location.id}/book`, payload, {
            onError: (backendErrors) => {
                setStep('form');
                const unmappedErrors: string[] = [];

                Object.entries(backendErrors).forEach(([key, message]) => {
                    const poMatch = key.match(/^po_numbers\.(\d+)$/);
                    if (poMatch) {
                        setError(
                            `po_numbers.${poMatch[1]}.value` as any,
                            { type: 'server', message },
                        );
                    } else if (
                        formFieldKeys.includes(key as keyof FormValues)
                    ) {
                        setError(key as keyof FormValues, {
                            type: 'server',
                            message,
                        });
                    } else {
                        unmappedErrors.push(message);
                    }
                });

                if (unmappedErrors.length > 0) {
                    setServerError(unmappedErrors.join(' '));
                }
            },
            onFinish: () => setProcessing(false),
        });
    };

    return (
        <PublicLayout>
            <Head title={t.pageTitle} />

            <div className="px-4 py-6 sm:py-10">
                <div className="mx-auto max-w-md lg:max-w-xl">
                    {/* Page title */}
                    <div className="mb-6">
                        <p className="mb-1 text-[10px] font-bold tracking-widest text-brand-green uppercase">
                            {t.newAppointment}
                        </p>
                        <h1 className="text-2xl font-bold text-brand-grey dark:text-gray-100">
                            {t.pageTitle}
                        </h1>
                    </div>

                    {/* Card */}
                    <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700/60 dark:bg-gray-800">
                        {/* Green accent bar at top */}
                        <div className="h-1 w-full bg-brand-green" />

                        {step === 'form' ? (
                            <form
                                onSubmit={(e) => {
                                    e.preventDefault();
                                    onReview();
                                }}
                                noValidate
                            >
                                {/* ── Section 1: Appointment Details ───────── */}
                                <div className="px-5 pt-5 pb-6">
                                    <SectionHeader
                                        step={1}
                                        label={t.appointmentDetails}
                                    />

                                    {serverError && (
                                        <div className="mt-4">
                                            <AlertBanner
                                                type="error"
                                                open={!!serverError}
                                                onClose={() =>
                                                    setServerError(null)
                                                }
                                            >
                                                {serverError}
                                            </AlertBanner>
                                        </div>
                                    )}

                                    {/* Location + today's hours */}
                                    <div className="mt-4">
                                        <LocationScheduleCard
                                            location={location}
                                            todaysHoursLabel={t.todaysHours}
                                            openLabel={t.open}
                                        />
                                    </div>

                                    {/* Date + Time */}
                                    {hasAvailability ? (
                                        <div className="mt-5 space-y-5">
                                            <Controller
                                                name="date"
                                                control={control}
                                                render={({
                                                    field,
                                                    fieldState,
                                                }) => (
                                                    <Field
                                                        data-invalid={
                                                            fieldState.invalid
                                                        }
                                                    >
                                                        <FieldLabel htmlFor="date">
                                                            {t.dateLabel}
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
                                                            onDateChange={(
                                                                d,
                                                            ) => {
                                                                const ds = d
                                                                    ? format(
                                                                          d,
                                                                          'yyyy-MM-dd',
                                                                      )
                                                                    : '';
                                                                field.onChange(
                                                                    ds,
                                                                );
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
                                                render={({
                                                    field,
                                                    fieldState,
                                                }) => (
                                                    <Field
                                                        data-invalid={
                                                            fieldState.invalid
                                                        }
                                                    >
                                                        <FieldLabel>
                                                            {t.timeLabel}
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
                                            {t.noAvailability}
                                        </div>
                                    )}

                                    {/* PO Numbers */}
                                    <div className="mt-4">
                                        <PurchaseOrderInputs
                                            control={control}
                                            fields={fields}
                                            append={append}
                                            remove={remove}
                                            translations={poT}
                                        />
                                    </div>
                                </div>

                                {/* Divider */}
                                <div className="mx-5 border-t border-dashed border-gray-200 dark:border-gray-700" />

                                {/* ── Section 2: Driver Information ─────────── */}
                                <div className="px-5 pt-5 pb-5">
                                    <SectionHeader
                                        step={2}
                                        label={t.driverInformation}
                                    />

                                    <FieldGroup className="mt-4">
                                        {/* Driver Name */}
                                        <Controller
                                            name="drivers_name"
                                            control={control}
                                            render={({
                                                field,
                                                fieldState,
                                            }) => (
                                                <Field
                                                    data-invalid={
                                                        fieldState.invalid
                                                    }
                                                >
                                                    <FieldLabel htmlFor="drivers_name">
                                                        {t.driversNameLabel}
                                                    </FieldLabel>
                                                    <div className="relative">
                                                        <UserIcon className="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                                        <Input
                                                            {...field}
                                                            id="drivers_name"
                                                            aria-invalid={
                                                                fieldState.invalid
                                                            }
                                                            placeholder={
                                                                t.driversNamePlaceholder
                                                            }
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
                                            name="drivers_cellphone"
                                            control={control}
                                            render={({
                                                field,
                                                fieldState,
                                            }) => (
                                                <Field
                                                    data-invalid={
                                                        fieldState.invalid
                                                    }
                                                >
                                                    <FieldLabel htmlFor="drivers_cellphone">
                                                        {t.cellphoneLabel}
                                                    </FieldLabel>
                                                    <div className="flex rounded-4xl shadow-xs">
                                                        <span className="inline-flex items-center rounded-l-4xl border border-r-0 border-input bg-muted px-3 text-sm text-muted-foreground dark:bg-input/30">
                                                            +1
                                                        </span>
                                                        <Input
                                                            id="drivers_cellphone"
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
                                                            onBlur={
                                                                field.onBlur
                                                            }
                                                            name={field.name}
                                                            ref={field.ref}
                                                            aria-invalid={
                                                                fieldState.invalid
                                                            }
                                                            placeholder={
                                                                t.cellphonePlaceholder
                                                            }
                                                            maxLength={14}
                                                            className="rounded-l-none"
                                                        />
                                                    </div>
                                                    <FieldDescription>
                                                        {t.cellphoneConsent}
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
                                        disabled={!hasAvailability}
                                        className="w-full bg-brand-green text-white hover:bg-brand-green/90 focus-visible:ring-brand-green/50"
                                    >
                                        {t.reviewButton}
                                    </Button>
                                    <p className="mt-2.5 text-center text-[11px] text-gray-400 dark:text-gray-500">
                                        {t.reviewNote}
                                    </p>
                                </div>
                            </form>
                        ) : (
                            <div>
                                <ReviewSummary
                                    location={location}
                                    values={getValues()}
                                    t={t}
                                    poTranslations={poT}
                                />

                                {/* ── Review footer ──────────────────────── */}
                                <div className="border-t border-gray-100 bg-gray-50 px-5 py-4 dark:border-gray-700/60 dark:bg-gray-800/60">
                                    <div className="flex gap-3">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            className="flex-1"
                                            onClick={() => setStep('form')}
                                            disabled={processing}
                                        >
                                            {t.editButton}
                                        </Button>
                                        <Button
                                            type="button"
                                            className="flex-1 bg-brand-green text-white hover:bg-brand-green/90 focus-visible:ring-brand-green/50"
                                            onClick={onSubmit}
                                            disabled={processing}
                                        >
                                            {processing
                                                ? t.submitting
                                                : t.submitButton}
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
