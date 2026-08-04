<?php

declare(strict_types=1);

namespace App\Pdf;

/**
 * Keeps Chromium out of CI while still exercising the Blade templates.
 *
 * Rendering the view is the point: undefined variables, missing translation
 * keys and broken relations surface as test failures rather than shipping, and
 * asserting on the returned bytes stays meaningful.
 */
final class FakeRecordPdfRenderer implements RecordPdfRenderer
{
    /** @var list<RecordPdfDocument> */
    public array $rendered = [];

    public function render(RecordPdfDocument $document): string
    {
        $this->rendered[] = $document;

        return "%PDF-1.4 fake\n".view($document->view, $document->data)->render();
    }
}
