<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\RecordHistoryEvent;
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

    public function __construct(private readonly RecordHistoryAction $history) {}

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

            /*
             * Recorded by hand rather than by the status hook in AppServiceProvider:
             * these are mass updates, and Query::update() fires no model events.
             * The row is only written when the update actually matched, so a
             * record already claimed by someone else leaves no misleading entry.
             */
            if ($user->pending_check_in_id !== null) {
                $claimed = CheckIn::query()
                    ->whereKey($user->pending_check_in_id)
                    ->whereNull('user_id')
                    ->update($claim);

                if ($claimed > 0) {
                    $this->recordClaim(CheckIn::query()->find($user->pending_check_in_id), $user);
                }
            }

            if ($user->pending_appointment_id !== null) {
                $claimed = Appointment::query()
                    ->whereKey($user->pending_appointment_id)
                    ->whereNull('user_id')
                    ->update($claim);

                if ($claimed > 0) {
                    $this->recordClaim(Appointment::query()->find($user->pending_appointment_id), $user);
                }
            }

            $user->forceFill([
                'pending_check_in_id' => null,
                'pending_appointment_id' => null,
            ])->save();
        });
    }

    private function recordClaim(CheckIn|Appointment|null $record, User $user): void
    {
        if ($record === null) {
            return;
        }

        $this->history->handle(
            record: $record,
            event: RecordHistoryEvent::Claimed,
            subject: self::CLAIMED_VIA,
            user: $user,
        );
    }
}
