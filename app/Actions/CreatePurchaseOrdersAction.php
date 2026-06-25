<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Appointment;

final readonly class CreatePurchaseOrdersAction
{
    /**
     * @param  array<int, string>  $poNumbers
     */
    public function handle(Appointment $appointment, array $poNumbers): void
    {
        $appointment->purchaseOrders()->createMany(
            array_map(fn (string $number): array => ['number' => $number], $poNumbers)
        );
    }
}
