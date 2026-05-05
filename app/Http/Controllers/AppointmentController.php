<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Appointment\AppointmentScheduleResolver;
use App\Http\Requests\AppointmentFormRequest;
use App\Http\Requests\AppointmentLocationSelectRequest;
use App\Models\Location;
use App\Session\AppointmentSession;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

final class AppointmentController extends Controller
{
    public function __construct(private readonly AppointmentSession $session) {}

    public function gate(AppointmentLocationSelectRequest $request, AppointmentScheduleResolver $resolver): RedirectResponse
    {
        $request->validated();

        $location = $request->location;
        assert($location instanceof Location);

        $this->session->setLocation($resolver->buildDTO($location, now()));

        return to_route('appointment.form', $location->uuid ?? '');
    }

    public function form(): Response
    {
        return inertia('AppointmentForm', [
            'location' => $this->session->getLocation(),
        ]);
    }

    public function review(AppointmentFormRequest $request): Response
    {
        return inertia('AppointmentForm', [
        ]);
    }
}
