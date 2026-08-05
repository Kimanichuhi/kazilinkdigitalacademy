<?php

namespace Modules\Core\Support;

/**
 * Resizes/re-encodes an image file in place using plain GD (no
 * intervention/image — this project avoids adding it as a Composer
 * dependency when a handful of GD calls do the same job). Animated GIFs are
 * left untouched: GD's single-frame API would silently flatten them to a
 * static image, which is worse than not optimizing at all.
 */
class ImageOptimizer
{
    public static function optimize(string $absolutePath, int $maxWidth = 1600, int $quality = 82): void
    {
        $info = @getimagesize($absolutePath);

        if (! $info) {
            return;
        }

        [$width, $height, $type] = $info;

        if (! $type || $type === IMAGETYPE_GIF || $width <= 0 || $height <= 0 || $width <= $maxWidth) {
            return;
        }

        $source = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($absolutePath),
            IMAGETYPE_PNG => @imagecreatefrompng($absolutePath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($absolutePath) : false,
            default => false,
        };

        if (! $source) {
            return;
        }

        $targetHeight = (int) round($height * ($maxWidth / $width));
        $resized = imagecreatetruecolor($maxWidth, $targetHeight);

        if ($type === IMAGETYPE_PNG) {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }

        imagecopyresampled($resized, $source, 0, 0, 0, 0, $maxWidth, $targetHeight, $width, $height);

        match ($type) {
            IMAGETYPE_JPEG => imagejpeg($resized, $absolutePath, $quality),
            IMAGETYPE_PNG => imagepng($resized, $absolutePath, 6),
            IMAGETYPE_WEBP => imagewebp($resized, $absolutePath, $quality),
            default => null,
        };

        imagedestroy($source);
        imagedestroy($resized);
    }
}
