<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\BookAppointmentAction;
use App\Address\AddressManager;
use App\Appointment\AppointmentScheduleResolver;
use App\DTOs\AppointmentLocationDTO;
use App\Enums\AppointmentStatus;
use App\Http\Requests\AppointmentFormRequest;
use App\Http\Requests\AppointmentLocationSelectRequest;
use App\Models\Appointment;
use App\Models\Location;
use App\Session\AppointmentSession;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Throwable;

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

        $this->session->forgetLocation();

        return to_route('appointment.confirmed', $appointment->uuid);
    }

    public function confirmed(string $uuid, AddressManager $address): Response
    {
        $appointment = Appointment::with(['location.address', 'purchaseOrders'])
            ->where('uuid', $uuid)
            ->where('status', AppointmentStatus::Scheduled)
            ->firstOrFail();

        $scheduledFor = $appointment->scheduled_for->setTimezone($appointment->location->timezone);

        return inertia('AppointmentConfirmation', [
            'appointment' => [
                'uuid' => $appointment->uuid,
                'referenceNumber' => $appointment->reference_number,
                'scheduledDate' => $scheduledFor->format('F j, Y'),
                'scheduledTime' => $scheduledFor->format('g:i A'),
                'driversName' => $appointment->drivers_name,
                'locationName' => $appointment->location->name,
                'locationAddress' => $address->buildAddress($appointment->location->address->toArray()),
                'purchaseOrders' => $appointment->purchaseOrders->pluck('number')->all(),
            ],
            'contact' => [
                'phone' => '(555) 555-5555',
                'email' => 'martori@email.com',
            ],
        ]);
    }
}
