<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Theme;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Every field is `sometimes` so each control on the settings page saves on its own, without the others
 * having to round-trip their current values. New preferences slot in as extra rules.
 */
final class UpdateAccountSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'theme' => ['sometimes', 'nullable', Rule::enum(Theme::class)],
            // The public uuid, never the primary key. Resolved against the bookable locations in the controller.
            'location_id' => ['sometimes', 'nullable', 'uuid'],
        ];
    }

    public function hasTheme(): bool
    {
        return $this->has('theme');
    }

    public function theme(): ?Theme
    {
        $theme = $this->validated('theme');

        return $theme === null ? null : Theme::from((string) $theme);
    }

    public function hasLocation(): bool
    {
        return $this->has('location_id');
    }

    public function locationUuid(): ?string
    {
        $uuid = $this->validated('location_id');

        return $uuid === null ? null : (string) $uuid;
    }
}
