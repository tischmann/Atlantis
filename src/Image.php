<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Exception;
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

    public static function resize(
        GdImage $image,
        int $dst_width = 800,
        int $dst_height = 600
    ): GdImage {
        $image_width = imagesx($image);

        $image_height = imagesy($image);

        $source_width = $image_width;

        $source_height = $image_height;

        $source_ratio = $image_width / $image_height;

        $output_ratio = $dst_width / $dst_height;

        $src_x = 0;

        $src_y = 0;

        $dst_x = 0;

        $dst_y = 0;

        if ($source_ratio >= $output_ratio) { // Исходное изображение шире
            $source_width = intval($source_height * $output_ratio);
            $src_x = intval(($image_width - $source_width) / 2);
        } else { // Исходное изображение выше
            $source_height = intval($source_width / $output_ratio);
            $src_y = intval(($image_height - $source_height) / 2);
        }

        $output = imagecreatetruecolor($dst_width, $dst_height);

        if (!imagecopyresampled(
            $output,
            $image,
            $dst_x,
            $dst_y,
            $src_x,
            $src_y,
            $dst_width,
            $dst_height,
            $source_width,
            $source_height
        )) {
            throw new Exception('Не удалось изменить размер изображения');
        }

        return $output;
    }
}
