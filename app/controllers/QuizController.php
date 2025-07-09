<?php
// Osigurajte da su modeli uključeni. Podesite putanje ako su drugačije.
require_once __DIR__ . '/../models/Pitanja.php';
require_once __DIR__ . '/../models/Tematike.php';
require_once __DIR__ . '/../models/Users.php'; // Za spremanje statistike kviza
require_once __DIR__ . '/../models/LeaderboardModel.php'; // Za spremanje rezultata na ljestvicu
require_once __DIR__ . '/../Core/database.php'; // POTREBNO: Uključite Database klasu

class QuizController {
    private $pitanjaModel;
    private $tematikeModel;
    private $userModel;
    private $leaderboardModel;
    private $db; // Dodajte property za PDO objekt baze podataka

    public function __construct() {
        // Dobivanje instance baze podataka
        $this->db = Database::getInstance(); // Dohvatite PDO objekt ovdje

        // Instanciranje modela koje će kontroler koristiti
        $this->pitanjaModel = new Pitanja();
        $this->tematikeModel = new Tematike();
        $this->userModel = new User(); // Klasa User ispravno dohvaća DB instancu unutar svog konstruktora
        $this->leaderboardModel = new LeaderboardModel($this->db); // ISPRAVKA: Proslijedite $this->db objekt LeaderboardModelu
    }

    public function startQuiz() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $topicName = $_GET['topic'] ?? null;

        if (!$topicName) {
            header('Location: index.php?action=home');
            exit;
        }

        $tematika = $this->tematikeModel->getTematikaByName($topicName);

        if (!$tematika) {
            echo "Nema pitanja za ovu tematiku. Molimo pokušajte s drugom.";
            echo "<p><a href='index.php?action=home'>Natrag na početnu</a></p>";
            exit;
        }

        // AKO JE KORISNIK ADMIN
        if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
            $topic = $tematika; // Podaci o tematici
            $questions = $this->pitanjaModel->getAllQuestionsByTopicId($tematika['id']); // Dohvati SVA pitanja

            include __DIR__ . '/../views/topic_managment.php'; // Prikazujemo admin view
            exit;
        }

        // NORMALNA LOGIKA ZA POKRETANJE KVIZA (za korisnike)
        $questions = $this->pitanjaModel->getQuestionsByTopicId($tematika['id'], 5); // Dohvaća 5 pitanja za kviz

        if (empty($questions)) {
            echo "Nema pitanja za ovu tematiku. Molimo pokušajte s drugom.";
            echo "<p><a href='index.php?action=home'>Natrag na početnu</a></p>";
            exit;
        }

        $_SESSION['quiz'] = [
            'topic_id' => $tematika['id'],
            'topic_name' => $tematika['name'],
            'questions' => $questions,
            'current_question_index' => 0,
            'score' => 0,
            'total_questions' => count($questions),
            'start_time' => time(),
        ];

        header('Location: index.php?action=process_answer');
        exit;
    }

    public function processAnswer() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['quiz']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=home');
            exit;
        }

        $quiz = &$_SESSION['quiz'];
        $currentQuestion = $quiz['questions'][$quiz['current_question_index']];
        $userAnswer = $_POST['answer'] ?? null;

        if ($userAnswer == $currentQuestion['odgovor']) {
            $quiz['score']++;
        }

        $quiz['current_question_index']++;

        if ($quiz['current_question_index'] < $quiz['total_questions']) {
            $question = $quiz['questions'][$quiz['current_question_index']];
            include __DIR__ . '/../views/quiz/quiz_question.php';
        } else {
            $this->finishQuiz();
        }
    }

    public function finishQuiz() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['quiz'])) {
            header('Location: index.php?action=home');
            exit;
        }

        $quiz = $_SESSION['quiz'];
        $endTime = time();
        $duration = $endTime - $quiz['start_time'];

        $username = $_SESSION['user']['username'] ?? 'Gost';
        $user_id = $_SESSION['user']['id'] ?? null;

        if ($user_id) {
            $this->userModel->saveQuizStats(
                $username,
                $quiz['topic_name'],
                $quiz['score'],
                $quiz['total_questions'],
                date('Y-m-d H:i:s', $quiz['start_time']),
                date('Y-m-d H:i:s', $endTime)
            );

            $this->leaderboardModel->addScore($user_id, $quiz['score']);
        }

        $finalScore = $quiz['score'];
        $totalQuestions = $quiz['total_questions'];
        $topicName = $quiz['topic_name'];

        unset($_SESSION['quiz']);

        include __DIR__ . '/../views/quiz/quiz_result.php';
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> 9da5f07e2ce258c4293e6b0b4086a83eeb4ef826
