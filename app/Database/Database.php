<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private static ?Database $instance = null;

    private PDO $pdo;

    private function __construct(array $config)
    {
        $dsn = $config['dsn'];
        $username = $config['username'] ?? null;
        $password = $config['password'] ?? null;
        $options = $config['options'] ?? [];

        $defaultOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $this->pdo = new PDO(
            $dsn,
            $username,
            $password,
            array_replace($defaultOptions, $options)
        );
    }

    public static function getInstance(array $config = []): self
    {
        if (self::$instance === null) {
            if (empty($config)) {
                $config = require __DIR__ . '/../Config/database.php';
                $config = $config['database'];
            }

            self::$instance = new self($config);
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $this->bindParams($stmt, $params);
        $stmt->execute();

        return $stmt;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);

        return $stmt->fetchAll();
    }

    public function fetch(string $sql, array $params = []): array|false
    {
        $stmt = $this->query($sql, $params);

        return $stmt->fetch();
    }

    public function fetchColumn(string $sql, array $params = [], int $column = 0): mixed
    {
        $stmt = $this->query($sql, $params);

        return $stmt->fetchColumn($column);
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        $this->bindParams($stmt, $params);

        return $stmt->execute();
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    private function bindParams(PDOStatement $stmt, array $params): void
    {
        foreach ($params as $key => $value) {
            $type = match (true) {
                is_int($value) => PDO::PARAM_INT,
                is_null($value) => PDO::PARAM_NULL,
                is_bool($value) => PDO::PARAM_BOOL,
                default => PDO::PARAM_STR,
            };

            $stmt->bindValue(is_int($key) ? $key + 1 : $key, $value, $type);
        }
    }
}
