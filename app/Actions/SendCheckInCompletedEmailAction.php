<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\RecordHistoryEvent;
use App\Mail\CheckInCompleted;
use App\Models\CheckIn;
use Illuminate\Support\Facades\Mail;

final readonly class SendCheckInCompletedEmailAction
{
    public function __construct(private RecordHistoryAction $history) {}

    /**
     * Queue the shipping-department notification for a freshly completed
     * check-in. Recipients come from the location's check-in config;
     * locations without configured addresses are silently skipped.
     */
    public function handle(CheckIn $checkIn): void
    {
        $checkIn->loadMissing(['location.address', 'purchaseOrders']);

        $recipients = $checkIn->location->checkInEmailAddresses();

        if ($recipients === []) {
            return;
        }

        Mail::to($recipients)->send(new CheckInCompleted($checkIn));

        /*
         * Recorded here rather than by the notification listener: this is a
         * Mailable, not a Notification, so nothing fires NotificationSent for it.
         * Employee-facing messages built later should be Notifications
         * implementing RecordNotification, which record themselves.
         */
        $this->history->handle(
            record: $checkIn,
            event: RecordHistoryEvent::EmployeeNotificationQueued,
            subject: 'check-in-completed',
            channel: 'mail',
            context: ['recipients' => $recipients],
        );
    }
}
