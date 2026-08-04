<?php

declare(strict_types=1);

namespace App\Mail;

use App\Address\AddressManager;
use App\Models\Appointment;
use App\Phone\PhoneFormatter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class AppointmentBooked extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Appointment $appointment) {}

    public function envelope(): Envelope
    {
        $this->appointment->loadMissing('location');

        return new Envelope(
            subject: sprintf(
                'New Appointment %s - %s',
                $this->appointment->reference_number,
                $this->appointment->location->name,
            ),
        );
    }

    public function content(): Content
    {
        $this->appointment->loadMissing(['location.address', 'purchaseOrders']);

        $location = $this->appointment->location;
        $scheduledFor = $this->appointment->scheduled_for->setTimezone($location->timezone);

        return new Content(
            view: 'mail.notification',
            with: [
                'preheader' => sprintf('Appointment %s at %s', $this->appointment->reference_number, $location->name),
                'eyebrow' => 'New Appointment',
                'title' => 'A new appointment has been booked',
                'intro' => 'A driver has booked an appointment at your location. The details are below.',
                'referenceLabel' => 'Reference',
                'referenceNumber' => $this->appointment->reference_number,
                'rows' => [
                    ['label' => 'Location', 'value' => $location->name, 'sub' => resolve(AddressManager::class)->buildAddress($location->address->toArray())],
                    ['label' => 'Date', 'value' => $scheduledFor->format('F j, Y')],
                    ['label' => 'Time', 'value' => $scheduledFor->format('g:i A T')],
                    ['label' => 'PO Number(s)', 'value' => implode(', ', $this->appointment->purchaseOrders->pluck('number')->all())],
                    ['label' => 'Driver', 'value' => $this->appointment->drivers_name],
                    ['label' => "Driver's Phone Number", 'value' => resolve(PhoneFormatter::class)->format($this->appointment->drivers_cellphone)],
                ],
            ],
        );
    }
}
