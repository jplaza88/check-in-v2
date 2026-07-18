<?php

declare(strict_types=1);

namespace App\Actions;

use App\Mail\CheckInCompleted;
use App\Models\CheckIn;
use Illuminate\Support\Facades\Mail;

final readonly class SendCheckInCompletedEmailAction
{
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
    }
}
