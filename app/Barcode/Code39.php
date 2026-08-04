<?php

declare(strict_types=1);

namespace App\Barcode;

use InvalidArgumentException;
use Throwable;

/**
 * Code 39 (also written "Code 3 of 9") as inline SVG.
 *
 * Each character is nine elements, alternating bar/space and starting with a
 * bar: five bars, four spaces, of which exactly three are wide. Characters are
 * separated by one narrow space, and '*' delimits the payload at both ends.
 */
final readonly class Code39
{
    /**
     * Narrow (n) and wide (w) elements per character, bar first.
     *
     * Keyed by array-key rather than string because PHP silently casts the
     * numeric keys ('0'..'9') to integers. Lookups coerce the same way, so
     * self::PATTERNS[$character] still resolves for a string digit.
     *
     * @var array<array-key, string>
     */
    private const array PATTERNS = [
        '0' => 'nnnwwnwnn',
        '1' => 'wnnwnnnnw',
        '2' => 'nnwwnnnnw',
        '3' => 'wnwwnnnnn',
        '4' => 'nnnwwnnnw',
        '5' => 'wnnwwnnnn',
        '6' => 'nnwwwnnnn',
        '7' => 'nnnwnnwnw',
        '8' => 'wnnwnnwnn',
        '9' => 'nnwwnnwnn',
        'A' => 'wnnnnwnnw',
        'B' => 'nnwnnwnnw',
        'C' => 'wnwnnwnnn',
        'D' => 'nnnnwwnnw',
        'E' => 'wnnnwwnnn',
        'F' => 'nnwnwwnnn',
        'G' => 'nnnnnwwnw',
        'H' => 'wnnnnwwnn',
        'I' => 'nnwnnwwnn',
        'J' => 'nnnnwwwnn',
        'K' => 'wnnnnnnww',
        'L' => 'nnwnnnnww',
        'M' => 'wnwnnnnwn',
        'N' => 'nnnnwnnww',
        'O' => 'wnnnwnnwn',
        'P' => 'nnwnwnnwn',
        'Q' => 'nnnnnnwww',
        'R' => 'wnnnnnwwn',
        'S' => 'nnwnnnwwn',
        'T' => 'nnnnwnwwn',
        'U' => 'wwnnnnnnw',
        'V' => 'nwwnnnnnw',
        'W' => 'wwwnnnnnn',
        'X' => 'nwnnwnnnw',
        'Y' => 'wwnnwnnnn',
        'Z' => 'nwwnwnnnn',
        '-' => 'nwnnnnwnw',
        '.' => 'wwnnnnwnn',
        ' ' => 'nwwnnnwnn',
        '$' => 'nwnwnwnnn',
        '/' => 'nwnwnnnwn',
        '+' => 'nwnnnwnwn',
        '%' => 'nnnwnwnwn',
        '*' => 'nwnnwnwnn',
    ];

    private const string DELIMITER = '*';

    /**
     * Quiet zone either side, as a multiple of the narrow width. Ten is the
     * spec minimum and the most common reason a barcode that looks perfect
     * refuses to scan, so it is baked into the viewBox rather than left to
     * page padding.
     */
    private const int QUIET_ZONE_MULTIPLE = 10;

    /**
     * Wide elements are three times a narrow one. The spec allows 2:1 through
     * 3:1; the wider ratio is the more forgiving to read.
     */
    private const int WIDE_MULTIPLE = 3;

    /**
     * @throws InvalidArgumentException|Throwable when the value cannot be encoded
     */
    public function svg(string $value, int $narrow = 2, int $height = 56): string
    {
        $encoded = mb_strtoupper(mb_trim($value));

        throw_if($encoded === '', InvalidArgumentException::class, 'Cannot encode an empty value as Code 39.');

        $wide = $narrow * self::WIDE_MULTIPLE;
        $quietZone = $narrow * self::QUIET_ZONE_MULTIPLE;

        $x = $quietZone;
        $bars = [];

        foreach (mb_str_split(self::DELIMITER.$encoded.self::DELIMITER) as $index => $character) {
            if (! isset(self::PATTERNS[$character])) {
                throw new InvalidArgumentException(
                    sprintf('Character "%s" cannot be encoded as Code 39.', $character)
                );
            }

            if ($index > 0) {
                // Inter-character gap: always one narrow space.
                $x += $narrow;
            }

            foreach (mb_str_split(self::PATTERNS[$character]) as $element => $size) {
                $width = $size === 'w' ? $wide : $narrow;

                // Even indexes are bars, odd are spaces. Only bars are drawn.
                if ($element % 2 === 0) {
                    $bars[] = sprintf(
                        '<rect x="%d" y="0" width="%d" height="%d"/>',
                        $x,
                        $width,
                        $height,
                    );
                }

                $x += $width;
            }
        }

        $width = $x + $quietZone;

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d" width="%d" height="%d" '.
            'shape-rendering="crispEdges" role="img" aria-label="%s" fill="#111827">%s</svg>',
            $width,
            $height,
            $width,
            $height,
            e($encoded),
            implode('', $bars),
        );
    }
}
