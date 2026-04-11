<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocationDistanceRequest extends FormRequest
{
    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
