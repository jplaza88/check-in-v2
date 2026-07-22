<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse as FailedPasswordResetLinkRequestResponseContract;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enumeration-safe response for a password reset link request. Fortify's default
 * fails loudly ("we can't find that email"), which reveals which addresses are
 * registered. This answers a missing account exactly like a real one: the same
 * generic "sent" status, so the form never confirms whether an email exists.
 */
final class PasswordResetLinkResponse implements FailedPasswordResetLinkRequestResponseContract
{
    /**
     * @param  Request  $request
     */
    public function toResponse($request): Response
    {
        $status = trans(Password::RESET_LINK_SENT);

        return $request->wantsJson()
            ? new JsonResponse(['message' => $status], 200)
            : back()->with('status', $status);
    }
}
