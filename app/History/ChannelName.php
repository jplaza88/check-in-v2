<?php

declare(strict_types=1);

namespace App\History;

use App\Notifications\Channels\SmsChannel;

/**
 * Normalises the channel identifier Laravel reports into something an admin
 * screen can print.
 *
 * Built-in channels arrive as short names ('mail'), but a class-based channel
 * arrives as its fully-qualified name, which is not something to show anyone.
 */
final readonly class ChannelName
{
    /** @var array<class-string, string> */
    private const array ALIASES = [
        SmsChannel::class => 'sms',
    ];

    public function forChannel(string $channel): string
    {
        return self::ALIASES[$channel] ?? $channel;
    }
}
