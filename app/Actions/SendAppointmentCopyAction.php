<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Appointment;
use App\Models\User;
use App\Notifications\AppointmentCopy;

final readonly class SendAppointmentCopyAction
{
    /**
     * Send the driver their own copy of a booking. See
     * {@see SendCheckInCopyAction} for why guests are skipped.
     */
    public function handle(Appointment $appointment): void
    {
        $appointment->loadMissing('user');

        $user = $appointment->user;

        if (! $user instanceof User || ! $user->notify_appointment_copy) {
            return;
        }

        $user->notify(new AppointmentCopy($appointment)->locale($appointment->locale));
    }
}
