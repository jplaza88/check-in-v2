<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\NotificationChannel;
use App\Enums\Theme;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Every field is `sometimes` so each control on the settings page saves on its own, without the others
 * having to round-trip their current values. New preferences slot in as extra rules.
 */
final class UpdateAccountSettingsRequest extends FormRequest
{
    /**
     * The notification toggles, in the order they appear on the settings page.
     *
     * @var list<string>
     */
    public const array NOTIFICATION_TOGGLES = [
        'notify_check_in_copy',
        'notify_appointment_copy',
        'notify_appointment_reminder',
    ];

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
            'notify_check_in_copy' => ['sometimes', 'boolean'],
            'notify_appointment_copy' => ['sometimes', 'boolean'],
            'notify_appointment_reminder' => ['sometimes', 'boolean'],
            'notification_channel' => ['sometimes', Rule::enum(NotificationChannel::class)],
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

    public function hasNotificationToggle(string $attribute): bool
    {
        return $this->has($attribute);
    }

    public function notificationToggle(string $attribute): bool
    {
        return $this->boolean($attribute);
    }

    public function hasNotificationChannel(): bool
    {
        return $this->has('notification_channel');
    }

    public function notificationChannel(): NotificationChannel
    {
        return NotificationChannel::from((string) $this->validated('notification_channel'));
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
