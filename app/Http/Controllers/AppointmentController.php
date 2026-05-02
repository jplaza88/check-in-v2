<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentFormRequest;
use App\Http\Requests\AppointmentSelectRequest;
use App\Session\UserSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

final class AppointmentController extends Controller
{
    public function __construct(private readonly UserSession $session) {}

    public function gate(AppointmentSelectRequest $request): RedirectResponse
    {
        $request->validated();

        $location = $request->location;

        $this->session->setAppointmentLocation($location);

        return redirect()->route('appointment.form', $location->uuid ?? '');

        /*return inertia('AppointmentForm', [
            'location' => $location,
        ]);*/
    }

    public function form(): Response
    {
        $location = $this->session->getAppointmentLocation();
        unset($location['address']);
        return inertia('AppointmentForm', [
            'location' => $location,
        ]);
    }

    public function review(AppointmentFormRequest $request): Response
    {
        return inertia('AppointmentForm', [
        ]);
    }
}
