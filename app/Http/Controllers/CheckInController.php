<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CheckInSelectRequest;
use App\Session\CheckInSession;
use Inertia\Response;

final class CheckInController extends Controller
{
    public function __construct(private readonly CheckInSession $session) {}

    public function gate(CheckInSelectRequest $request): Response
    {
        $request->validated();

        $location = $request->location;

        $this->session->setCheckInLocation($location);

        return inertia('CheckInForm', [
            'location' => $location,
        ]);
    }
}
