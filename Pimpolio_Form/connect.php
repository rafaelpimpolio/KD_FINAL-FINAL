<?php
class Database
{
    // --- Database Credentials ---
    private static $dbName = 'kd_sportswear';
    private static $dbHost = 'localhost';
    private static $dbUsername = 'root';
    private static $dbPassword = '';

    // --- PDO Connection Holder ---
    private static $cont = null;

    // -----------------------------
    //  CONNECT TO DATABASE (SINGLETON)
    // -----------------------------
    public static function Connection()
    {
        if (self::$cont === null) {
            try {
                self::$cont = new PDO(
                    "mysql:host=" . self::$dbHost . ";dbname=" . self::$dbName,
                    self::$dbUsername,
                    self::$dbPassword
                );
                self::$cont->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                self::WriteLog("DB Connection Error: " . $e->getMessage());
                die("Database connection failed.");
            }
        }
        return self::$cont;
    }

    // -----------------------------
    //  EXECUTE INSERT / UPDATE / DELETE
    // -----------------------------
    public static function ManageRecord($pdo, $sql, $params = [])
    {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    // -----------------------------
    //  LOG POST DATA
    // -----------------------------
    public static function WritePost($post)
    {
        self::WriteLog("POST DATA: " . json_encode($post));
    }

    // -----------------------------
    //  WRITE LOG TO log.txt
    // -----------------------------
    public static function WriteLog($msg)
    {
        $path = "log.txt";
        $file = fopen($path, "a");
        fwrite($file, date("Y-m-d g:i a") . " - " . $msg . "\n");
        fclose($file);
    }
}
?>
