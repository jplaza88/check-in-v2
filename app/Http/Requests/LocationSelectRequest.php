<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ScheduleType;
use App\Models\Location;
use App\Services\CheckInAvailabilityService;
use App\Services\LocationScheduleService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class LocationSelectRequest extends FormRequest
{
    public ?Location $location = null {
        get {
            return $this->location ??= resolve(LocationScheduleService::class)
                ->getActiveLocationByUuid($this->input('uuid'), ScheduleType::CheckIn);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'uuid' => ['required', 'uuid'],
        ];
    }

    /**
     * @return array<mixed>
     */
    public function after(CheckInAvailabilityService $service): array
    {
        return [
            function (Validator $validator) use ($service): void {
                $location = $this->location;

                if (! $location instanceof Location) {
                    $validator->errors()->add('uuid', __('messages.checkInSelectLocation.invalidLocation'));

                    return;
                }

                $coords = $this->attributes->get('userCoords');

                $canCheckIn = $service->canCheckIn($location, $coords);

                if (! $canCheckIn->allowed) {
                    $validator->errors()->add('uuid', $canCheckIn->reason ?? __('messages.checkInSelectLocation.locationClosed'));
                }
            },
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('uuid')) {
            $this->merge(['uuid' => $this->route('uuid')]);
        }
    }
}
