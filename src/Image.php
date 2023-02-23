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
final class Image
{
    /**
     * Изменение размеров изображения
     * 
     * @param GdImage $image Изображение
     * @param int $dst_width Ширина на выходе
     * @param int $dst_height Высота на выходе
     */
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
            throw new Exception(Locale::get('image_resize_error'));
        }

        return $output;
    }
}
