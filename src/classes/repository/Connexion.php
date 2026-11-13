<?php
namespace iutnc\touiteur\repository;

use PDO;
use Exception;

class Connexion {
    private static ?PDO $pdo = null;

    public static function setConfig(string $file): void {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new Exception("Impossible de lire le fichier de configuration : $file");
        }

        $charset = $conf['charset'] ?? 'utf8mb4';
        $dsn = "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']};charset={$charset}";
        self::$pdo = new PDO($dsn, $conf['username'], $conf['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public static function getConnection(): PDO {
        return self::$pdo;
    }
}