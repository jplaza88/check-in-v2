<?php

declare(strict_types=1);

namespace App\Pdf;

/**
 * Assets embedded into the PDF templates as data URIs.
 *
 * Everything is inlined rather than linked because Chrome renders the template
 * from a temporary file with no dev server and no guaranteed network, so a
 * relative asset path or an @vite URL would silently resolve to nothing.
 *
 * Memoised because the files never change at runtime.
 */
final class PdfAssets
{
    /**
     * The masthead logo renders about 93px wide. The source is 2168px, which
     * embedded raw accounted for most of a 300KB document -- and this gets
     * downloaded by drivers on rural cellular. This width is still roughly 4x
     * oversampled for print.
     */
    private const int LOGO_WIDTH = 400;

    private static ?string $font = null;

    private static ?string $logo = null;

    /**
     * Figtree Variable, latin subset. Its unicode-range covers U+2000-206F, so
     * the bullet used by the masked licence number renders.
     */
    public static function fontDataUri(): string
    {
        return self::$font ??= self::dataUri(
            base_path('node_modules/@fontsource-variable/figtree/files/figtree-latin-wght-normal.woff2'),
            'font/woff2',
        );
    }

    public static function logoDataUri(): string
    {
        return self::$logo ??= self::downscaledPng(public_path('logo.png'))
            ?? self::dataUri(public_path('logo.png'), 'image/png');
    }

    /**
     * Null when GD cannot read the file, so the caller falls back to embedding
     * it untouched rather than losing the logo entirely.
     */
    private static function downscaledPng(string $path): ?string
    {
        $source = @imagecreatefrompng($path);

        if ($source === false) {
            return null;
        }

        $height = (int) round(imagesy($source) * (self::LOGO_WIDTH / imagesx($source)));

        // A degenerate source would make an invalid canvas; fall back instead.
        if ($height < 1) {
            return null;
        }

        $target = imagecreatetruecolor(self::LOGO_WIDTH, $height);

        // Preserve transparency instead of compositing onto black.
        imagealphablending($target, false);
        imagesavealpha($target, true);

        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);

        if ($transparent === false) {
            return null;
        }

        imagefill($target, 0, 0, $transparent);

        imagecopyresampled(
            $target, $source,
            0, 0, 0, 0,
            self::LOGO_WIDTH, $height,
            imagesx($source), imagesy($source),
        );

        ob_start();
        imagepng($target, null, 9);
        $contents = (string) ob_get_clean();

        return sprintf('data:image/png;base64,%s', base64_encode($contents));
    }

    private static function dataUri(string $path, string $mimeType): string
    {
        $contents = @file_get_contents($path);

        // A missing asset must not take the whole document down; the layout
        // degrades to the fallback font stack and a text wordmark.
        if ($contents === false) {
            return '';
        }

        return sprintf('data:%s;base64,%s', $mimeType, base64_encode($contents));
    }
}
