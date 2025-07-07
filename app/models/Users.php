<?php
require_once __DIR__ . '/../Core/database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function register($username, $password,$role = 'user') {
        //provjera postoji li korisnik
        $stmt =$this->db->prepare("SELECT username FROM users WHERE username= ?");
        $stmt->execute([$username]);
        if($stmt->fetch()) return false; //korisnik postoji
        
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO users (username, password,role) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $hash,$role]);
    }

    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    public function getTopics(){
        $stmt = $this->db->query("SELECT * FROM tematike");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getUserStats($username){
        $stmt = $this->db->prepare("SELECT * FROM statistics WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
