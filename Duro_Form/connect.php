<?php
class Database
{
    // --- Database Credentials ---
    private static $dbName = 'kd_database';  
    private static $dbHost = 'localhost';
    private static $dbUsername = 'root';
    private static $dbPassword = '';

    // --- PDO Connection Holder ---
    private static $cont = null;

    //  CONNECT TO DATABASE (SINGLETON)
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
                die("Database Connection Error: " . $e->getMessage());
            }
        }
        return self::$cont;
    }

    //  GET A SINGLE PAYMENT RECORD
    public static function GetOnePayment($pdo, $sql)
    {
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Error fetching payment record: " . $e->getMessage();
            return false;
        }
    }

    //  GET ALL PAYMENT RECORDS
    public static function GetAllPayments($pdo, $sql)
    {
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo "Error fetching payment data: " . $e->getMessage();
            return [];
        }
    }

    //  INSERT, UPDATE, DELETE PAYMENT
    public static function ManagePayment($pdo, $sql, $params = [])
    {
        try {
            $stmt = $pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (Exception $e) {
            self::WriteLog("Payment DB Error: " . $e->getMessage());
            throw $e;  // re-throw so calling function can catch
        }
    }

    //  LOG ALL POST DATA
    public static function WritePost($post)
    {
        foreach ($post as $key => $value) {
            if (is_array($value)) {
                self::WriteLog($key . ' = ' . json_encode($value));
            } else {
                self::WriteLog($key . ' = ' . $value);
            }
        }
    }

    //  WRITE LOG TO log.txt
    public static function WriteLog($msg)
    {
        $path = "log.txt";
        $file = fopen($path, "a");
        fwrite($file, date("Y-m-d g:i a") . " - " . $msg . "\n");
        fclose($file);
    }
}
?>
