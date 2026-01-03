<?php
class Database
{
<<<<<<< HEAD
    // --- Database Credentials ---
=======
>>>>>>> d8b50dd3bf4c2b3cca1e1d17df4fcfec34ebc008
    private static $dbName = 'kd_sportswear';
    private static $dbHost = 'localhost';
    private static $dbUsername = 'root';
    private static $dbPassword = '';

    private static $cont = null;

<<<<<<< HEAD
    // -----------------------------
    //  CONNECT TO DATABASE (SINGLETON)
    // -----------------------------
=======
>>>>>>> d8b50dd3bf4c2b3cca1e1d17df4fcfec34ebc008
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
<<<<<<< HEAD

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
=======
>>>>>>> d8b50dd3bf4c2b3cca1e1d17df4fcfec34ebc008
}
?>
