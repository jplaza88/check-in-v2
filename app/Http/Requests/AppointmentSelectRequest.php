<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Location;
use App\Queries\AppointmentLocation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class AppointmentSelectRequest extends FormRequest
{
    public ?Location $location = null {
        get {
            return $this->location ??= resolve(AppointmentLocation::class)->execute($this->input('uuid'));
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
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $location = $this->location;

                if (! $location instanceof Location) {
                    $validator->errors()->add('uuid', __('messages.checkInSelectLocation.invalidLocation'));
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
