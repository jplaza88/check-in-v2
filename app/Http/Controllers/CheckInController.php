<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CheckInLocationSelectRequest;
use App\Session\CheckInSession;
use Inertia\Response;

final class CheckInController extends Controller
{
    public function __construct(private readonly CheckInSession $session) {}

    public function gate(CheckInLocationSelectRequest $request): Response
    {
        $request->validated();

        // Proximity check has passed; issue a gate pass so the driver can
        // complete the (potentially long) form without re-validating coords.
        $this->session->issueGatePass($request->location->uuid);

        return inertia('CheckInForm', [
            'location' => $request->location,
        ]);
    }
}
