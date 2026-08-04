<?php

declare(strict_types=1);

namespace App\Account;

use App\Models\Appointment;
use App\Models\CheckIn;
use App\Models\User;

final readonly class HistoryRecordFinder
{
    /**
     * Scoped to the driver so a record belonging to someone else 404s rather
     * than 403s, which would otherwise confirm the record exists.
     */
    public function checkIn(User $user, string $uuid): CheckIn
    {
        return CheckIn::query()
            ->with(['location.address', 'purchaseOrders'])
            ->where('uuid', $uuid)
            ->where('user_id', $user->id)
            ->firstOrFail();
    }

    public function appointment(User $user, string $uuid): Appointment
    {
        return Appointment::query()
            ->with(['location.address', 'purchaseOrders'])
            ->where('uuid', $uuid)
            ->where('user_id', $user->id)
            ->firstOrFail();
    }
}
