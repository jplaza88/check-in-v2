<?php

declare(strict_types=1);

namespace App\Pdf;

use Illuminate\Support\Facades\Storage;

/**
 * Renders a document once and serves the bytes from disk thereafter.
 *
 * A Browsershot render blocks an Octane worker for one to three seconds, far
 * too long to pay every time a driver opens their receipt. The cache key
 * carries the record's updated_at, so an edited record renders afresh and a
 * cached copy can never go stale.
 */
final readonly class RecordPdfStore
{
    public function __construct(private RecordPdfRenderer $renderer) {}

    public function bytes(RecordPdfDocument $document): string
    {
        $disk = Storage::disk('local');

        if ($disk->exists($document->cacheKey)) {
            $cached = $disk->get($document->cacheKey);

            if ($cached !== null && $cached !== '') {
                return $cached;
            }
        }

        $bytes = $this->renderer->render($document);

        $disk->put($document->cacheKey, $bytes);

        return $bytes;
    }
}
