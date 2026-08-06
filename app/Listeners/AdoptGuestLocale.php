<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use App\Session\UserSession;
use Illuminate\Auth\Events\Login;

/**
 * Carries a language chosen while signed out onto the account being signed in to.
 *
 * A driver who switches to Spanish during a guest check-in and then registers should stay in Spanish.
 * Fortify fires Login on registration too, so this covers both paths.
 *
 * Only fills a null column, never overwrites: an account that already states a language outranks
 * whatever the session happens to hold. Deliberately not queued, since it reads the request's session.
 */
final readonly class AdoptGuestLocale
{
    public function __construct(private UserSession $session) {}

    public function handle(Login $event): void
    {
        $user = $event->user;

        if (! $user instanceof User || $user->locale !== null) {
            return;
        }

        $locale = $this->session->getLocale();

        if ($locale === null || ! in_array($locale, config('app.locales'), true)) {
            return;
        }

        $user->update(['locale' => $locale]);
    }
}
