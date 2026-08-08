<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\RecordHistoryEvent;
use App\Mail\AppointmentBooked;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;

final readonly class SendAppointmentBookedEmailAction
{
    public function __construct(private RecordHistoryAction $history) {}

    /**
     * Queue the shipping-department notification for a freshly booked
     * appointment. Recipients come from the location's appointment config;
     * locations without configured addresses are silently skipped.
     */
    public function handle(Appointment $appointment): void
    {
        $appointment->loadMissing(['location.address', 'purchaseOrders']);

        $recipients = $appointment->location->appointmentEmailAddresses();

        if ($recipients === []) {
            return;
        }

        Mail::to($recipients)->send(new AppointmentBooked($appointment));

        // A Mailable, so the notification listener never sees it. See
        // SendCheckInCompletedEmailAction for the full note.
        $this->history->handle(
            record: $appointment,
            event: RecordHistoryEvent::EmployeeNotificationQueued,
            subject: 'appointment-booked',
            channel: 'mail',
            context: ['recipients' => $recipients],
        );
    }
}
