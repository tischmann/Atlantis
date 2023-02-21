<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\{Ratings};

use DateTime;

use Tischmann\Atlantis\{Migration, Model};

/**
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
class Rating extends Model
{
    public function __construct(
        public int $id = 0,
        public int $article_id = 0,
        public string $uuid = '',
        public float $rating = 0,
        public ?DateTime $created_at = null,
        public ?DateTime $updated_at = null,
    ) {
    }

    public static function table(): Migration
    {
        return new Ratings();
    }
}
