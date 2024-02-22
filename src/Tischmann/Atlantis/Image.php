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

    public static function upload(
        array $files,
        string $path,
        int $width = 400,
        int $height = 300,
        int $max_width = 1920,
        int $max_height = 1080,
        int $thumb_width = 400,
        int $thumb_height = 300,
        int $quality = 80
    ): array {
        if (!is_dir($path)) mkdir($path, 0775, true);

        $images = [];

        foreach ($files as $file) {
            if (!is_uploaded_file($file['tmp_name'])) {
                throw new Exception(Locale::get('error_upload_file'));
            }

            if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $file['name'])) {
                throw new Exception(Locale::get('error_invalid_filename'));
            }

            $extensions = ["gif", "jpg", "png", "webp", "jpeg", "bmp"];

            $extension = mb_strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, $extensions)) {
                throw new Exception(Locale::get('error_unsupported_image_type'));
            }

            $filename = md5(bin2hex(random_bytes(32)));

            $file_to_write = "{$path}/{$filename}.webp";

            $thumb_to_write = "{$path}/thumb_{$filename}.webp";

            switch ($extension) {
                case 'gif':
                    $im = imagecreatefromgif($file['tmp_name']);
                    break;
                case 'jpg':
                case 'jpeg':
                    $im = imagecreatefromjpeg($file['tmp_name']);
                    break;
                case 'png':
                    $im = imagecreatefrompng($file['tmp_name']);
                    break;
                case 'bmp':
                    $im = imagecreatefrombmp($file['tmp_name']);
                    break;
                case 'webp':
                    $im = imagecreatefromwebp($file['tmp_name']);
                    break;
            }

            if ($max_width && $max_height) {
                $src_width = imagesx($im);

                $src_height = imagesy($im);

                $src_ratio = $src_width / $src_height;

                if ($src_width > $max_width) {
                    $width = $max_width;
                    $height = intval($width / $src_ratio);
                } else if ($src_height > $max_height) {
                    $height = $max_height;
                    $width = intval($height * $src_ratio);
                }
            }

            $image = $im;

            if ($width && $height) {
                $image = Image::resize($im, $width, $height);
            }

            $thumb = Image::resize($im, $thumb_width, $thumb_height);

            if (!imagewebp($thumb, $thumb_to_write, $quality)) {
                throw new Exception(Locale::get('error_create_webp'));
            }

            if (!imagewebp($image, $file_to_write, $quality)) {
                throw new Exception(Locale::get('error_create_webp'));
            }

            $images[] = "{$filename}.webp";
        }

        return $images;
    }
}
