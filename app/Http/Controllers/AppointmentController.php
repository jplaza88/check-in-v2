<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\BookAppointmentAction;
use App\Appointment\AppointmentConfirmationResolver;
use App\Appointment\AppointmentScheduleResolver;
use App\DTOs\AppointmentLocationDTO;
use App\Http\Requests\AppointmentFormRequest;
use App\Http\Requests\AppointmentLocationSelectRequest;
use App\Models\Location;
use App\Session\AppointmentSession;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Throwable;

final class AppointmentController extends Controller
{
    public function __construct(private readonly AppointmentSession $session) {}

    public function selectLocation(AppointmentScheduleResolver $resolver): Response
    {
        return inertia('AppointmentSelectLocation', [
            'locations' => $resolver->getLocations(),
        ]);
    }

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

    /**
     * @throws Throwable
     */
    public function store(AppointmentFormRequest $request, BookAppointmentAction $action): RedirectResponse
    {
        $locationDTO = $this->session->getLocation();
        if (! $locationDTO instanceof AppointmentLocationDTO) {
            return to_route('appointment.selectLocation');
        }

        $appointment = $action->handle($request->validated(), $locationDTO);

        $this->session->markBookingComplete();

        return to_route('appointment.confirmed', $appointment->uuid);
    }

    public function confirmed(string $uuid, AppointmentConfirmationResolver $resolver): Response
    {
        inertia()->clearHistory();

        return inertia('AppointmentConfirmation', $resolver->resolve($uuid));
    }
}
