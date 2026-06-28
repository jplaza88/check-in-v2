<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class AppointmentConfirmationDTO
{
    /**
     * @param  list<string>  $purchaseOrders
     */
    public function __construct(
        public string $uuid,
        public string $referenceNumber,
        public string $scheduledDate,
        public string $scheduledTime,
        public string $driversName,
        public string $locationName,
        public string $locationAddress,
        public array $purchaseOrders,
    ) {}
}
