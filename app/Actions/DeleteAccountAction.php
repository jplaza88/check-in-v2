<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\RecordHistoryEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Deletes a driver's account for good.
 *
 * The row is removed rather than soft deleted: a retained row would still hold
 * the email, cellphone and encrypted license, which is a hidden record and not
 * a deletion. The foreign keys already encode what should survive - check-ins,
 * appointments and their history are nullOnDelete so the yard keeps its
 * records unlinked, while the account trail, passkeys and role assignments
 * cascade away with the account.
 */
final readonly class DeleteAccountAction
{
    public function __construct(private RecordHistoryAction $history) {}

    /**
     * @throws Throwable
     */
    public function handle(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $this->noteOnRecords($user);
            $this->forgetSessions($user);

            $user->delete();
        });
    }

    /**
     * Mark the driver's records before the account goes.
     *
     * Written first because the link is what identifies them; a moment later
     * the same foreign key nulls both these rows' user_id and the records' own.
     * Without this an admin opening a check-in would find a null user_id and no
     * way to tell whether it was ever claimed.
     */
    private function noteOnRecords(User $user): void
    {
        $records = [
            ...$user->checkIns()->get()->all(),
            ...$user->appointments()->get()->all(),
        ];

        foreach ($records as $record) {
            $this->history->handle(
                record: $record,
                event: RecordHistoryEvent::AccountDeleted,
                user: $user,
            );
        }
    }

    private function forgetSessions(User $user): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::table('sessions')->where('user_id', $user->id)->delete();
    }
}
