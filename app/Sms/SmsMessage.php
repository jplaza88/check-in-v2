<?php

declare(strict_types=1);

namespace App\Sms;

/**
 * One outbound text. Deliberately provider-agnostic: FlowRoute is the intended
 * carrier, but nothing here knows that.
 */
final readonly class SmsMessage
{
    public function __construct(
        public string $to,
        public string $body,
    ) {}
}
