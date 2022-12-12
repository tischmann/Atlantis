<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Dummy as DatabaseDummy;

use DateTime;

use Tischmann\Atlantis\{Migration, Model};

class Dummy extends Model
{
    public function __construct()
    {
        parent::__construct(id: rand(1, 10000), created_at: new DateTime());
    }

    public static function table(): Migration
    {
        return new DatabaseDummy();
    }

    public function insert(): bool
    {
        $this->created_at = new DateTime();
        $this->id = rand(1, 10000);
        return true;
    }

    public function update(): bool
    {
        $this->updated_at = new DateTime();
        return true;
    }

    public static function find(mixed $value, string|array $column = 'id'): self
    {
        return new self();
    }
}
