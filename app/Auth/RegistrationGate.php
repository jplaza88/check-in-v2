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

    /**
     * Open the registration window for the configured TTL.
     */
    public function allow(): void
    {
        session([self::SESSION_KEY => now()->addSeconds($this->ttl())->timestamp]);
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
     * Close the registration window.
     */
    public function consume(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    private function ttl(): int
    {
        return (int) config('app.registration_window_ttl');
    }
}
