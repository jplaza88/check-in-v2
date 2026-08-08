<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\CheckIn;
use App\Models\User;
use App\Notifications\CheckInCopy;

final readonly class SendCheckInCopyAction
{
    /**
     * Send the driver their own copy of a check-in, on whichever channels they
     * have chosen.
     *
     * Guests get nothing: no form collects an email address or a phone number
     * from them, so there is nowhere to send it and no preference to read.
     */
    public function handle(CheckIn $checkIn): void
    {
        $checkIn->loadMissing('user');

        $user = $checkIn->user;

        // Read off the persisted record rather than the ?User threaded through
        // the caller, so the guard reflects what was actually saved.
        if (! $user instanceof User || ! $user->notify_check_in_copy) {
            return;
        }

        // The locale captured at check-in, so the worker renders the language
        // the driver was using. Covers the text as well as the email.
        $user->notify(new CheckInCopy($checkIn)->locale($checkIn->locale));
    }
}
