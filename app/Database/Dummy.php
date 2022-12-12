<?php

declare(strict_types=1);

namespace App\Database;

use Tischmann\Atlantis\Migration;

class Dummy extends Migration
{
    public static function name(): string
    {
        return 'dummy';
    }
}
