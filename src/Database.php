<?php
// src/Database.php
declare(strict_types=1);

namespace App;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private PDO $pdo;
    private string $dbPath;

    public function __construct()
    {
        $this->dbPath = dirname(__DIR__) . "/{$_ENV['DATA_PATH']}/{$_ENV['DB_FILE']}";
        $this->connect();
    }

    /**
     * Connect to SQLite database
     */
    private function connect(): void
    {
        try {
            $this->pdo = new PDO($_ENV['DB_TYPE'] . ':' . $this->dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }

    public function migrate(): void
    {
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT,
            price TEXT,
            available TEXT ,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        SQL;

        $this->pdo->exec($sql);
    }

    public function insertProduct(?string $title, ?string $price, ?string $available)
    {
        $statement = $this->pdo->prepare('INSERT INTO products (title, price, available) VALUES (:title, :price, :available)');
        $statement->execute([
            ':title' => $title,
            ':price' => $price,
            ':available' => $available
        ]);
    }
}
