<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Appointment;
use App\Models\CheckIn;

final readonly class CreatePurchaseOrdersAction
{
    /**
     * @param  array<int, string>  $poNumbers
     */
    public function handle(Appointment|CheckIn $purchasable, array $poNumbers): void
    {
        $purchasable->purchaseOrders()->createMany(
            array_map(fn (string $number): array => ['number' => $number], $poNumbers)
        );
    }
}
