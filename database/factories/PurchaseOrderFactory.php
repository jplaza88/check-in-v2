<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

final class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        return [
            'number' => mb_strtoupper($this->faker->bothify('PO-#####')),
        ];
    }
}
