<?php

declare(strict_types=1);

namespace App\Pdf;

/**
 * Both the view route and the queued email render through this, so one fake
 * covers every path in tests.
 *
 * Spatie's own Pdf::fake() is not sufficient on its own: it intercepts save(),
 * toResponse() and saveQueued(), but base64() and toMailAttachment() still
 * shell out to Chrome. Process::preventStrayProcesses() does not catch that
 * either, because Browsershot uses Symfony's Process directly rather than
 * Laravel's facade.
 */
interface RecordPdfRenderer
{
    /**
     * Raw PDF bytes.
     */
    public function render(RecordPdfDocument $document): string;
}
