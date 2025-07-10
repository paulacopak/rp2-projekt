<?php
require_once __DIR__ . '/../Core/database.php';

class Komentar {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Dodaje novi komentar u bazu podataka.
     * @param int $userId ID korisnika koji je ostavio komentar.
     * @param string $commentText Sadržaj komentara.
     * @return bool True ako je uspješno dodano, false inače.
     */
    public function addComment($userId, $commentText) {
        $sql = "INSERT INTO komentari (user_id, comment_text) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $commentText]);
    }

    /**
     * Dohvaća sve komentare iz baze podataka, s korisničkim imenima.
     * @return array Niz komentara, svaki s detaljima (id, user_id, username, comment_text, created_at).
     */
    public function getAllComments() {
        $sql = "SELECT k.id, k.user_id, u.username, k.comment_text, k.created_at
                FROM komentari k
                JOIN users u ON k.user_id = u.id
                ORDER BY k.created_at DESC"; // Najnoviji komentari prvi
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Briše komentar.
     * @param int $commentId ID komentara za brisanje.
     * @return bool True ako je uspješno obrisano, false inače.
     */
    public function deleteComment($commentId) {
        // Možete dodati provjeru ovlasti (npr. samo admin ili autor može obrisati)
        $sql = "DELETE FROM komentari WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$commentId]);
    }
    public function isCommentAuthor($userId, $commentId) {
        $sql = "SELECT COUNT(*) FROM komentari WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$commentId, $userId]);
        return $stmt->fetchColumn() > 0;
    }
}