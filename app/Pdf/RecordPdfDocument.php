<?php

declare(strict_types=1);

namespace App\Pdf;

/**
 * Everything needed to render one record as a PDF: which template, the view
 * data, a human-readable filename, and a cache key.
 *
 * Built by RecordPdfDocumentFactory from the same payload the on-screen detail
 * page uses, so the document and the page can never drift apart.
 */
final readonly class RecordPdfDocument
{
    /**
     * @param  view-string  $view
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public string $view,
        public array $data,
        public string $fileName,
        public string $cacheKey,
    ) {}
}
