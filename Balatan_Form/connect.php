<?php
class Database
{
    static $dbName = 'kd_database';
    static $dbHost = 'localhost';
    static $dbUsername = 'root';
    static $dbPassword = '';

    private static $cont = null;

    public static function Connection()
    {
        if (self::$cont === null) {
            try {
                self::$cont = new PDO(
                    "mysql:host=" . self::$dbHost . ";dbname=" . self::$dbName . ";charset=utf8",
                    self::$dbUsername,
                    self::$dbPassword,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                die(json_encode([
                    "status" => "error",
                    "message" => $e->getMessage()
                ]));
            }
        }
        return self::$cont;
    }

    public static function GetAllData($pdo, $sql, $params = [])
    {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function ManageRecord($pdo, $sql, $params = [])
    {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }
}
