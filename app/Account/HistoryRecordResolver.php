<?php

declare(strict_types=1);

namespace App\Account;

use App\Address\AddressManager;
use App\Models\Appointment;
use App\Models\CheckIn;
use Illuminate\Support\Facades\Date;

final readonly class HistoryRecordResolver
{
    public function __construct(private AddressManager $address) {}

    /**
     * @return array<string, mixed>
     */
    public function checkIn(CheckIn $checkIn): array
    {
        $location = $checkIn->location;
        $when = $checkIn->created_at->setTimezone($location->timezone);

        /** @var list<string> $purchaseOrders */
        $purchaseOrders = $checkIn->purchaseOrders->pluck('number')->all();

        return [
            'uuid' => $checkIn->uuid,
            'referenceNumber' => $checkIn->reference_number,
            'status' => $checkIn->status->value,
            'date' => $when->format('M j, Y'),
            'time' => $when->format('g:i A T'),
            'locationName' => $location->name,
            'locationAddress' => $this->address->buildAddress($location->address->toArray()),
            'customer' => $checkIn->customer,
            'destination' => $this->destination($checkIn),
            'truckName' => $checkIn->truck_name,
            'truckPlate' => $this->plate($checkIn->truck_plate, $checkIn->truck_plate_state, $checkIn->truck_plate_country),
            'truckColor' => $checkIn->truck_color,
            'trailerPlate' => $this->plate($checkIn->trailer_plate, $checkIn->trailer_plate_state, $checkIn->trailer_plate_country),
            'trailerChute' => $checkIn->trailer_chute?->value,
            'emptyWeightLbs' => $checkIn->empty_weight_lbs,
            'driversName' => $checkIn->drivers_name,
            'driversCellphone' => $checkIn->drivers_cellphone,
            'driversEmail' => $checkIn->drivers_email,
            'licenseMasked' => $this->maskLicense($checkIn->drivers_license_number),
            'licenseState' => $checkIn->drivers_license_state,
            'licenseExpiration' => $this->calendarDate($checkIn->drivers_license_expiration_date),
            'loadingInstructions' => $checkIn->loading_instructions,
            'purchaseOrders' => $purchaseOrders,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function appointment(Appointment $appointment): array
    {
        $location = $appointment->location;
        $when = $appointment->scheduled_for->setTimezone($location->timezone);
        $cancelledAt = $appointment->cancelled_at?->setTimezone($location->timezone);

        /** @var list<string> $purchaseOrders */
        $purchaseOrders = $appointment->purchaseOrders->pluck('number')->all();

        return [
            'uuid' => $appointment->uuid,
            'referenceNumber' => $appointment->reference_number,
            'status' => $appointment->status->value,
            'date' => $when->format('M j, Y'),
            'time' => $when->format('g:i A T'),
            'isUpcoming' => $appointment->scheduled_for->isFuture(),
            'locationName' => $location->name,
            'locationAddress' => $this->address->buildAddress($location->address->toArray()),
            'driversName' => $appointment->drivers_name,
            'driversCellphone' => $appointment->drivers_cellphone,
            'cancelledAt' => $cancelledAt?->format('M j, Y'),
            'cancelledReason' => $appointment->cancelled_reason,
            'purchaseOrders' => $purchaseOrders,
        ];
    }

    private function destination(CheckIn $checkIn): string
    {
        return implode(', ', array_filter([
            $checkIn->destination_city,
            $checkIn->destination_state,
            $checkIn->destination_country,
        ]));
    }

    private function plate(string $plate, ?string $state, ?string $country): string
    {
        $origin = implode(' ', array_filter([$state, $country]));

        return $origin === '' ? $plate : sprintf('%s (%s)', $plate, $origin);
    }

    /**
     * Never expose the full number. The driver already knows it, and the column
     * is encrypted at rest under a dedicated key.
     */
    private function maskLicense(?string $number): ?string
    {
        if ($number === null || $number === '') {
            return null;
        }

        return str_repeat('•', max(0, mb_strlen($number) - 4)).mb_substr($number, -4);
    }

    /**
     * A calendar date carries no time or zone, so it is formatted as-is. Running
     * it through setTimezone() would shift it a day for anyone west of UTC.
     */
    private function calendarDate(?string $date): ?string
    {
        return $date === null || $date === ''
            ? null
            : Date::parse($date)->format('M j, Y');
    }
}
