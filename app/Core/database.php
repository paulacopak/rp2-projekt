<?php

class Database {
    private static $instance = null;

    private function __construct() {}

    public static function getInstance() {
        if (self::$instance === null) {
            $host = 'rp2.studenti.math.hr';       // zamijeni sa stvarnim hostom baze
            $db   = 'copak';             // ime tvoje baze
            $user = 'student';           // tvoje korisničko ime
            $pass = 'pass.mysql';        // tvoja lozinka

            try {
                self::$instance = new PDO(
                    "mysql:host=$host;dbname=$db;charset=utf8",
                    $user,
                    $pass
                );
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Greška: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
