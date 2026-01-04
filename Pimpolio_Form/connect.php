<?php
class Database {
    public static function Connection() {
        return new PDO(
            "mysql:host=localhost;dbname=kd_sportswear;charset=utf8",
            "root",
            "",
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }
}
