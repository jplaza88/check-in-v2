<?php

declare(strict_types=1);

namespace App\Appointment;

use App\Address\AddressManager;
use App\DTOs\AppointmentConfirmationDTO;
use App\Enums\AppointmentStatus;
use App\Models\Appointment;

final readonly class AppointmentConfirmationResolver
{
    public function __construct(private AddressManager $address) {}

    /**
     * @return array{appointment: AppointmentConfirmationDTO, contact: array{phone: string, email: string}}
     */
    public function resolve(string $uuid): array
    {
        $appointment = Appointment::with(['location.address', 'purchaseOrders'])
            ->where('uuid', $uuid)
            ->where('status', AppointmentStatus::Scheduled)
            ->firstOrFail();

        $location = $appointment->location;
        $scheduledFor = $appointment->scheduled_for->setTimezone($location->timezone);

        return [
            'appointment' => new AppointmentConfirmationDTO(
                uuid: $appointment->uuid,
                referenceNumber: $appointment->reference_number,
                scheduledDate: $scheduledFor->format('F j, Y'),
                scheduledTime: $scheduledFor->format('g:i A'),
                driversName: $appointment->drivers_name,
                locationName: $location->name,
                locationAddress: $this->address->buildAddress($location->address->toArray()),
                purchaseOrders: $appointment->purchaseOrders->pluck('number')->all(),
            ),
            'contact' => [
                'phone' => $location->phone,
                'email' => $location->email,
            ],
        ];
    }
}
