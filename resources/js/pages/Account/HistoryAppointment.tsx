import { Head, Link, usePage } from '@inertiajs/react';
import { ChevronLeft } from 'lucide-react';

import RecordActions from '@/components/history/RecordActions';
import {
    DetailRow,
    DetailSection,
} from '@/components/history/RecordDetailCard';
import StatusBadge from '@/components/StatusBadge';
import AccountLayout from '@/layouts/AccountLayout';
import { history as accountHistory } from '@/routes/account';
import {
    email as appointmentEmail,
    pdf as appointmentPdf,
} from '@/routes/account/history/appointment';

import type { RecordTranslations } from './recordTranslations';
import { recordStatusLabel } from './recordTranslations';

interface AppointmentDetail {
    uuid: string;
    referenceNumber: string;
    status: string;
    date: string;
    time: string;
    isUpcoming: boolean;
    locationName: string;
    locationAddress: string;
    driversName: string;
    driversCellphone: string;
    cancelledAt: string | null;
    cancelledReason: string | null;
    purchaseOrders: string[];
}

interface PageProps {
    appointment: AppointmentDetail;
    translations: { accountHistoryRecord: RecordTranslations };
    [key: string]: unknown;
}

export default function HistoryAppointment() {
    const { appointment, translations } = usePage<PageProps>().props;
    const t = translations.accountHistoryRecord;

    return (
        <AccountLayout>
            <Head title={t.appointmentTitle} />

            <div className="mx-auto max-w-2xl space-y-5 px-6 pt-5 pb-12">
                <Link
                    href={
                        accountHistory({
                            query: { tab: 'appointments' },
                        }).url
                    }
                    className="inline-flex items-center gap-1 text-sm font-medium text-gray-500 transition-colors hover:text-brand-grey dark:text-gray-400 dark:hover:text-gray-200"
                >
                    <ChevronLeft className="h-4 w-4" />
                    {t.back}
                </Link>

                <div className="flex items-start justify-between gap-4">
                    <div className="min-w-0">
                        <h1 className="text-xl font-bold tracking-tight text-brand-grey dark:text-gray-50">
                            {t.appointmentTitle}
                        </h1>
                        <p className="mt-0.5 font-mono text-xs text-gray-400 dark:text-gray-500">
                            #{appointment.referenceNumber}
                        </p>
                    </div>
                    <StatusBadge
                        status={appointment.status}
                        label={recordStatusLabel(appointment.status, t)}
                    />
                </div>

                <DetailSection heading={t.locationHeading}>
                    <DetailRow label={t.date} value={appointment.date} />
                    <DetailRow label={t.time} value={appointment.time} />
                    <DetailRow
                        label={t.locationHeading}
                        value={appointment.locationName}
                    />
                    <DetailRow
                        label={t.address}
                        value={appointment.locationAddress}
                    />
                </DetailSection>

                <DetailSection heading={t.shipmentHeading}>
                    <DetailRow
                        label={t.purchaseOrders}
                        value={
                            appointment.purchaseOrders.length > 0 ? (
                                <span className="font-mono">
                                    {appointment.purchaseOrders.join(', ')}
                                </span>
                            ) : null
                        }
                    />
                </DetailSection>

                <DetailSection heading={t.driverHeading}>
                    <DetailRow
                        label={t.driverName}
                        value={appointment.driversName}
                    />
                    <DetailRow
                        label={t.driverPhone}
                        value={appointment.driversCellphone}
                    />
                </DetailSection>

                {appointment.cancelledAt !== null && (
                    <DetailSection heading={t.statusCancelled}>
                        <DetailRow
                            label={t.cancelledOn}
                            value={appointment.cancelledAt}
                        />
                        <DetailRow
                            label={t.cancellationReason}
                            value={appointment.cancelledReason}
                        />
                    </DetailSection>
                )}

                <RecordActions
                    pdfUrl={appointmentPdf(appointment.uuid).url}
                    emailUrl={appointmentEmail(appointment.uuid).url}
                    labels={{
                        viewPdf: t.viewPdf,
                        emailCopy: t.emailCopy,
                        emailSent: t.emailSent,
                    }}
                />
            </div>
        </AccountLayout>
    );
}
