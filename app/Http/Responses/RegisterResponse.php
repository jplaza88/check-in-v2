<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Auth\HomeRedirect;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

final class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $user = $request->user();
        assert($user instanceof User);

        return redirect()->intended(HomeRedirect::for($user));
    }
}
