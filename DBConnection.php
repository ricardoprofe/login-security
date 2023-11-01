<?php

class DBConnection
{
//Database connection data
    private static string $servername = "localhost";
    private static string $dbname = "logins";
    private static string $username = "logins";
    private static string $password = "logins";

    public static function connectDB(): ?PDO
    {
        $servername = self::$servername;
        $dbname = self::$dbname;
        $username = self::$username;
        $password = self::$password;

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password,
                array(PDO::ATTR_PERSISTENT => true));
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
}
