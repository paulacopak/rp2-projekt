<?php
// app/models/Rezultati.php
require_once __DIR__ . '/../Core/database.php';

class Rezultati {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function spremiRezultat($user_id, $score, $category, $duration_seconds) {
        $stmt = $this->db->prepare("INSERT INTO results (user_id, score, category, duration, created_at) VALUES (?, ?, ?, ?, ?)");
        $duration = gmdate("H:i:s", $duration_seconds);
        $created_at = date("Y-m-d H:i:s");
        return $stmt->execute([$user_id, $score, $category, $duration, $created_at]);

    }
}
?>
