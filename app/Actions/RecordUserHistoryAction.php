<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserHistoryEvent;
use App\Models\User;
use App\Models\UserHistory;

/**
 * Appends a row to the account's history trail. Redaction happens here rather
 * than in the callers so no future caller can leak a secret into the log by
 * accident.
 */
final readonly class RecordUserHistoryAction
{
    /**
     * Attributes whose values must never reach the trail. Their presence is
     * still recorded — we keep the fact that they changed, not what they became.
     *
     * @var list<string>
     */
    private const array REDACTED = [
        'drivers_license_number',
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * @param  array<string, mixed>  $changes  Keyed by attribute, each holding a
     *                                         {from, to} pair.
     */
    public function handle(User $user, UserHistoryEvent $event, array $changes = []): UserHistory
    {
        $changes = $this->redact($changes);

        return UserHistory::query()->create([
            'user_id' => $user->id,
            'event' => $event,
            'changes' => $changes === [] ? null : $changes,
        ]);
    }

    /**
     * @param  array<string, mixed>  $changes
     * @return array<string, mixed>
     */
    private function redact(array $changes): array
    {
        foreach (self::REDACTED as $attribute) {
            if (array_key_exists($attribute, $changes)) {
                $changes[$attribute] = ['changed' => true];
            }
        }

        return $changes;
    }
}
