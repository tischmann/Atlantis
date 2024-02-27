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
        int $dst_width,
        int $dst_height
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
        int $min_width = 0,
        int $min_height = 0,
        int $max_width = 0,
        int $max_height = 0,
        int $thumb_width = 400,
        int $thumb_height = 300,
        int $quality = 80
    ): array {
        if (!is_dir($path)) mkdir($path, 0775, true);

        $images = [];

        $names = $files['image']['name'] ?? [];

        if (!is_array($names)) $names = [$names];

        $tmp_names = $files['image']['tmp_name'] ?? [];

        if (!is_array($tmp_names)) $tmp_names = [$tmp_names];

        foreach ($names as $index => $name) {
            $tmp_name = $tmp_names[$index];

            if (!is_uploaded_file($tmp_name)) {
                throw new Exception(Locale::get('error_upload_file'));
            }

            if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $name)) {
                throw new Exception(Locale::get('error_invalid_filename'));
            }

            $extensions = ["gif", "jpg", "png", "webp", "jpeg", "bmp"];

            $extension = mb_strtolower(pathinfo($name, PATHINFO_EXTENSION));

            if (!in_array($extension, $extensions)) {
                throw new Exception(Locale::get('error_unsupported_image_type'));
            }

            $filename = md5(bin2hex(random_bytes(32)));

            $file_to_write = "{$path}/{$filename}.webp";

            $thumb_to_write = "{$path}/thumb_{$filename}.webp";

            switch ($extension) {
                case 'gif':
                    $im = imagecreatefromgif($tmp_name);
                    break;
                case 'jpg':
                case 'jpeg':
                    $im = imagecreatefromjpeg($tmp_name);
                    break;
                case 'png':
                    $im = imagecreatefrompng($tmp_name);
                    break;
                case 'bmp':
                    $im = imagecreatefrombmp($tmp_name);
                    break;
                case 'webp':
                    $im = imagecreatefromwebp($tmp_name);
                    break;
            }

            $src_width = $width = imagesx($im);

            $src_height = $height = imagesy($im);

            $min_ratio = $min_width / $min_height;

            $max_ratio = $max_width / $max_height;

            $src_ratio = $src_width / $src_height;

            if ($min_width) {
                $width = $min_width;
                $height = intval($width / $min_ratio);
            } else if ($max_width) {
                $width = $max_width;
                $height = intval($width / $max_ratio);
            }

            if ($min_height) {
                $height = $min_height;
                $width = intval($height * $min_ratio);
            } else if ($max_height) {
                $height = $max_height;
                $width = intval($height * $max_ratio);
            }

            $image = Image::resize($im, $width, $height);

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
