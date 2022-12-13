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
    public const TINYMCE_IMG_REGEX = "<img\s+src=\"(data:image\/(\w+);base64,([^\"]+))\"([^\/]*)\/>";

    public const ARTICLES_IMAGES_DIR = 'images/articles';

    public static function getExtension(string $type): ?string
    {
        if (empty($type)) return null;

        switch ($type) {
            case 'image/bmp':
                return '.bmp';
            case 'image/cis-cod':
                return '.cod';
            case 'image/gif':
                return '.gif';
            case 'image/ief':
                return '.ief';
            case 'image/jpeg':
                return '.jpg';
            case 'image/pipeg':
                return '.jfif';
            case 'image/tiff':
                return '.tif';
            case 'image/x-cmu-raster':
                return '.ras';
            case 'image/x-cmx':
                return '.cmx';
            case 'image/x-icon':
                return '.ico';
            case 'image/x-portable-anymap':
                return '.pnm';
            case 'image/x-portable-bitmap':
                return '.pbm';
            case 'image/x-portable-graymap':
                return '.pgm';
            case 'image/x-portable-pixmap':
                return '.ppm';
            case 'image/x-rgb':
                return '.rgb';
            case 'image/x-xbitmap':
                return '.xbm';
            case 'image/x-xpixmap':
                return '.xpm';
            case 'image/x-xwindowdump':
                return '.xwd';
            case 'image/png':
                return '.png';
            case 'image/x-jps':
                return '.jps';
            case 'image/x-freehand':
                return '.fh';
            default:
                return null;
        }
    }

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

    public static function saveTinyMceImages(string &$content, int $id, int $quality = 65): bool
    {
        preg_match_all(
            Image::TINYMCE_IMG_REGEX,
            $content,
            $matches,
            PREG_SET_ORDER
        );

        if (empty($matches)) return false;

        $root = getenv('APP_ROOT') . "/public";

        $articleDir = "{$root}/" . self::ARTICLES_IMAGES_DIR . "/{$id}";

        $saveDir = "{$articleDir}/images";

        if (!file_exists($articleDir)) {
            mkdir($articleDir, 0755, true);
        }

        if (!file_exists($saveDir)) {
            mkdir($saveDir, 0755, true);
        }

        foreach ($matches as $match) {
            $source = $match[1];

            $filename = md5(random_bytes(128)) . ".webp";

            $path =  "{$saveDir}/{$filename}";

            while (file_exists($path)) {
                $filename = md5(random_bytes(128)) . ".webp";

                $path = "{$saveDir}/{$filename}";
            }

            $image = Image::createImageFromBase64($match[3]);

            if ($image) {
                imagewebp($image, $path, $quality);

                $content = str_replace(
                    $source,
                    self::ARTICLES_IMAGES_DIR . "/{$id}/images/{$filename}",
                    $content
                );
            }
        }

        return true;
    }

    public static function deleteTinyMceImages(int $id): bool
    {
        $dir = getenv('APP_ROOT') . "/public/" . self::ARTICLES_IMAGES_DIR . "/{$id}/images";

        $files = glob("{$dir}/*.webp");

        foreach ($files as $file) {
            unlink($file);
        }

        return true;
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
