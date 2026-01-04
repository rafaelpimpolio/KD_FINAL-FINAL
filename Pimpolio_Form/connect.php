<?php
class Database {
    private static $dbName = "kd_sportswear";
    private static $dbHost = "localhost";
    private static $dbUser = "root";
    private static $dbPass = "";
    private static $conn = null;

    public static function Connection() {
        if (self::$conn === null) {
            self::$conn = new PDO(
                "mysql:host=".self::$dbHost.";dbname=".self::$dbName,
                self::$dbUser,
                self::$dbPass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
        return self::$conn;
    }
}
