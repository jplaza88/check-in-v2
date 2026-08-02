<?php

declare(strict_types=1);

namespace App\Account;

use App\Enums\HistoryTab;
use App\Models\Appointment;
use App\Models\CheckIn;
use App\Models\User;
use App\Queries\DriverAppointments;
use App\Queries\DriverCheckIns;

final readonly class HistoryResolver
{
    public function __construct(
        private DriverCheckIns $checkIns,
        private DriverAppointments $appointments,
    ) {}

    /**
     * @return array{data: list<array<string, mixed>>, currentPage: int, hasMore: bool}
     */
    public function resolve(User $user, HistoryFilters $filters): array
    {
        $page = match ($filters->tab) {
            HistoryTab::CheckIns => $this->checkIns->execute($user, $filters->since()),
            HistoryTab::Appointments => $this->appointments->execute($user, $filters->since()),
        };

        // items() is read straight off the union rather than passed to a helper:
        // Paginator's TValue is invariant, so Paginator<CheckIn> is not a
        // Paginator<CheckIn|Appointment> and no shared signature would accept both.
        $rows = array_values(array_map(
            fn (CheckIn|Appointment $record): array => $record instanceof CheckIn
                ? $this->checkInRow($record)
                : $this->appointmentRow($record),
            $page->items(),
        ));

        return [
            'data' => $rows,
            'currentPage' => $page->currentPage(),
            'hasMore' => $page->hasMorePages(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function checkInRow(CheckIn $checkIn): array
    {
        $when = $checkIn->created_at->setTimezone($checkIn->location->timezone);

        return [
            'key' => $checkIn->uuid,
            'uuid' => $checkIn->uuid,
            'referenceNumber' => $checkIn->reference_number,
            'locationName' => $checkIn->location->name,
            'customer' => $checkIn->customer,
            'date' => $when->format('M j, Y'),
            'status' => $checkIn->status->value,
            'href' => route('account.history.checkIn', $checkIn),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function appointmentRow(Appointment $appointment): array
    {
        $when = $appointment->scheduled_for->setTimezone($appointment->location->timezone);

        return [
            'key' => $appointment->uuid,
            'uuid' => $appointment->uuid,
            'referenceNumber' => $appointment->reference_number,
            'locationName' => $appointment->location->name,
            'date' => $when->format('M j, Y'),
            'time' => $when->format('g:i A T'),
            'monthShort' => $when->settings(['locale' => app()->getLocale()])->isoFormat('MMM'),
            'day' => $when->format('j'),
            'status' => $appointment->status->value,
            'isUpcoming' => $appointment->scheduled_for->isFuture(),
            'href' => route('account.history.appointment', $appointment),
        ];
    }
}
