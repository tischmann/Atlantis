<?php

namespace Atlantis;

class Migration
{
    public Database $db;
    public Blueprint $blueprint;
    public bool $fresh = false;
    public bool $seed = false;
    public bool $import = false;
    public string $prefix = '';

    public function __construct($args = null)
    {
        $this->table = $this->prefix . $this->table;

        if (strpos($args, 'i') !== false) {
            $this->import = true;
        }

        if (strpos($args, 'f') !== false) {
            $this->fresh = true;
        }

        if (strpos($args, 's') !== false) {
            $this->seed = true;
        }

        $this->db = new Database(
            name: getenv('DB_NAME') ?: '',
            user: getenv('DB_USER') ?: '',
            pass: getenv('DB_PASS') ?: '',
        );
    }

    public function clean()
    {
        $this->drop();
        Console::line("Dropping table {$this->table}");
    }

    public function migrate()
    {
        if ($this->fresh) {
            $this->down();
            Console::line("Dropping table {$this->table}");
        }

        $this->up();

        Console::line("Creating table {$this->table}");

        if ($this->seed && !$this->import) {
            $this->seed();
        } else if ($this->import) {
            $this->import();
        }
    }

    public function create(): bool
    {
        $this->blueprint->datetime('created_at')
            ->null()
            ->default('CURRENT_TIMESTAMP');

        $this->blueprint->datetime('updated_at')
            ->null()
            ->update('CURRENT_TIMESTAMP');

        $this->db->execute("SET FOREIGN_KEY_CHECKS = 0");

        $result = $this->db->execute($this->getCreateSql());

        $this->db->execute("SET FOREIGN_KEY_CHECKS = 1");

        return $result;
    }

    protected function getCreateSql()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (" . PHP_EOL;

        $primary = null;
        $indexes = [];
        $uniques = [];
        $foreigns = [];

        foreach ($this->blueprint->columns as $name => $column) {
            if ($column->primary && !$primary) {
                $primary = $column;
            }

            if ($column->index) {
                $indexes[] = $column;
            }

            if ($column->unique) {
                $uniques[] = $column;
            }

            if ($column->foreign) {
                $foreigns[$column->column] = $column->foreign;
            }

            $sql .= "`{$column}` {$column->typeAndLength()} ";

            if (!$column->null) {
                $sql .= "NOT NULL";
            } else {
                $sql .= "DEFAULT {$column->getDefault()}";
            }

            if ($column->increment) {
                $sql .= " AUTO_INCREMENT";
            }

            if ($column->update) {
                $sql .= " ON UPDATE {$column->getUpdate()}";
            }

            if ($column->comment) {
                $sql .= " COMMENT '{$column->comment}'";
            }

            $sql .= "," . PHP_EOL;
        }

        if ($primary) {
            $sql .= "PRIMARY KEY (`{$primary}`)";
        }

        if ($foreigns) {
            if ($primary) {
                $sql .= "," . PHP_EOL;
            }

            foreach ($foreigns as $key => $val) {
                $foreignTable = $this->prefix . array_keys($val)[0];
                $foreignColumn = reset($val);
                $sql .= "FOREIGN KEY (`{$key}`) "
                    . "REFERENCES `{$foreignTable}` (`{$foreignColumn}`) "
                    . "ON UPDATE CASCADE ON DELETE RESTRICT," . PHP_EOL;
            }

            $sql = substr($sql, 0, -2);
        }

        if ($uniques) {
            if ($foreigns || $primary) {
                $sql .= "," . PHP_EOL;
            }

            foreach ($uniques as $key) {
                $sql .= "UNIQUE KEY `{$key}` (`{$key}`)," . PHP_EOL;
            }

            $sql = substr($sql, 0, -2);
        }

        if ($indexes) {
            if ($foreigns || $primary || $uniques) {
                $sql .= "," . PHP_EOL;
            }

            foreach ($indexes as $key) {
                $sql .= "KEY (`{$key}`)," . PHP_EOL;
            }

            $sql = substr($sql, 0, -2);
        }

        $sql .= ") ENGINE=INNODB, DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        return $sql;
    }

    protected function getDropSql(): string
    {
        return "DROP TABLE IF EXISTS `{$this->table}`;";
    }

    public function drop(): bool
    {
        $this->db->execute("SET FOREIGN_KEY_CHECKS = 0");

        $result = $this->db->execute($this->getDropSql());

        $this->db->execute("SET FOREIGN_KEY_CHECKS = 1");

        return $result;
    }

    protected function up(): bool
    {
        return true;
    }

    protected function down(): bool
    {
        return true;
    }

    public function seed(): bool
    {
        $insert = $this->insert();
        $total = count($insert);
        $step = 0;

        foreach ($insert as $row) {
            $columns = array_keys($row);

            $sql = "INSERT INTO `{$this->table}` (`"
                . implode('`, `', $columns) . "`) VALUES (:"
                . implode(", :", $columns) . ");";

            if (!$this->db->execute($sql, $row)) {
                return false;
            }

            Console::progress(++$step, $total, "Seeding table {$this->table}");
        }

        Console::line("Seeding table {$this->table}");

        return true;
    }

    public function insert(): array
    {
        return [];
    }

    public function import(): bool
    {
        Console::line("Importing table {$this->table}");
        return true;
    }
}
