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
	error_log("DeleteComment called");
    	error_log("Session user: " . print_r($_SESSION['user'], true));
    	error_log("POST data: " . print_r($_POST, true));
      
	if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Niste prijavljeni.']);
        exit;
    	}

    	$userId = $_SESSION['user']['id'];
    	$userRole = $_SESSION['user']['role'];
    	$commentId = $_POST['comment_id'] ?? null;
	if (!$commentId) {
        echo json_encode(['success' => false, 'message' => 'Nedostaje ID komentara.']);
        exit;
    	}

    	error_log("UserRole: $userRole, UserId: $userId, CommentId: $commentId");
        
	// Dozvoli brisanje ako je korisnik admin ili autor komentara
    	if ($userRole === 'admin' || $this->komentarModel->isCommentAuthor($userId, $commentId)) {
        	if ($this->komentarModel->deleteComment($commentId)) {
            		echo json_encode(['success' => true, 'message' => 'Komentar je uspješno obrisan.']);
        	} else {
            		echo json_encode(['success' => false, 'message' => 'Greška pri brisanju komentara.']);
        	}
    	} else {
        	echo json_encode(['success' => false, 'message' => 'Nemate ovlasti za brisanje ovog komentara.']);
    	}

    exit;
}
}
