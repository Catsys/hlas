<?php
declare(strict_types=1);

namespace App\Service;

use PDO;
use PDOException;

class DB
{
    private static ?PDO $pdo = null;

    // Приватный конструктор, чтобы предотвратить создание экземпляра извне
    private function __construct()
    {
    }

    // Приватный метод для предотвращения клонирования объекта
    private function __clone()
    {
    }

    // Метод для получения подключения (или создания нового, если оно еще не существует)
    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            self::$pdo = self::createConnection();
        }

        return self::$pdo;
    }

    // Метод для создания нового подключения
    private static function createConnection(): PDO
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $dbname = getenv('DB_NAME');
        $username = getenv('DB_USER');
        $password = getenv('DB_PASS');

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

        try {
            // Создаём соединение
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}
