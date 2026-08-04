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
use App\Mail\RecordDocumentMail;
use App\Models\Appointment;
use App\Models\CheckIn;
use App\Models\Location;
use App\Models\User;
use App\Pdf\RecordPdfDocument;
use App\Pdf\RecordPdfDocumentFactory;
use App\Pdf\RecordPdfStore;
use App\Queries\CheckInLocations;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

final class AccountController extends Controller
{
    public function index(#[CurrentUser] User $user, OverviewResolver $resolver): Response
    {
        $expirationDate = $user->drivers_license_expiration_date;

        return inertia('Account', [
            ...$resolver->resolve($user),
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

    public function checkInPdf(
        HistoryRecordRequest $request,
        #[CurrentUser] User $user,
        HistoryRecordFinder $finder,
        RecordPdfDocumentFactory $documents,
        RecordPdfStore $store,
    ): HttpResponse {
        $document = $documents->forCheckIn($finder->checkIn($user, $request->uuid()));

        return $this->inlinePdf($document, $store->bytes($document));
    }

    public function appointmentPdf(
        HistoryRecordRequest $request,
        #[CurrentUser] User $user,
        HistoryRecordFinder $finder,
        RecordPdfDocumentFactory $documents,
        RecordPdfStore $store,
    ): HttpResponse {
        $document = $documents->forAppointment($finder->appointment($user, $request->uuid()));

        return $this->inlinePdf($document, $store->bytes($document));
    }

    public function emailCheckInPdf(
        HistoryRecordRequest $request,
        #[CurrentUser] User $user,
        HistoryRecordFinder $finder,
    ): RedirectResponse {
        return $this->queueDocument($user, $finder->checkIn($user, $request->uuid()));
    }

    public function emailAppointmentPdf(
        HistoryRecordRequest $request,
        #[CurrentUser] User $user,
        HistoryRecordFinder $finder,
    ): RedirectResponse {
        return $this->queueDocument($user, $finder->appointment($user, $request->uuid()));
    }

    public function settings(#[CurrentUser] User $user, CheckInLocations $locations): Response
    {
        return inertia('Account/Settings', [
            // Drives the default check-in location picker.
            'locations' => $locations->execute()
                ->map(fn (Location $location): array => [
                    'id' => $location->uuid,
                    'name' => $location->name,
                ])
                ->values()
                ->all(),
            // The text-message channel is unusable without a number on file,
            // so the option is disabled rather than silently failing to deliver.
            'hasCellphone' => $user->cellphone !== null,
        ]);
    }

    public function editProfile(#[CurrentUser] User $user): Response
    {
        // The license number is hidden from the globally-shared auth.user, so
        // pass the decrypted value explicitly, only on the screen that edits it.
        return inertia('Account/EditProfile', [
            'license' => [
                'number' => $user->drivers_license_number,
                'state' => $user->drivers_license_state,
                'expirationDate' => $user->drivers_license_expiration_date?->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * Sent only to the driver's own address, never one supplied by the request.
     * The locale is captured now so the worker renders the language the driver
     * was actually using, matching how the verification email already works.
     */
    private function queueDocument(User $user, CheckIn|Appointment $record): RedirectResponse
    {
        Mail::to($user->email)->queue(
            new RecordDocumentMail($record)
                ->onQueue('pdf')
                ->locale(app()->getLocale()),
        );

        // The page confirms via useForm's recentlySuccessful, the pattern the
        // profile page already uses; there is no flash plumbing in this app.
        return back();
    }

    /**
     * Inline, not attachment. An attachment disappears into the phone's
     * Downloads folder; inline opens the document in the built-in viewer, where
     * the share sheet still offers Save to Files, Print and Mail.
     */
    private function inlinePdf(RecordPdfDocument $document, string $bytes): HttpResponse
    {
        return response($bytes, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('inline; filename="%s"', $document->fileName),
            'Content-Length' => (string) mb_strlen($bytes, '8bit'),
        ]);
    }
}
