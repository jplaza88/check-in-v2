<?php

declare(strict_types=1);

namespace App\Services;

class AddressService
{
    /**
     * @param  array<string, mixed>  $address
     */
    public function buildAddress(array $address): string
    {
        $parts = array_filter([
            $address['street1'],
            $address['street2'] ?? null,
            $address['city'],
            $address['state'],
        ]);

        return implode(', ', $parts).' '.$address['zip_code'];
    }
}
