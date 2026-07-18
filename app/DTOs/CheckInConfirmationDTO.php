<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class CheckInConfirmationDTO
{
    /**
     * @param  list<string>  $purchaseOrders
     */
    public function __construct(
        public string $uuid,
        public string $referenceNumber,
        public string $customer,
        public string $destinationCity,
        public string $destinationState,
        public string $destinationCountry,
        public string $driversName,
        public string $locationName,
        public string $locationAddress,
        public array $purchaseOrders,
    ) {}
}
