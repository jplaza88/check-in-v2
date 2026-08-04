<?php

declare(strict_types=1);

namespace App\Pdf;

use RuntimeException;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;

final readonly class BrowsershotRecordPdfRenderer implements RecordPdfRenderer
{
    /**
     * Bounded so a hung Chrome cannot hold an Octane worker or a queue slot
     * indefinitely. Stays below the pdf supervisor's 120s job timeout.
     */
    private const int TIMEOUT_SECONDS = 90;

    public function render(RecordPdfDocument $document): string
    {
        $encoded = Pdf::view($document->view, $document->data)
            ->format(Format::Letter)
            ->withBrowsershot(function (Browsershot $browsershot): void {
                $browsershot->timeout(self::TIMEOUT_SECONDS);
            })
            ->base64();

        $decoded = base64_decode($encoded, strict: true);

        // Strict decoding rejects anything that is not clean base64, which
        // would otherwise reach the driver as a silently corrupt PDF.
        throw_if(
            $decoded === false,
            RuntimeException::class,
            sprintf('Browsershot returned malformed base64 rendering [%s].', $document->view),
        );

        return $decoded;
    }
}
