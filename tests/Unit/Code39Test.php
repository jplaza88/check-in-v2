<?php

declare(strict_types=1);

use App\Barcode\Code39;

const NARROW = 2;
const WIDE = 6;
const QUIET_ZONE = 20;

/**
 * @return array<string, string>
 */
function code39Patterns(): array
{
    /** @var array<string, string> $patterns */
    $patterns = (new ReflectionClass(Code39::class))->getConstant('PATTERNS');

    return $patterns;
}

/**
 * @return list<array{x: int, width: int}>
 */
function barsFrom(string $svg): array
{
    preg_match_all('/<rect x="(\d+)" y="0" width="(\d+)"/', $svg, $matches, PREG_SET_ORDER);

    return array_map(
        fn (array $m): array => ['x' => (int) $m[1], 'width' => (int) $m[2]],
        $matches,
    );
}

/**
 * Reads the rendered geometry back into characters. This is what proves the
 * bar/space alternation, element widths and inter-character gaps are laid out
 * the way the table says, rather than merely being self-consistent.
 */
function decodeCode39(string $svg): string
{
    $bars = barsFrom($svg);
    $lookup = array_flip(code39Patterns());
    $decoded = '';
    $counter = count($bars);

    // Five bars per character, four spaces between them.
    for ($character = 0; $character * 5 < $counter; $character++) {
        $pattern = '';

        for ($bar = 0; $bar < 5; $bar++) {
            $current = $bars[$character * 5 + $bar];
            $pattern .= $current['width'] === WIDE ? 'w' : 'n';

            if ($bar === 4) {
                continue;
            }

            $next = $bars[$character * 5 + $bar + 1];
            $space = $next['x'] - ($current['x'] + $current['width']);
            $pattern .= $space === WIDE ? 'w' : 'n';
        }

        $decoded .= $lookup[$pattern] ?? '?';
    }

    return $decoded;
}

// ── Layer 1: structural invariant ────────────────────────────────────────────

it('encodes every character as nine elements with exactly three wide', function (): void {
    foreach (code39Patterns() as $character => $pattern) {
        expect(mb_strlen($pattern))->toBe(9, sprintf('"%s" is not 9 elements', $character))
            ->and(mb_substr_count($pattern, 'w'))
            ->toBe(3, sprintf('"%s" does not have exactly 3 wide elements', $character))
            ->and(mb_substr_count($pattern, 'n'))
            ->toBe(6, sprintf('"%s" does not have exactly 6 narrow elements', $character));
    }
});

it('has a unique pattern for every character', function (): void {
    $patterns = code39Patterns();

    expect(array_unique($patterns))->toHaveCount(count($patterns));
});

it('covers the full reference number alphabet', function (): void {
    // The alphabet CreateCheckInAction draws reference numbers from.
    foreach (mb_str_split('ABCDEFGHJKMNPQRSTUVWXYZ23456789') as $character) {
        expect(code39Patterns())->toHaveKey($character);
    }
});

// ── Layer 2: golden values, transcribed independently ────────────────────────

it('matches the published pattern for known characters', function (string $character, string $expected): void {
    expect(code39Patterns()[$character])->toBe($expected);
})->with([
    'zero' => ['0', 'nnnwwnwnn'],
    'one' => ['1', 'wnnwnnnnw'],
    'nine' => ['9', 'nnwwnnwnn'],
    'A' => ['A', 'wnnnnwnnw'],
    'Z' => ['Z', 'nwwnwnnnn'],
    'delimiter' => ['*', 'nwnnwnwnn'],
]);

// ── Layer 3: round trip through the rendered SVG ─────────────────────────────

it('renders a barcode that decodes back to the original value', function (string $value): void {
    $svg = (new Code39)->svg($value, NARROW);

    // The payload is wrapped in the * delimiter at both ends.
    expect(decodeCode39($svg))->toBe('*'.$value.'*');
})->with([
    'reference number' => ['A1B2C3D4'],
    'all letters' => ['ABCDEFGH'],
    'all digits' => ['23456789'],
    'single character' => ['A'],
    'ambiguity-free alphabet' => ['JKMNPQRS'],
]);

it('separates characters with exactly one narrow gap', function (): void {
    $bars = barsFrom((new Code39)->svg('A1B2C3D4', NARROW));

    // The gap after every fifth bar, except the last, is the inter-character gap.
    for ($character = 0; $character < 9; $character++) {
        $last = $bars[$character * 5 + 4];
        $next = $bars[$character * 5 + 5];

        expect($next['x'] - ($last['x'] + $last['width']))
            ->toBe(NARROW, sprintf('gap after character %d is not narrow', $character));
    }
});

// ── Layer 4: guards ──────────────────────────────────────────────────────────

it('leaves a quiet zone of ten narrow widths on both sides', function (): void {
    $svg = (new Code39)->svg('A1B2C3D4', NARROW);
    $bars = barsFrom($svg);

    preg_match('/viewBox="0 0 (\d+) /', $svg, $viewBox);
    $totalWidth = (int) $viewBox[1];
    $lastBar = $bars[count($bars) - 1];

    expect($bars[0]['x'])->toBe(QUIET_ZONE)
        ->and($totalWidth - ($lastBar['x'] + $lastBar['width']))->toBe(QUIET_ZONE);
});

it('uppercases lowercase input rather than rejecting it', function (): void {
    $lower = (new Code39)->svg('a1b2c3d4', NARROW);
    $upper = (new Code39)->svg('A1B2C3D4', NARROW);

    expect($lower)->toBe($upper);
});

it('rejects a value it cannot encode', function (string $value): void {
    expect(fn (): string => (new Code39)->svg($value))
        ->toThrow(InvalidArgumentException::class);
})->with([
    'empty' => [''],
    'whitespace only' => ['   '],
    'lowercase-safe but unsupported symbol' => ['A1B2#C3'],
    'accented' => ['CAFÉ'],
]);

it('scales every element with the narrow width', function (): void {
    $small = barsFrom((new Code39)->svg('A', 2));
    $large = barsFrom((new Code39)->svg('A', 4));

    foreach ($small as $index => $bar) {
        expect($large[$index]['width'])->toBe($bar['width'] * 2)
            ->and($large[$index]['x'])->toBe($bar['x'] * 2);
    }
});
