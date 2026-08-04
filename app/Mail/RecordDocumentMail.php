<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Appointment;
use App\Models\CheckIn;
use App\Pdf\RecordPdfDocument;
use App\Pdf\RecordPdfDocumentFactory;
use App\Pdf\RecordPdfStore;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class RecordDocumentMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public CheckIn|Appointment $record) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('messages.recordDocumentEmail.subject', [
                'reference' => $this->record->reference_number,
            ]),
        );
    }

    public function content(): Content
    {
        $this->record->loadMissing('location');

        return new Content(
            view: 'mail.notification',
            with: [
                'preheader' => __('messages.recordDocumentEmail.preheader'),
                'eyebrow' => __('messages.recordDocumentEmail.eyebrow'),
                'title' => __('messages.recordDocumentEmail.title'),
                'intro' => __('messages.recordDocumentEmail.intro'),
                'referenceLabel' => __('messages.accountHistoryRecord.reference'),
                'referenceNumber' => $this->record->reference_number,
                'rows' => [
                    [
                        'label' => __('messages.accountHistoryRecord.locationHeading'),
                        'value' => $this->record->location->name,
                    ],
                ],
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        // Resolved here rather than in the constructor: services do not survive
        // SerializesModels, and rendering lazily keeps ~150KB of PDF out of the
        // Redis job payload. The closure runs in the worker at send time.
        return [
            Attachment::fromData(fn (): string => $this->pdf(), $this->document()->fileName)
                ->withMime('application/pdf'),
        ];
    }

    private function pdf(): string
    {
        return resolve(RecordPdfStore::class)->bytes($this->document());
    }

    private function document(): RecordPdfDocument
    {
        $factory = resolve(RecordPdfDocumentFactory::class);

        return $this->record instanceof CheckIn
            ? $factory->forCheckIn($this->record)
            : $factory->forAppointment($this->record);
    }
}
