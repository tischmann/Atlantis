<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\{Views};

use DateTime;

use Tischmann\Atlantis\{Migration, Model};

/**
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
class View extends Model
{
    public function __construct(
        public int $id = 0,
        public int $article_id = 0,
        public string $uuid = '',
        public ?DateTime $created_at = null,
        public ?DateTime $updated_at = null,
    ) {
    }

    public static function table(): Migration
    {
        return new Views();
    }
}
