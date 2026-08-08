<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\DeleteAccountAction;
use App\Http\Requests\DeleteAccountRequest;
use App\Http\Requests\UpdateAccountSettingsRequest;
use App\Models\Location;
use App\Models\User;
use App\Queries\AppointmentLocations;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Response;
use Throwable;

final class AccountSettingsController extends Controller
{
    public function __construct(private readonly AppointmentLocations $locations) {}

    public function index(#[CurrentUser] User $user): Response
    {
        $bookable = $this->locations->execute();

        return inertia('Account/Settings', [
            // Drives the usual-location picker. Bookable locations, not checkable-in ones: the preference
            // only steers the appointment flow, since check-in ordering comes from the driver's coordinates.
            'locations' => $bookable
                ->map(fn (Location $location): array => [
                    'id' => $location->uuid,
                    'name' => $location->name,
                ])
                ->values()
                ->all(),
            // Resolved through the bookable set rather than the relation, so a location that has since been
            // soft-deleted or had appointments disabled reads as "none" instead of showing an unpickable option.
            'locationId' => $this->currentLocationUuid($user, $bookable),
            // The text-message channel is unusable without a number on file,
            // so the option is disabled rather than silently failing to deliver.
            'hasCellphone' => $user->cellphone !== null,
            'notifications' => [
                'checkInCopy' => $user->notify_check_in_copy,
                'appointmentCopy' => $user->notify_appointment_copy,
                'appointmentReminder' => $user->notify_appointment_reminder,
                'channel' => $user->notification_channel->value,
            ],
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function update(UpdateAccountSettingsRequest $request, #[CurrentUser] User $user): RedirectResponse
    {
        $attributes = [];

        if ($request->hasTheme()) {
            $attributes['theme'] = $request->theme();
        }

        if ($request->hasLocation()) {
            $attributes['location_id'] = $this->resolveLocationId($request->locationUuid());
        }

        foreach (UpdateAccountSettingsRequest::NOTIFICATION_TOGGLES as $toggle) {
            if ($request->hasNotificationToggle($toggle)) {
                $attributes[$toggle] = $request->notificationToggle($toggle);
            }
        }

        if ($request->hasNotificationChannel()) {
            $attributes['notification_channel'] = $request->notificationChannel();
        }

        if ($attributes !== []) {
            $user->forceFill($attributes)->save();
        }

        // The page confirms via useForm's recentlySuccessful, the pattern the
        // profile page already uses; there is no flash plumbing in this app.
        return back();
    }

    /**
     * Delete the driver's account for good.
     *
     * The action is injected here rather than through the constructor, which
     * carries only what the two settings screens need.
     *
     * @throws Throwable
     */
    public function destroy(
        DeleteAccountRequest $request,
        #[CurrentUser] User $user,
        DeleteAccountAction $deleteAccount,
    ): RedirectResponse {
        $request->validated();

        $deleteAccount->handle($user);

        // Signed out after the delete, not before, so #[CurrentUser] still
        // resolves through the action.
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('home');
    }

    /**
     * Trades the public uuid for the primary key, refusing anything the driver could not have booked at.
     *
     * @throws ValidationException
     */
    private function resolveLocationId(?string $uuid): ?int
    {
        if ($uuid === null) {
            return null;
        }

        $location = $this->locations->execute()->firstWhere('uuid', $uuid);

        if (! $location instanceof Location) {
            throw ValidationException::withMessages([
                'location_id' => __('validation.exists', ['attribute' => 'location']),
            ]);
        }

        return $location->id;
    }

    /**
     * @param  Collection<int, Location>  $bookable
     */
    private function currentLocationUuid(User $user, Collection $bookable): ?string
    {
        if ($user->location_id === null) {
            return null;
        }

        return $bookable->firstWhere('id', $user->location_id)?->uuid;
    }
}
