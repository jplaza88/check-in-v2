<?php

declare(strict_types=1);

namespace App\History;

use App\Models\Appointment;
use App\Models\CheckIn;

/**
 * Marks a notification as belonging to a check-in or booking timeline.
 *
 * This is the whole extension point. {@see \App\Listeners\RecordNotificationHistory}
 * records any notification implementing this, on every channel it went out on,
 * so a new message type - an ERP "processed" notice, a delay warning, whatever
 * comes later - joins the trail by implementing this interface and nothing else.
 * No migration, no new event case, no change to the listener.
 */
interface RecordNotification
{
    /** The record this message is about. */
    public function historyRecord(): CheckIn|Appointment;

    /** Slug naming this message in the trail, e.g. 'check-in-copy'. */
    public function historySubject(): string;
}
