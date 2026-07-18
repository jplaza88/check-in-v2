<?php

declare(strict_types=1);

namespace App\CheckIn;

use App\Address\AddressManager;
use App\DTOs\CheckInConfirmationDTO;
use App\Enums\CheckInStatus;
use App\Models\CheckIn;

final readonly class CheckInConfirmationResolver
{
    public function __construct(private AddressManager $address) {}

    /**
     * @return array{checkIn: CheckInConfirmationDTO, contact: array{phone: string, email: string}}
     */
    public function resolve(string $uuid): array
    {
        $checkIn = CheckIn::with(['location.address', 'purchaseOrders'])
            ->where('uuid', $uuid)
            ->where('status', CheckInStatus::Pending)
            ->firstOrFail();

        $location = $checkIn->location;

        /** @var list<string> $purchaseOrders */
        $purchaseOrders = $checkIn->purchaseOrders->pluck('number')->all();

        return [
            'checkIn' => new CheckInConfirmationDTO(
                uuid: $checkIn->uuid,
                referenceNumber: $checkIn->reference_number,
                customer: $checkIn->customer,
                destinationCity: $checkIn->destination_city,
                destinationState: $checkIn->destination_state,
                destinationCountry: $checkIn->destination_country,
                driversName: $checkIn->drivers_name,
                locationName: $location->name,
                locationAddress: $this->address->buildAddress($location->address->toArray()),
                purchaseOrders: $purchaseOrders,
            ),
            'contact' => [
                'phone' => $location->phone,
                'email' => $location->email,
            ],
        ];
    }
}
