<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\CheckInLocationDTO;
use App\Models\CheckIn;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CompleteCheckInAction
{
    public function __construct(
        private CreateCheckInAction $createCheckIn,
        private CreatePurchaseOrdersAction $createPurchaseOrders,
        private SendCheckInCompletedEmailAction $sendCheckInCompletedEmail,
        private SendCheckInCopyAction $sendCheckInCopy,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     *
     * @throws Throwable
     */
    public function handle(array $validated, CheckInLocationDTO $locationDTO, ?User $user = null): CheckIn
    {
        $checkIn = DB::transaction(function () use ($validated, $locationDTO, $user): CheckIn {

            $checkIn = $this->createCheckIn->handle($validated, $locationDTO, $user);
            $this->createPurchaseOrders->handle($checkIn, $validated['po_numbers']);

            return $checkIn;
        });

        // Queued after the commit so the notification never references a
        // check-in that was rolled back.
        $this->sendCheckInCompletedEmail->handle($checkIn);

        // The driver's own copy, if they are signed in and have asked for it.
        $this->sendCheckInCopy->handle($checkIn);

        return $checkIn;
    }
}
