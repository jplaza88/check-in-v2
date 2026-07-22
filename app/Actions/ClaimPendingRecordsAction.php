<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Appointment;
use App\Models\CheckIn;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Attaches the guest check-in / appointment a driver created just before
 * registering, now that their email is verified. Stamps how and when the
 * record was claimed, then clears the pending references — all in one
 * transaction.
 */
final class ClaimPendingRecordsAction
{
    private const string CLAIMED_VIA = 'email_verification';

    /**
     * @throws Throwable
     */
    public function handle(User $user): void
    {
        if ($user->pending_check_in_id === null && $user->pending_appointment_id === null) {
            return;
        }

        DB::transaction(function () use ($user): void {
            $claim = [
                'user_id' => $user->id,
                'claimed_at' => Date::now(),
                'claimed_via' => self::CLAIMED_VIA,
            ];

            if ($user->pending_check_in_id !== null) {
                CheckIn::query()
                    ->whereKey($user->pending_check_in_id)
                    ->whereNull('user_id')
                    ->update($claim);
            }

            if ($user->pending_appointment_id !== null) {
                Appointment::query()
                    ->whereKey($user->pending_appointment_id)
                    ->whereNull('user_id')
                    ->update($claim);
            }

            $user->forceFill([
                'pending_check_in_id' => null,
                'pending_appointment_id' => null,
            ])->save();
        });
    }
}
