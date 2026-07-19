<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\ClaimPendingRecordsAction;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Bridges the Verified event to the claim action. Queued so the work runs on
 * Horizon rather than in the verification request.
 */
final class AttachPendingRecordsToUser implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(private readonly ClaimPendingRecordsAction $action) {}

    public function handle(Verified $event): void
    {
        if ($event->user instanceof User) {
            $this->action->handle($event->user);
        }
    }
}
