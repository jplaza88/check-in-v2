<?php

declare(strict_types=1);

namespace App\Mail;

use App\Address\AddressManager;
use App\Models\CheckIn;
use App\Phone\PhoneFormatter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class CheckInCompleted extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public CheckIn $checkIn) {}

    public function envelope(): Envelope
    {
        $this->checkIn->loadMissing('location');

        return new Envelope(
            subject: sprintf(
                'New Check-In %s - %s',
                $this->checkIn->reference_number,
                $this->checkIn->location->name,
            ),
        );
    }

    public function content(): Content
    {
        $this->checkIn->loadMissing(['location.address', 'purchaseOrders']);

        $location = $this->checkIn->location;

        return new Content(
            view: 'mail.notification',
            with: [
                'preheader' => sprintf('Check-in %s at %s', $this->checkIn->reference_number, $location->name),
                'eyebrow' => 'New Check-In',
                'title' => 'A new check-in has arrived',
                'intro' => 'A driver has checked in at your location. The details are below.',
                'referenceLabel' => 'Reference',
                'referenceNumber' => $this->checkIn->reference_number,
                'rows' => [
                    ['label' => 'Location', 'value' => $location->name, 'sub' => resolve(AddressManager::class)->buildAddress($location->address->toArray())],
                    ['label' => 'Customer', 'value' => $this->checkIn->customer],
                    ['label' => 'Destination', 'value' => $this->destination()],
                    ['label' => 'PO Number(s)', 'value' => implode(', ', $this->checkIn->purchaseOrders->pluck('number')->all())],
                    ['label' => 'Driver', 'value' => $this->checkIn->drivers_name],
                    ['label' => "Driver's Phone Number", 'value' => resolve(PhoneFormatter::class)->format($this->checkIn->drivers_cellphone)],
                ],
            ],
        );
    }

    private function destination(): string
    {
        return sprintf(
            '%s, %s, %s',
            $this->checkIn->destination_city,
            $this->checkIn->destination_state,
            $this->checkIn->destination_country,
        );
    }
}
