<?php

namespace Atlantis;

use PDO;
use PDOException;
use PDOStatement;

final class Database
{
    public PDO $pdo;
    public PDOStatement $statement;
    private string $type;
    private string $host;
    private string $name;
    private string $charset;
    private string $port;
    private string $user;
    private string $pass;
    private array $options;

    function __construct(
        string $type = 'mysql',
        string $host = 'localhost',
        string $name = '',
        string $charset = 'utf8',
        string $port = '3306',
        string $user = '',
        string $pass = '',
        array $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => true,
            PDO::ATTR_PERSISTENT => true
        ]
    ) {
        $this->type = $type;
        $this->host = $host;
        $this->name = $name;
        $this->charset = $charset;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
        $this->options = $options;
        $this->connect();
    }

    private function connect()
    {
        try {
            $dsn = "{$this->type}:host={$this->host}"
                . ";dbname={$this->name}"
                . ";charset={$this->charset}"
                . ";port={$this->port}";

            $this->pdo = new PDO(
                $dsn,
                $this->user,
                $this->pass,
                $this->options
            );
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function execute(string $sql, array $values = []): bool
    {
        try {
            $this->statement = $this->pdo->prepare($sql);
            return $this->statement->execute($values);
        } catch (PDOException $e) {
            die($e->getMessage() . ". SQL: {$sql}");
        }
    }

    public function lastInsertId(string $name = null): string
    {
        return $this->pdo->lastInsertId($name);
    }
}
