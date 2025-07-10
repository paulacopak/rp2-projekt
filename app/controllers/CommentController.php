<?php
require_once __DIR__ . '/../models/Komentar.php';

class CommentController {
    private $komentarModel;

    public function __construct() {
        $this->komentarModel = new Komentar();
    }

    /**
     * Prikazuje stranicu s komentarima.
     * Dohvaća sve komentare i prosljeđuje ih viewu.
     */
    public function showComments() {
        // Provjera prijave je ključna za pristup komentarima (ako samo prijavljeni mogu vidjeti/dodavati)
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login'); // Preusmjeri na login ako nije prijavljen
            exit;
        }

        $comments = $this->komentarModel->getAllComments();
        include __DIR__ . '/../views/comments/comments.php'; // Prikazujemo komentar view
    }

    /**
     * Obrađuje slanje novog komentara.
     */
    public function addComment() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            $commentText = $_POST['comment_text'] ?? '';

            if (!empty($commentText)) {
                $success = $this->komentarModel->addComment($userId, $commentText);
                if ($success) {
                    $_SESSION['comment_message'] = 'Komentar uspješno dodan.';
                } else {
                    $_SESSION['comment_error'] = 'Greška pri dodavanju komentara.';
                }
            } else {
                $_SESSION['comment_error'] = 'Komentar ne može biti prazan.';
            }
        }
        // Uvijek preusmjeri natrag na stranicu s komentarima nakon obrade POST-a
        header('Location: index.php?action=comment');
        exit;
    }

    /**
     * Obrađuje brisanje komentara (ako želimo omogućiti, npr. za admine ili autora).
     */
    public function deleteComment() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json'); // Ovo je ključno za AJAX odgovor

        // Provjeri je li korisnik admin ili autor komentara
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            // Dodao sam provjeru je li korisnik autor komentara
            $commentId = $_POST['comment_id'] ?? null;
            if ($commentId && isset($_SESSION['user']) && $this->komentarModel->isCommentAuthor($_SESSION['user']['id'], $commentId)) {
                // Ako je autor, dozvoli brisanje
            } else {
                echo json_encode(['success' => false, 'message' => 'Niste ovlašteni za brisanje ovog komentara.']);
                exit;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $commentId = $_POST['comment_id'] ?? null;
            if ($commentId) {
                if ($this->komentarModel->deleteComment($commentId)) {
                    echo json_encode(['success' => true, 'message' => 'Komentar obrisan.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Greška pri brisanju komentara iz baze.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Nedostaje ID komentara.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
        exit; // Uvijek izađite nakon slanja JSON odgovora
    }
}