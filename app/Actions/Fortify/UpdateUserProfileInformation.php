<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Actions\RecordUserHistoryAction;
use App\Enums\UserHistoryEvent;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

final readonly class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function __construct(private RecordUserHistoryAction $history) {}

    /**
     * Validate and update the given user's profile information.
     *
     * @param array<string, string> $input
     *
     * @throws ValidationException
     * @throws \Throwable
     */
    public function update(User $user, array $input): void
    {
        $input['cellphone'] = $this->normalizeCellphone($input['cellphone'] ?? null);

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],

            'cellphone' => ['nullable', 'string', 'regex:/^\+1\d{10}$/'],

            'drivers_license_number' => ['nullable', 'string', 'min:4', 'max:20'],
            'drivers_license_state' => ['nullable', 'string', 'max:255'],
            'drivers_license_expiration_date' => ['nullable', 'date_format:Y-m-d', 'after:today', 'before:+60 years'],
        ], [
            'cellphone.regex' => __('messages.accountProfile.cellphoneInvalid'),
            'drivers_license_number.min' => __('messages.accountProfile.licenseNumberInvalid'),
            'drivers_license_number.max' => __('messages.accountProfile.licenseNumberInvalid'),
            'drivers_license_expiration_date.after' => __('messages.accountProfile.licenseExpirationPast'),
            'drivers_license_expiration_date.before' => __('messages.accountProfile.licenseExpirationTooFar'),
        ])->validateWithBag('updateProfileInformation');

        $license = $this->normalizeLicense($input);

        $before = $this->snapshot($user);

        $requiresReverification = $input['email'] !== $user->email
            && $user instanceof MustVerifyEmail;

        DB::transaction(function () use ($user, $input, $license, $before, $requiresReverification): void {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'cellphone' => $input['cellphone'],
                ...$license,
                ...($requiresReverification ? ['email_verified_at' => null] : []),
            ])->save();

            $changes = $this->diff($before, $this->snapshot($user));

            if ($changes !== []) {
                $this->history->handle($user, UserHistoryEvent::ProfileUpdated, $changes);
            }
        });

        // Dispatched after the commit on purpose: VerifyEmail is queued on Redis,
        // so a worker could pick the job up and mail a link for an email address
        // whose row never landed.
        if ($requiresReverification) {
            $user->sendEmailVerificationNotification();
        }
    }

    /**
     * Capture the trackable profile attributes as plain, comparable scalars.
     *
     * This reads through the accessors on purpose — getDirty()/getChanges()
     * can't be used here. Encrypted::set() produces different ciphertext on
     * every write and Eloquent only decrypts-to-compare for its own built-in
     * `encrypted` cast, so drivers_license_number would report dirty on every
     * single save. The expiration date is normalized to its stored Y-m-d shape
     * because the cast hands back a CarbonImmutable.
     *
     * @return array<string, string|null>
     */
    private function snapshot(User $user): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'cellphone' => $user->cellphone,
            'drivers_license_number' => $user->drivers_license_number,
            'drivers_license_state' => $user->drivers_license_state,
            'drivers_license_expiration_date' => $user->drivers_license_expiration_date?->format('Y-m-d'),
        ];
    }

    /**
     * @param  array<string, string|null>  $before
     * @param  array<string, string|null>  $after
     * @return array<string, array{from: string|null, to: string|null}>
     */
    private function diff(array $before, array $after): array
    {
        $changes = [];

        foreach ($after as $attribute => $value) {
            if ($before[$attribute] !== $value) {
                $changes[$attribute] = ['from' => $before[$attribute], 'to' => $value];
            }
        }

        return $changes;
    }

    /**
     * Normalize a US cellphone to the "+1XXXXXXXXXX" shape used across the app,
     * or null when empty. Matches the check-in form so the number can prefill.
     */
    private function normalizeCellphone(?string $value): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        return $digits === '' ? null : '+1'.$digits;
    }

    /**
     * Coerce the optional license fields to their stored shape: trimmed values
     * or null when blank, so clearing a field wipes it.
     *
     * @param  array<string, string>  $input
     * @return array{drivers_license_number: string|null, drivers_license_state: string|null, drivers_license_expiration_date: string|null}
     */
    private function normalizeLicense(array $input): array
    {
        $blankToNull = static function (?string $value): ?string {
            $value = mb_trim((string) $value);

            return $value === '' ? null : $value;
        };

        return [
            'drivers_license_number' => $blankToNull($input['drivers_license_number'] ?? null),
            'drivers_license_state' => $blankToNull($input['drivers_license_state'] ?? null),
            'drivers_license_expiration_date' => $blankToNull($input['drivers_license_expiration_date'] ?? null),
        ];
    }
}
