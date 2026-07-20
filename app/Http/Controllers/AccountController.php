<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Account\AccountResolver;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Inertia\Response;

final class AccountController extends Controller
{
    public function index(#[CurrentUser] User $user, AccountResolver $resolver): Response
    {
        return inertia('Account', $resolver->resolve($user));
    }

    public function editProfile(#[CurrentUser] User $user): Response
    {
        // The license number is hidden from the globally-shared auth.user, so
        // pass the decrypted value explicitly, only on the screen that edits it.
        return inertia('Account/EditProfile', [
            'license' => [
                'number' => $user->drivers_license_number,
                'state' => $user->drivers_license_state,
                'expirationDate' => $user->drivers_license_expiration_date?->format('Y-m-d'),
            ],
        ]);
    }
}
