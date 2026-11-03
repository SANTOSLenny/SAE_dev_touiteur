<?php

namespace iutnc\touiteur\db;

use PDO;
use Exception;

class ConnectionFactory {
    private static ?PDO $db = null;
    private static array $config = [];

    public static function setConfig(string $file): void {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new Exception("Impossible de lire : $file");
        }
        self::$config = $conf;
    }

    public static function makeConnection(): PDO {
        if (self::$db === null) {
            $host = self::$config['host'] ?? 'localhost';
            $dbname = self::$config['database'] ?? 'touiteur';
            $username = self::$config['username'] ?? 'root';
            $password = self::$config['password'] ?? '';

            self::$db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8mb4", $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        }
        return self::$db;
    }
}