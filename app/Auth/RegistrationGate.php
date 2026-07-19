<?php

declare(strict_types=1);

namespace App\Auth;

/**
 * Grants a short-lived, single-use permission to register an account. The
 * window opens when a driver completes a check-in or books an appointment and
 * closes once it is used or expires, so account creation is only ever available
 * as a follow-up to one of those flows.
 */
final class RegistrationGate
{
    public const string SESSION_KEY = 'registration.expires_at';

    public const string CELLPHONE_KEY = 'registration.cellphone';

    public const string NAME_KEY = 'registration.name';

    public const string CLAIM_TYPE_KEY = 'registration.claim_type';

    public const string CLAIM_ID_KEY = 'registration.claim_id';

    /**
     * Open the registration window for the configured TTL, optionally carrying
     * the driver's name and cellphone so a new account can be prefilled. Any
     * prior record claim is reset.
     */
    public function allow(?string $cellphone = null, ?string $name = null): void
    {
        session([
            self::SESSION_KEY => now()->addSeconds($this->ttl())->timestamp,
            self::CELLPHONE_KEY => $cellphone,
            self::NAME_KEY => $name,
            self::CLAIM_TYPE_KEY => null,
            self::CLAIM_ID_KEY => null,
        ]);
    }

    /**
     * Record which check-in or appointment ("check_in" | "appointment") the new
     * account should be attached to once its email is verified.
     */
    public function claim(string $type, int $id): void
    {
        session([
            self::CLAIM_TYPE_KEY => $type,
            self::CLAIM_ID_KEY => $id,
        ]);
    }

    public function claimType(): ?string
    {
        return session(self::CLAIM_TYPE_KEY);
    }

    public function claimId(): ?int
    {
        $id = session(self::CLAIM_ID_KEY);

        return $id === null ? null : (int) $id;
    }

    /**
     * The cellphone captured from the completed check-in or appointment, if any.
     */
    public function cellphone(): ?string
    {
        return session(self::CELLPHONE_KEY);
    }

    /**
     * The driver's name captured from the completed check-in or appointment.
     */
    public function name(): ?string
    {
        return session(self::NAME_KEY);
    }

    /**
     * Whether registration is currently permitted. Expired windows are cleared.
     */
    public function isAllowed(): bool
    {
        $expiresAt = session(self::SESSION_KEY);

        if ($expiresAt === null) {
            return false;
        }

        if (now()->timestamp > (int) $expiresAt) {
            $this->consume();

            return false;
        }

        return true;
    }

    /**
     * Close the registration window and clear the carried cellphone.
     */
    public function consume(): void
    {
        session()->forget([
            self::SESSION_KEY,
            self::CELLPHONE_KEY,
            self::NAME_KEY,
            self::CLAIM_TYPE_KEY,
            self::CLAIM_ID_KEY,
        ]);
    }

    private function ttl(): int
    {
        return (int) config('app.registration_window_ttl');
    }
}
