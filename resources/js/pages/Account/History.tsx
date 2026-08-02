import { Head, Link, usePage } from '@inertiajs/react';
import { CalendarPlus, PackageOpen, Truck } from 'lucide-react';
import type { ReactNode } from 'react';

import EmptyState from '@/components/EmptyState';
import AppointmentRow from '@/components/history/AppointmentRow';
import CheckInRow from '@/components/history/CheckInRow';
import HistoryList, { HistoryDivider } from '@/components/history/HistoryList';
import HistorySkeleton from '@/components/history/HistorySkeleton';
import HistoryTabs from '@/components/history/HistoryTabs';
import LoadMoreButton from '@/components/history/LoadMoreButton';
import PeriodChips from '@/components/history/PeriodChips';
import type {
    AppointmentRowData,
    CheckInRowData,
    HistoryPage,
} from '@/components/history/types';
import type {
    HistoryPeriodKey,
    HistoryTabKey,
} from '@/hooks/useHistoryFilters';
import { useHistoryFilters } from '@/hooks/useHistoryFilters';
import AccountLayout from '@/layouts/AccountLayout';
import appointment from '@/routes/appointment';
import checkIn from '@/routes/checkIn';

// ── Types ────────────────────────────────────────────────────────────────────

interface HistoryTranslations {
    title: string;
    subtitle: string;
    tabCheckIns: string;
    tabAppointments: string;
    period30: string;
    period90: string;
    period12m: string;
    periodAll: string;
    upcoming: string;
    past: string;
    reference: string;
    loadMore: string;
    loading: string;
    endOfList: string;
    noCheckIns: string;
    noCheckInsSubtitle: string;
    noCheckInsCta: string;
    noAppointments: string;
    noAppointmentsSubtitle: string;
    noAppointmentsCta: string;
    noResults: string;
    noResultsSubtitle: string;
    statusPending: string;
    statusCompleted: string;
    statusCancelled: string;
    statusScheduled: string;
    statusNoShow: string;
    statusCheckedIn: string;
}

interface PageProps {
    filters: { tab: HistoryTabKey; period: HistoryPeriodKey };
    history: HistoryPage;
    translations: { accountHistory: HistoryTranslations };
    [key: string]: unknown;
}

// ── Helpers ──────────────────────────────────────────────────────────────────

function statusLabel(status: string, t: HistoryTranslations): string {
    const labels: Record<string, string> = {
        pending: t.statusPending,
        completed: t.statusCompleted,
        cancelled: t.statusCancelled,
        scheduled: t.statusScheduled,
        'no-show': t.statusNoShow,
        'checked-in': t.statusCheckedIn,
    };

    return labels[status] ?? status;
}

/**
 * Appointments sort by slot date, so anything still to come sits at the top.
 * Headings are only inserted once both groups are actually present.
 */
function appointmentItems(
    rows: AppointmentRowData[],
    t: HistoryTranslations,
): ReactNode[] {
    const startsUpcoming = rows[0]?.isUpcoming === true;
    const items: ReactNode[] = [];
    let pastHeadingAdded = false;

    rows.forEach((row, index) => {
        if (index === 0 && startsUpcoming) {
            items.push(<HistoryDivider key="upcoming" label={t.upcoming} />);
        }

        if (startsUpcoming && !row.isUpcoming && !pastHeadingAdded) {
            items.push(<HistoryDivider key="past" label={t.past} />);
            pastHeadingAdded = true;
        }

        items.push(
            <AppointmentRow
                key={row.key}
                row={row}
                referenceLabel={t.reference}
                statusLabel={statusLabel(row.status, t)}
            />,
        );
    });

    return items;
}

// ── Page ─────────────────────────────────────────────────────────────────────

export default function History() {
    const { filters, history, translations } = usePage<PageProps>().props;
    const t = translations.accountHistory;

    const { loadingMore, switching, applyFilters, loadMore } =
        useHistoryFilters({
            tab: filters.tab,
            period: filters.period,
            currentPage: history.currentPage,
            hasMore: history.hasMore,
        });

    const isAppointments = filters.tab === 'appointments';
    const rows = history.data;
    const isEmpty = rows.length === 0;

    // Only shown when nothing at all exists, so a narrow date range does not
    // read as "you have never checked in".
    const isFiltered = filters.period !== 'all';

    return (
        <AccountLayout>
            <Head title={t.title} />

            <div className="mx-auto max-w-2xl space-y-4 px-6 pt-5 pb-12">
                <div>
                    <h1 className="text-xl font-bold tracking-tight text-brand-grey dark:text-gray-50">
                        {t.title}
                    </h1>
                    <p className="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                        {t.subtitle}
                    </p>
                </div>

                <HistoryTabs
                    active={filters.tab}
                    onChange={(tab) => applyFilters({ tab })}
                    tabs={[
                        { key: 'check-ins', label: t.tabCheckIns },
                        { key: 'appointments', label: t.tabAppointments },
                    ]}
                />

                <PeriodChips
                    active={filters.period}
                    onChange={(period) => applyFilters({ period })}
                    chips={[
                        { key: '30d', label: t.period30 },
                        { key: '90d', label: t.period90 },
                        { key: '12m', label: t.period12m },
                        { key: 'all', label: t.periodAll },
                    ]}
                />

                {switching ? (
                    <HistorySkeleton />
                ) : isEmpty ? (
                    isFiltered ? (
                        <EmptyState
                            icon={PackageOpen}
                            title={t.noResults}
                            subtitle={t.noResultsSubtitle}
                        />
                    ) : isAppointments ? (
                        <EmptyState
                            icon={CalendarPlus}
                            tone="brand"
                            title={t.noAppointments}
                            subtitle={t.noAppointmentsSubtitle}
                            action={
                                <Link
                                    href={appointment.selectLocation().url}
                                    className="inline-flex items-center gap-1.5 rounded-full bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-brand-green/30 transition-colors hover:bg-brand-green/90"
                                >
                                    <CalendarPlus className="h-4 w-4" />
                                    {t.noAppointmentsCta}
                                </Link>
                            }
                        />
                    ) : (
                        <EmptyState
                            icon={Truck}
                            tone="brand"
                            title={t.noCheckIns}
                            subtitle={t.noCheckInsSubtitle}
                            action={
                                <Link
                                    href={checkIn.selectLocation().url}
                                    className="inline-flex items-center gap-1.5 rounded-full bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-brand-green/30 transition-colors hover:bg-brand-green/90"
                                >
                                    <Truck className="h-4 w-4" />
                                    {t.noCheckInsCta}
                                </Link>
                            }
                        />
                    )
                ) : (
                    <HistoryList>
                        {isAppointments
                            ? appointmentItems(rows as AppointmentRowData[], t)
                            : (rows as CheckInRowData[]).map((row) => (
                                  <CheckInRow
                                      key={row.key}
                                      row={row}
                                      referenceLabel={t.reference}
                                      statusLabel={statusLabel(row.status, t)}
                                  />
                              ))}
                    </HistoryList>
                )}

                {!switching && !isEmpty && (
                    <LoadMoreButton
                        hasMore={history.hasMore}
                        loading={loadingMore}
                        isEmpty={isEmpty}
                        labels={{
                            loadMore: t.loadMore,
                            loading: t.loading,
                            endOfList: t.endOfList,
                        }}
                        onClick={loadMore}
                    />
                )}
            </div>
        </AccountLayout>
    );
}
