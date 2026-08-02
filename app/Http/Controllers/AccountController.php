<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Account\HistoryFilters;
use App\Account\HistoryRecordFinder;
use App\Account\HistoryRecordResolver;
use App\Account\HistoryResolver;
use App\Account\OverviewResolver;
use App\Http\Requests\HistoryFilterRequest;
use App\Http\Requests\HistoryRecordRequest;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Inertia\Inertia;
use Inertia\Response;

final class AccountController extends Controller
{
    public function index(#[CurrentUser] User $user, OverviewResolver $resolver): Response
    {
        $expirationDate = $user->drivers_license_expiration_date;

        return inertia('Account', [
            ...$resolver->resolve($user),
            'hasLicense' => $user->drivers_license_number !== null,
            'licenseExpired' => $expirationDate !== null && $expirationDate->lt(today()),
        ]);
    }

    public function history(HistoryFilterRequest $request, #[CurrentUser] User $user, HistoryResolver $resolver): Response
    {
        $filters = HistoryFilters::fromRequest($request);

        return inertia('Account/History', [
            'filters' => $filters->toArray(...),
            'history' => Inertia::merge(fn (): array => $resolver->resolve($user, $filters))
                ->append('data', matchOn: 'key'),
        ]);
    }

    public function showCheckIn(
        HistoryRecordRequest $request,
        #[CurrentUser] User $user,
        HistoryRecordFinder $finder,
        HistoryRecordResolver $resolver,
    ): Response {
        return inertia('Account/HistoryCheckIn', [
            'checkIn' => $resolver->checkIn($finder->checkIn($user, $request->uuid())),
        ]);
    }

    public function showAppointment(
        HistoryRecordRequest $request,
        #[CurrentUser] User $user,
        HistoryRecordFinder $finder,
        HistoryRecordResolver $resolver,
    ): Response {
        return inertia('Account/HistoryAppointment', [
            'appointment' => $resolver->appointment($finder->appointment($user, $request->uuid())),
        ]);
    }

    public function editProfile(#[CurrentUser] User $user): Response
    {
        // The license number is hidden from the globally-shared auth.user, so
        // pass the decrypted value explicitly, only on the screen that edits it.
        return inertia('Account/EditProfile', [
            'hasLicense' => $user->drivers_license_number !== null,
            'license' => [
                'number' => $user->drivers_license_number,
                'state' => $user->drivers_license_state,
                'expirationDate' => $user->drivers_license_expiration_date?->format('Y-m-d'),
            ],
        ]);
    }
}
