<?php

declare(strict_types=1);

namespace App\Mail\Concerns;

use App\Models\Appointment;
use App\Models\CheckIn;
use App\Pdf\RecordPdfDocument;
use App\Pdf\RecordPdfDocumentFactory;
use App\Pdf\RecordPdfStore;
use Illuminate\Mail\Mailables\Attachment;

/**
 * Attaches a record's PDF to a mailable.
 *
 * Services are resolved inside the closure rather than in the constructor:
 * they do not survive SerializesModels, and rendering lazily keeps ~150KB of
 * PDF out of the Redis job payload. The closure runs in the worker at send time.
 */
trait AttachesRecordPdf
{
    /**
     * @return array<int, Attachment>
     */
    protected function recordPdfAttachments(CheckIn|Appointment $record): array
    {
        $document = $this->recordPdfDocument($record);

        return [
            Attachment::fromData(
                fn (): string => resolve(RecordPdfStore::class)->bytes($document),
                $document->fileName,
            )->withMime('application/pdf'),
        ];
    }

    private function recordPdfDocument(CheckIn|Appointment $record): RecordPdfDocument
    {
        $factory = resolve(RecordPdfDocumentFactory::class);

        return $record instanceof CheckIn
            ? $factory->forCheckIn($record)
            : $factory->forAppointment($record);
    }
}
