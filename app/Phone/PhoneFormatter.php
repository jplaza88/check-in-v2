<?php

declare(strict_types=1);

namespace App\Phone;

final class PhoneFormatter
{
    /**
     * "+15551234567" -> "+1 (555) 123-4567" for readability.
     *
     * Anything that is not a US E.164 number is returned untouched, so a
     * partially-entered or international number still renders.
     */
    public function format(?string $phone): ?string
    {
        if ($phone === null || $phone === '') {
            return null;
        }

        if (preg_match('/^\+1(\d{3})(\d{3})(\d{4})$/', $phone, $matches) === 1) {
            return sprintf('+1 (%s) %s-%s', $matches[1], $matches[2], $matches[3]);
        }

        return $phone;
    }
}
