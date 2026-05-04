<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Appointment\AppointmentScheduleResolver;
use App\Http\Requests\AppointmentFormRequest;
use App\Http\Requests\AppointmentSelectRequest;
use App\Session\AppointmentSession;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

final class AppointmentController extends Controller
{
    public function __construct(private readonly AppointmentSession $session) {}

    public function gate(AppointmentSelectRequest $request, AppointmentScheduleResolver $resolver): RedirectResponse
    {
        $request->validated();

        $location = $request->location;

        $scheduleToday = $resolver->buildDTO($location, now());

        $this->session->set($location, $scheduleToday);

        return redirect()->route('appointment.form', $location->uuid ?? '');
    }

    public function form(): Response
    {
        $locations = $this->session->location();

        //unset($locations['address']);

        return inertia('AppointmentForm', [
            'location' => $locations,
            'scheduleToday' => $this->session->scheduleToday(),
        ]);
    }

    public function review(AppointmentFormRequest $request): Response
    {
        return inertia('AppointmentForm', [
        ]);
    }
}
