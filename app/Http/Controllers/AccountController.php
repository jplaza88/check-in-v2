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
}
