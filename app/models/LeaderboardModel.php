<?php
class LeaderboardModel {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getLeaderboard() {
        $sql = "
            SELECT u.username, SUM(r.score) AS total_score
            FROM users u
            JOIN results r ON u.id = r.user_id
            GROUP BY u.id
            ORDER BY total_score DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // koristi PDO način dohvaćanja
    }
}
?>
