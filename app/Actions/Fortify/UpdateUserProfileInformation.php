<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

final class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
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

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input, $license);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'cellphone' => $input['cellphone'],
                ...$license,
            ])->save();
        }
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

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     * @param  array{drivers_license_number: string|null, drivers_license_state: string|null, drivers_license_expiration_date: string|null}  $license
     */
    private function updateVerifiedUser(User $user, array $input, array $license): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'cellphone' => $input['cellphone'],
            ...$license,
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
