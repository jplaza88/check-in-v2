<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Auth\HomeRedirect;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

final class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $user = $request->user();
        assert($user instanceof User);

        return redirect()->intended(HomeRedirect::for($user));
    }
}
