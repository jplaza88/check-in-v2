<?php

declare(strict_types=1);

namespace App\Auth;

/**
 * Resolves where a user lands after authenticating. Centralized so login and
 * registration stay in sync.
 *
 * Drivers are role-less authenticated users (spatie roles/teams are reserved
 * for employees, who belong to location/manager/admin teams). Everyone lands
 * on their driver account for now; once the staff surface exists, users who
 * hold a team-scoped role will branch to the staff dashboard instead.
 */
final class HomeRedirect
{
    public static function for(): string
    {
        return route('account');
    }
}
