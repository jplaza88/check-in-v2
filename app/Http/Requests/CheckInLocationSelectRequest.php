<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\CheckIn\CheckInAvailabilityResolver;
use App\Models\Location;
use App\Queries\CheckInLocation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class CheckInLocationSelectRequest extends FormRequest
{
    public ?Location $location = null {
        get {
            return $this->location ??= resolve(CheckInLocation::class)->execute($this->input('uuid'));
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
    public function after(CheckInAvailabilityResolver $resolver): array
    {
        return [
            function (Validator $validator) use ($resolver): void {
                $location = $this->location;

                if (! $location instanceof Location) {
                    $validator->errors()->add('uuid', __('messages.checkInSelectLocation.invalidLocation'));

                    return;
                }

                $userCoords = $this->attributes->get('userCoords');

                $canCheckIn = $resolver->isAvailableForCheckIn($location, $userCoords);

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
