<?php

declare(strict_types=1);

namespace App\Account;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\CheckIn;
use App\Models\User;
use Illuminate\Support\Facades\Date;

final class OverviewResolver
{
    /**
     * @return array{
     *     nextAppointment: array{referenceNumber: string, locationName: string, monthShort: string, day: string, time: string}|null,
     *     recentCheckIns: list<array{referenceNumber: string, locationName: string, date: string, status: string}>
     *         }
     */
    public function resolve(User $user): array
    {
        return [
            'nextAppointment' => $this->nextAppointment($user),
            'recentCheckIns' => $this->recentCheckIns($user),
        ];
    }

    /**
     * @return array{referenceNumber: string, locationName: string, monthShort: string, day: string, time: string}|null
     */
    private function nextAppointment(User $user): ?array
    {
        $appointment = Appointment::with('location')
            ->where('user_id', $user->id)
            ->where('status', AppointmentStatus::Scheduled)
            ->where('scheduled_for', '>=', Date::now())
            ->orderBy('scheduled_for')
            ->first();

        if (! $appointment instanceof Appointment) {
            return null;
        }

        $scheduledFor = $appointment->scheduled_for->setTimezone($appointment->location->timezone);

        return [
            'referenceNumber' => $appointment->reference_number,
            'locationName' => $appointment->location->name,
            'monthShort' => $scheduledFor->settings(['locale' => app()->getLocale()])->isoFormat('MMM'),
            'day' => $scheduledFor->format('j'),
            'time' => $scheduledFor->format('g:i A'),
        ];
    }

    /**
     * @return list<array{referenceNumber: string, locationName: string, date: string, status: string}>
     */
    private function recentCheckIns(User $user): array
    {
        return array_values(
            CheckIn::with('location')
                ->where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn (CheckIn $checkIn): array => [
                    'referenceNumber' => $checkIn->reference_number,
                    'locationName' => $checkIn->location->name,
                    'date' => $checkIn->created_at->setTimezone($checkIn->location->timezone)->format('M j, Y'),
                    'status' => $checkIn->status->value,
                ])
                ->all()
        );
    }
}
