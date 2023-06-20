<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use App\Models\{User};

use BadMethodCallException;

use Exception;

class Controller
{
    public function __call($name, $arguments): mixed
    {
        throw new BadMethodCallException(
            Locale::get('method_not_found') . ": {$name}",
            404
        );
    }

    public static function setTitle(string $title): void
    {
        putenv('APP_TITLE=' . getenv('APP_TITLE') . " - " . $title);
    }

    protected function checkAdmin(): void
    {
        if (!User::current()->isAdmin()) {
            throw new Exception(Locale::get('access_denied'), 404);
        }
    }

    protected function sort(Query &$query, Request $request): Query
    {
        $sort = $request->request('sort') ?: 'id';

        $order = $request->request('order') ?: 'desc';

        return $query->order($sort, $order);
    }

    protected function search(
        Query &$query,
        Request $request,
        array $columns
    ): Query {
        $search = strval($request->request('query'));

        $search = strip_tags($search);

        if (mb_strlen($search) == 0) return $query;

        if ($search) {
            $query->where(function (&$nested) use ($columns, $search) {
                foreach ($columns as $column) {
                    $nested->orWhere($column, 'LIKE', "%{$search}%");
                }
            });
        }

        return $query;
    }

    /**
     * Динамическая подгрузка
     */
    public function fetch(
        Request $request,
        Query $query,
        callable $callback,
        int $limit
    ): void {
        $html = '';

        $page = intval($request->request('page') ?? 1);

        $next = intval($request->request('next') ?? 1);

        $last = intval($request->request('last') ?? 1);

        $total = 0;

        $limit = intval($request->request('limit') ?? $limit);

        $total = $query->count();

        $pagination = new Pagination(
            total: $total,
            page: $next,
            limit: $limit
        );

        if ($page < $last) {
            $offset = $pagination->offset;

            if ($limit) $query->limit($limit);

            if ($offset) $query->offset($offset);

            $html .= $callback($query);
        }

        Response::json([
            'html' => $html,
            ...get_object_vars($pagination)
        ]);
    }

    public function uploadImage(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'path' => ['required'],
        ]);

        $width = intval($request->request('width'));

        $height = intval($request->request('height'));

        $max_width = intval($request->request('max_width'));

        $max_height = intval($request->request('max_height'));

        $thumb_width = intval($request->request('thumb_width') ?? 400);

        $thumb_height = intval($request->request('thumb_height') ?? 300);

        $path =  $request->request('path');

        $thumbPath = $request->request('thumb_path');

        $quality = intval($request->request('quality') ?? 80);

        if ($thumbPath) {
            if (!is_dir($thumbPath)) {
                mkdir($thumbPath, 0775, true);
            }
        }

        if (!is_dir($path)) {
            mkdir($path, 0775, true);
        }

        reset($_FILES);

        $temp = current($_FILES);

        if (!is_uploaded_file($temp['tmp_name'])) {
            throw new Exception(Locale::get('error_upload_file'));
        }

        if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
            throw new Exception(Locale::get('error_invalid_filename'));
        }

        $extensions = ["gif", "jpg", "png", "webp", "jpeg", "bmp"];

        $fileExtension = strtolower(
            pathinfo(
                $temp['name'],
                PATHINFO_EXTENSION
            )
        );

        if (!in_array($fileExtension, $extensions)) {
            throw new Exception(Locale::get('error_unsupported_image_type'));
        }

        $filename = md5(bin2hex(random_bytes(128)));

        $filetowrite = "{$path}/{$filename}.webp";

        $thumbtowrite = "";

        if ($thumbPath) {
            $thumbtowrite = "{$path}/thumb_{$filename}.webp";
        }

        switch ($fileExtension) {
            case 'gif':
                $im = imagecreatefromgif($temp['tmp_name']);
                break;
            case 'jpg':
            case 'jpeg':
                $im = imagecreatefromjpeg($temp['tmp_name']);
                break;
            case 'png':
                $im = imagecreatefrompng($temp['tmp_name']);
                break;
            case 'bmp':
                $im = imagecreatefrombmp($temp['tmp_name']);
                break;
            case 'webp':
                $im = imagecreatefromwebp($temp['tmp_name']);
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

        $result = true;

        if ($thumbPath) {
            $thumb = Image::resize($im, $thumb_width, $thumb_height);

            $result = imagewebp($thumb, $thumbtowrite, $quality);
        }

        $result = $result && imagewebp($image, $filetowrite, $quality);

        if (!$result) {
            throw new Exception(Locale::get('error_image_to_webp'));
        }

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'
            ? "https://"
            : "http://";

        $baseurl = $protocol . $_SERVER["HTTP_HOST"];

        $location =  "{$baseurl}/{$filetowrite}";

        $thumb_location = "{$baseurl}/{$thumbtowrite}";

        Response::json([
            'image' => basename($location),
            'thumb' => basename($thumb_location),
            'location' => $location,
            'thumb_location' => $thumb_location,
        ]);
    }
}
