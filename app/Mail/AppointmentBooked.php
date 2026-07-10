<?php

declare(strict_types=1);

namespace App\Mail;

use App\Address\AddressManager;
use App\Models\Appointment;
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
            view: 'mail.appointment-booked',
            with: [
                'referenceNumber' => $this->appointment->reference_number,
                'locationName' => $location->name,
                'locationAddress' => resolve(AddressManager::class)->buildAddress($location->address->toArray()),
                'scheduledDate' => $scheduledFor->format('F j, Y'),
                'scheduledTime' => $scheduledFor->format('g:i A T'),
                'purchaseOrders' => $this->appointment->purchaseOrders->pluck('number')->all(),
                'driversName' => $this->appointment->drivers_name,
                'driversPhone' => $this->formatPhone($this->appointment->drivers_cellphone),
            ],
        );
    }

    /**
     * "+15551234567" -> "+1 (555) 123-4567" for readability.
     */
    private function formatPhone(string $phone): string
    {
        if (preg_match('/^\+1(\d{3})(\d{3})(\d{4})$/', $phone, $matches) === 1) {
            return sprintf('+1 (%s) %s-%s', $matches[1], $matches[2], $matches[3]);
        }

        return $phone;
    }
}
