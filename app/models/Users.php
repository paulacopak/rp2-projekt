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
    public function getUserStats($username) {
    $stmt = $this->db->prepare("
        SELECT r.category AS topic, r.score AS bodovi, 
               NULL AS ukupno, r.created_at AS start_time, 
               ADDTIME(r.created_at, r.duration) AS end_time
        FROM results r
        JOIN users u ON r.user_id = u.id
        WHERE u.username = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$username]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    public function addTopic($name){
        $stmt=$this->db->prepare("INSERT INTO tematike (name) VALUES (?)");
        return $stmt->execute([$name]);
    }
    public function addQuestion($topic_id, $tekst, $odgovor, $tip) {
    $stmt = $this->db->prepare("
        INSERT INTO questions (id_tematike, tekst_pitanja, odgovor, tip)
        VALUES (?, ?, ?, ?)
    ");
    return $stmt->execute([$topic_id, $tekst, $odgovor, $tip]);
    }

    public function createStatisticsEntry($username) {
    $defaultTopic='';
    $stmt = $this->db->prepare("
        INSERT INTO statistics 
            (username, topic, bodovi, ukupno, start_time, end_time, created_at)
        VALUES 
            (?, ?, 0, 0, NULL, NULL, NOW())
    ");
    return $stmt->execute([$username,$defaultTopic]);
}

}
