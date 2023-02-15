<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use GdImage;

/**
 * Изображения
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
class Image
{
    public static function createImageFromBase64(string $base64): GdImage|false
    {
        return imagecreatefromstring(base64_decode($base64));
    }

    public static function getBase64Data(string $base64, bool $decode = true): string
    {
        $data = explode(',', $base64);

        if ($decode) $data[1] = base64_decode($data[1]);

        return $data[1];
    }

    public static function savePng(GdImage $image, string $path): bool
    {
        return imagepng($image, $path, 0);
    }

    public static function saveWebp(GdImage $image, string $path): bool
    {
        return imagewebp($image, $path, 75);
    }

    public static function base64ToWebp(
        string $base64,
        string $path,
        int
        $quality = 80
    ): bool {
        $image = static::createImageFromBase64($base64);

        if (!$image) return false;

        return self::saveWebp($image, $path, $quality);
    }
}
