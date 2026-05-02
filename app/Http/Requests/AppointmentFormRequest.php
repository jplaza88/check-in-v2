<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Appointment\AppointmentAvailabilityResolver;
use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class AppointmentFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'datetime' => ['required', 'datetime', 'datetime_format:Y-m-d H:i:s', 'after_or_equal:today'],
        ];
    }

    /**
     * @return array<mixed>
     */
    public function after(AppointmentAvailabilityResolver $resolver): array
    {
        return [
            function (Validator $validator) use ($resolver): void {
                $location = $this->location;

                if (! $location instanceof Location) {
                    $validator->errors()->add('uuid', __('messages.checkInSelectLocation.invalidLocation'));

                    return;
                }

                $appointmentDateTime = $validator->validated()['datetime'];

                $canBookAppointment = $resolver->isAvailableForAppointment($location, $appointmentDateTime);

                if (! $canBookAppointment->allowed) {
                    $validator->errors()->add('uuid', $canBookAppointment->reason ?? '');
                }
            },
        ];
    }
}
