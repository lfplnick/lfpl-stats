<?php

require_once __DIR__ . '/../LocalSettings.php';

class Connection {
    private static $conn;

    public static function getConnection()
    {
        if( isset( self::$conn ) )
        {
            return self::$conn;
        } else {
            $connectionString =
                "mysql:host=" . LocalSettings::$dbHost . ";" .
                "port=" . LocalSettings::$dbPort . ";" .
                "dbname=" . LocalSettings::$dbName;

            try {
                self::$conn = new PDO(
                    $connectionString,
                    LocalSettings::$dbUser,
                    LocalSettings::$dbPw
                );

                self::$conn->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );

            } catch ( PDOException $e ) {
                // @todo Handle this better.
                return false;
            }
            
            return self::$conn;
        }
    }

    public static function closeConnection()
    {
        self::$conn = null;
        return true;
    }
}