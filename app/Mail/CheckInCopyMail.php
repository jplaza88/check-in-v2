<?php

declare(strict_types=1);

namespace App\Mail;

use App\Account\HistoryRecordResolver;
use App\Mail\Concerns\AttachesRecordPdf;
use App\Mail\Concerns\FiltersBlankRows;
use App\Models\CheckIn;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The driver's own copy of a check-in, with the PDF receipt attached.
 *
 * Distinct from {@see CheckInCompleted}, which tells the shipping department
 * someone has arrived. This one is addressed to the driver, is translated, and
 * shows only the fields their location actually collects.
 */
final class CheckInCopyMail extends Mailable implements ShouldQueue
{
    use AttachesRecordPdf;
    use FiltersBlankRows;
    use Queueable;
    use SerializesModels;

    public function __construct(public CheckIn $checkIn) {}

    public function envelope(): Envelope
    {
        $this->checkIn->loadMissing('location');

        return new Envelope(
            subject: __('messages.checkInCopyEmail.subject', [
                'reference' => $this->checkIn->reference_number,
                'location' => $this->checkIn->location->name,
            ]),
        );
    }

    public function content(): Content
    {
        $this->checkIn->loadMissing(['location.address', 'purchaseOrders']);

        /*
         * The resolver already masks the licence, formats phones and returns
         * null for anything this location does not collect, so the row filter
         * below is all that stands between config-driven fields and a body full
         * of blank rows.
         */
        $record = resolve(HistoryRecordResolver::class)->checkIn($this->checkIn);

        return new Content(
            view: 'mail.notification',
            with: [
                'preheader' => __('messages.checkInCopyEmail.preheader', [
                    'location' => $record['locationName'],
                ]),
                'eyebrow' => __('messages.checkInCopyEmail.eyebrow'),
                'title' => __('messages.checkInCopyEmail.title'),
                'intro' => __('messages.checkInCopyEmail.intro'),
                'referenceLabel' => __('messages.accountHistoryRecord.reference'),
                'referenceNumber' => $record['referenceNumber'],
                'rows' => $this->presentRows([
                    ['label' => __('messages.accountHistoryRecord.locationHeading'), 'value' => $record['locationName'], 'sub' => $record['locationAddress']],
                    ['label' => __('messages.accountHistoryRecord.date'), 'value' => $record['date']],
                    ['label' => __('messages.accountHistoryRecord.time'), 'value' => $record['time']],
                    ['label' => __('messages.accountHistoryRecord.customer'), 'value' => $record['customer']],
                    ['label' => __('messages.accountHistoryRecord.destination'), 'value' => $record['destination']],
                    ['label' => __('messages.accountHistoryRecord.purchaseOrders'), 'value' => implode(', ', $record['purchaseOrders'])],
                    ['label' => __('messages.accountHistoryRecord.truckName'), 'value' => $record['truckName']],
                    ['label' => __('messages.accountHistoryRecord.truckPlate'), 'value' => $record['truckPlate']],
                    ['label' => __('messages.accountHistoryRecord.truckColor'), 'value' => $record['truckColor']],
                    ['label' => __('messages.accountHistoryRecord.trailerPlate'), 'value' => $record['trailerPlate']],
                    ['label' => __('messages.accountHistoryRecord.trailerChute'), 'value' => $record['trailerChute']],
                    ['label' => __('messages.accountHistoryRecord.emptyWeight'), 'value' => $record['emptyWeightLbs']],
                ]),
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return $this->recordPdfAttachments($this->checkIn);
    }
}
