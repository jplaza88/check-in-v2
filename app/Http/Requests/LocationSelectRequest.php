<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class LocationSelectRequest extends FormRequest
{
    protected ?Location $location = null;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('uuid')) {
            return;
        }

        $uuid = $this->route('uuid');

        if (is_string($uuid) && Str::isUuid($uuid)) {
            $this->merge(['uuid' => $uuid]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'uuid' => [
                'required',
                'uuid',
                Rule::exists('locations', 'uuid')
                    ->where('is_active', true)
                    ->where('is_checkins_enabled', true),
            ],
        ];
    }

    /**
     * @return array<mixed>
     */
    public function after(LocationService $service): array
    {
        return [
            function (Validator $validator) use ($service) {
                $location = $this->getLocation($service);
                $coords = $this->attributes->get('userCoords');

                if (! $location) {
                    $validator->errors()->add('uuid', 'Invalid Location.');

                    return;
                }

                if (! is_array($coords)) {
                    $validator->errors()->add('uuid', 'Please select a location to start over.');

                    return;
                }

                $canCheckIn = $service->canCheckIn($location, $coords);

                if (! $canCheckIn['allowed']) {
                    $validator->errors()->add('uuid', $canCheckIn['reason']);
                }
            },
        ];
    }

    public function getLocation(LocationService $service): ?Location
    {
        if (! is_null($this->location)) {
            return $this->location;
        }

        $uuid = $this->input('uuid');

        if (! is_string($uuid) || $uuid === '') {
            return $this->location = null;
        }

        return $this->location = $service->getActiveCheckInLocationsWithScheduleByUuid($uuid);
    }
}
