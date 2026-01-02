<?php
class Database
{
    static $dbName = 'form';
    static $dbHost = 'localhost';
    static $dbUsername = 'root';
    static $dbPassword = '';

    

    private static $cont = null;

    public static function Connection()
    {
        if (null == self::$cont) {
            try {

                self::$cont = new PDO("mysql:host=" . self::$dbHost . ";" . "dbname=" . self::$dbName, self::$dbUsername, self::$dbPassword);
            } catch (PDOException $e) {
                die($e->getMessage());
            }

            return self::$cont;

            function disconnect()
            {
                // self::$cont = null;
                //  echo "Disconnected";
            }
        }
    }

    public static  function GetOneData($pdo, $sql,  $value = 0)
    {
        try {
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $q = $pdo->prepare($sql);
            $q->execute();
            $result = $q->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (Exception $e) {
            echo "Error in Adding Record";
            echo 'Message: ' . $e->getMessage();
        }
    }






    public static function GetAllData($pdo, $sql)
    {
        try {
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $q = $pdo->prepare($sql);
            $q->execute();
            $result = $q->fetchAll();
            return ($result);
        } catch (Exception $e) {
            echo "Error in Adding Record";
            echo 'Message: ' . $e->getMessage();
        }
    }

    public static function ManageRecord($pdo, $sql, $value = 0)
    {
        try {
            if ($value != 0)


                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $q = $pdo->prepare($sql);
            $q->execute();
        } catch (Exception $e) {
            echo "Error in Adding Record";
            echo 'Message: ' . $e->getMessage();
        }
    }

     public static function WritePost($post)
    {
        foreach ($post as $key => $value) {
            if (is_array($value)) {
                // Convert array to readable string
                self::WriteLog($key . ' = ' . json_encode($value));
            } else {
                self::WriteLog($key . ' = ' . $value);
            }
        }
    }

    public static function WriteLog($num=0,$log="")
    {
        $path  = "log.txt";
        $file = fopen($path, "a");
        fwrite($file, date("g:i a   "));
        fwrite($file, $num. "    " .$log . "\n");
    }


   
    
}
