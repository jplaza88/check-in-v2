<?php

declare(strict_types=1);

use App\Models\Appointment;
use App\Models\PurchaseOrder;
use Illuminate\Database\UniqueConstraintViolationException;

it('attaches multiple purchase orders to an appointment', function (): void {
    $appointment = Appointment::factory()->create();

    PurchaseOrder::factory()->for($appointment, 'purchasable')->create(['number' => 'PO-00001']);
    PurchaseOrder::factory()->for($appointment, 'purchasable')->create(['number' => 'PO-00002']);

    expect($appointment->purchaseOrders()->count())->toBe(2);
});

it('allows the same po number across different appointments', function (): void {
    [$first, $second] = Appointment::factory()->count(2)->create();

    PurchaseOrder::factory()->for($first, 'purchasable')->create(['number' => 'PO-SHARED']);
    PurchaseOrder::factory()->for($second, 'purchasable')->create(['number' => 'PO-SHARED']);

    expect($first->purchaseOrders()->count())->toBe(1)
        ->and($second->purchaseOrders()->count())->toBe(1);
});

it('rejects a duplicate po number on the same appointment', function (): void {
    $appointment = Appointment::factory()->create();

    PurchaseOrder::factory()->for($appointment, 'purchasable')->create(['number' => 'PO-00001']);

    expect(fn () => PurchaseOrder::factory()->for($appointment, 'purchasable')->create(['number' => 'PO-00001']))
        ->toThrow(UniqueConstraintViolationException::class);
});

it('eager-loads purchase orders on an appointment', function (): void {
    $appointment = Appointment::factory()->create();

    PurchaseOrder::factory()->for($appointment, 'purchasable')->createMany([
        ['number' => 'PO-00001'],
        ['number' => 'PO-00002'],
    ]);

    $loaded = Appointment::with('purchaseOrders')->find($appointment->id);

    expect($loaded->relationLoaded('purchaseOrders'))->toBeTrue()
        ->and($loaded->purchaseOrders)->toHaveCount(2)
        ->and($loaded->purchaseOrders->pluck('number')->sort()->values()->all())
        ->toBe(['PO-00001', 'PO-00002']);
});
