<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Appointment\AppointmentAvailabilityResolver;
use App\Appointment\AppointmentScheduleResolver;
use App\DTOs\AppointmentLocationDTO;
use App\Models\Location;
use App\Queries\AppointmentLocation;
use App\Session\AppointmentSession;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Date;
use Illuminate\Validation\Validator;

final class AppointmentFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'datetime' => ['required', 'date', 'date_format:Y-m-d H:i:s', 'after_or_equal:today'],
        ];
    }

    /**
     * The location is resolved from the gate context in the session (never from
     * user input) and re-queried so availability is validated against current data.
     *
     * @return array<int, callable>
     */
    public function after(
        AppointmentAvailabilityResolver $availabilityResolver,
        AppointmentScheduleResolver $scheduleResolver,
        AppointmentSession $session,
    ): array {
        return [
            function (Validator $validator) use ($availabilityResolver, $scheduleResolver, $session): void {
                $context = $session->getLocation();

                $location = $context instanceof AppointmentLocationDTO
                    ? resolve(AppointmentLocation::class)->execute($context->id)
                    : null;

                if (! $location instanceof Location) {
                    $validator->errors()->add('datetime', __('messages.appointmentSelectLocation.invalidLocation'));

                    return;
                }

                $appointmentDateTime = Date::parse($validator->validated()['datetime'], $location->timezone);

                $availability = $availabilityResolver->isAvailableForAppointment($location, $appointmentDateTime);

                if (! $availability->allowed) {
                    $validator->errors()->add('datetime', $availability->reason ?? __('messages.appointmentSelectLocation.invalidLocation'));

                    return;
                }

                if (! $scheduleResolver->isBookableSlot($location, $appointmentDateTime)) {
                    $validator->errors()->add('datetime', __('messages.appointmentBooking.outsideBookingWindow', [
                        'days' => $location->appointmentMaxBookingDaysAhead(),
                    ]));
                }
            },
        ];
    }
}
