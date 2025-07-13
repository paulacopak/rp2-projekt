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
        $topic = $tematika;

        if ($_SESSION['user']['role'] === 'admin') {
            $questions = $this->pitanjaModel->getAllQuestionsByTopicId($tematika['id']);
            include __DIR__ . '/../views/topic_managment.php'; // ADMIN VIEW
            exit;
        } else {
            include __DIR__ . '/../views/topic_preview.php'; // USER VIEW
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
    if (!isset($_SESSION['quiz'])) {
        header('Location: index.php?action=home');
        exit;
    }

    $quizData = $_SESSION['quiz'];
    $questions = $quizData['questions'];
    $total = count($questions);

    // Novi kod:
    if (isset($_GET['index'])) {
        $_SESSION['quiz']['current_question_index'] = (int)$_GET['index'];
    }

    $currentIndex = $_SESSION['quiz']['current_question_index'];

    if ($currentIndex < 0 || $currentIndex >= $total) {
        echo "Greška: nevažeći indeks pitanja.";
        return;
    }

    $currentQuestion = $questions[$currentIndex];
    include __DIR__ . '/../views/process_answer.php';
}


    public function finishQuiz() {
        session_start();

        if (!isset($_SESSION['quiz'])) {
            header('Location: index.php?action=home');
            exit;
        }

        $quiz = $_SESSION['quiz'];
        $questions = $quiz['questions'];
        $answers = $quiz['answers'] ?? [];

        $tocno = 0;
        foreach ($questions as $i => $q) {
            if (isset($answers[$i]) && trim($answers[$i]) === trim($q['odgovor'])) {
                $tocno++;
            }
        }

        $ukupno = count($questions);
        $trajanje = time() - $quiz['start_time'];

        $_SESSION['quiz']['rezultat'] = [
            'tocno' => $tocno,
            'ukupno' => $ukupno,
            'trajanje' => $trajanje,
        ];

        // Spremi rezultat u tablicu 'results'
        require_once __DIR__ . '/../models/Rezultati.php';
        $rezRepo = new Rezultati();
        $rezRepo->spremiRezultat(
            $_SESSION['user']['id'],           // user_id
            $tocno,                             // score
            $_SESSION['quiz']['topic_name'],   // category
            $trajanje                          // duration in seconds
        );

        // (OPCIJA) Spremi statistiku i na leaderboard ako već imaš funkcije
        $username = $_SESSION['user']['username'] ?? null;
        $user_id = $_SESSION['user']['id'] ?? null;

        if ($user_id && $username) {
            $this->userModel->saveQuizStats(
                $username,
                $quiz['topic_name'],
                $tocno,
                $ukupno,
                date('Y-m-d H:i:s', $quiz['start_time']),
                date('Y-m-d H:i:s')
            );

            $this->leaderboardModel->addScore($user_id, $tocno);
        }

        include __DIR__ . '/../views/rezultat.php';
    }
    public function odustani(){
        if(session_status()==PHP_SESSION_NONE){
            session_start();
        }
        unset($_SESSION['quiz']);
        header('Location: index.php?action=home');
        exit;
    }

}

