
<?php
session_start();


error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once __DIR__. '/app/controllers/autocontrollers.php';
require_once __DIR__.'/app/controllers/QuizController.php';
require_once __DIR__ . '/app/controllers/LeaderboardController.php';
require_once __DIR__.'/app/controllers/CommentController.php';

require_once __DIR__. '/app/controllers/QuestionController.php';

$controller = new QuestionController();
/*
if ($_GET['url'] === 'questions/add' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->showAddForm();
} elseif ($_GET['url'] === 'questions/add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->addQuestion();
}
*/

$action = $_GET['action'] ?? 'login';
$auth = new AuthController();

$quizController = new QuizController();
$commentController = new CommentController();

if ($action === 'delete_comment') {
    // Obrada brisanja komentara AJAX-om
    $commentController->deleteComment();
    // exit pozvan unutar metode
}

if ($action === 'ajax_add_topic') {
    // AJAX dodavanje teme
    header('Content-Type: application/json');
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }
    $name = $_POST['name'] ?? '';
    if (!$name) {
        echo json_encode(['success' => false, 'error' => 'Missing name']);
        exit;
    }
    $success = $auth->addTopic($name);
    echo json_encode(['success' => $success]);
    exit;
}
if($action ==='admin_questions'){
    (new AdminController())->showAllQuestions();
}
if ($action === 'odustani') {
    $quizController->odustani();
    exit;
}
if ($action === 'obrisi_pitanje') {
    require_once 'app/controllers/AdminController.php';
    $controller = new AdminController();
    $controller->obrisiPitanje();
}


?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kviz</title>
</head>
<body>
    <style>
        body {
            background-color: #fff9c4; /* blago žuta */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* visina prozora */
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            text-align: center;
            background: white;
            padding: 30px 50px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h1 {
            margin-bottom: 20px;
            color: #fbc02d; /* tamno žuta */
        }
        a {
            color: #fbc02d;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Kviz</h1>
<?php

switch ($action) {
    case 'login':
        $auth->login();
        break;
    case 'register':
        $auth->register();
        break;
    case 'logout':
        $auth->logout();
        break;
    case 'dashboard':
        
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        echo "Dobrodošao, {$_SESSION['user']['username']}! Tvoja uloga je: {$_SESSION['user']['role']}";
        echo "<p><a href='index.php?action=logout'>Odjavi se</a></p>";
        echo "<p><a href='index.php?action=home'>Početak</a></p>";
        echo "<p><a href ='index.php?action=profil'>Moj profil</a></p>";
        break;
    case 'home':
       
        if(!isset($_SESSION['user'])){
            header('Location: index.php?action=login');
            exit;
        }
        $topics = $auth->getTopics();
        include __DIR__.'/app/views/home.php';
        break;
    case 'sva_pitanja':

        if ($_SESSION['user']['role'] === 'admin') {
            $controller = new AdminController();
            $controller->prikaziSvaPitanja();
        }
        break;
    case 'spremi_odgovor':
        session_start();
        if (!isset($_SESSION['quiz'])) exit;

        $index = isset($_POST['index']) ? (int)$_POST['index'] : -1;
        $odgovor = isset($_POST['odgovor']) ? trim($_POST['odgovor']) : '';

        if ($index >= 0) {
            $_SESSION['quiz']['answers'][$index] = $odgovor;
        }
        exit;  // Važno da ne šalješ dodatni output
    case 'review_answers':
        include 'app/views/review_answers.php';
        break;
    case 'go_to_question':
        if (!isset($_SESSION['quiz'])) {
            header('Location: index.php?action=home');
            exit;
        }
        // Prebaci index iz GET i pozovi processAnswer
        $_SESSION['quiz']['current_question_index'] = (int) ($_GET['index'] ?? 0);
        $quizController->processAnswer();
        break;

    case 'predaj_kviz':
        $quiz = $_SESSION['quiz'];
        $answers = $_SESSION['quiz']['answers'] ?? [];
        $tocno = 0;

        foreach ($quiz['questions'] as $i => $q) {
            if (strcasecmp(trim($answers[$i] ?? ''), trim($q['odgovor'])) === 0) {
                $tocno++;
            }
        }

        $kraj = time();
        $trajanje = $kraj - $quiz['start_time'];

        $_SESSION['quiz']['rezultat'] = [
            'tocno' => $tocno,
            'ukupno' => count($quiz['questions']),
            'trajanje' => $trajanje
        ];

        header('Location: index.php?action=rezultat');
        exit;
    case 'spremi_odgovor':
        $i = $_POST['index'] ?? null;
        $odgovor = $_POST['odgovor'] ?? '';

        if ($i !== null && isset($_SESSION['quiz']['questions'][$i])) {
            $_SESSION['quiz']['answers'][$i] = trim($odgovor);
        }
        exit;
    case 'ranking':
        
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        require_once __DIR__ . '/app/controllers/LeaderboardController.php';
        $controller = new LeaderboardController();
        $controller->show(); //prikazuje rang listu
        break;
    case 'profile':
        
        if(!isset($_SESSION['user'])){
            header('Location: index.php?action=login');
            exit;
        }
        $username = $_SESSION['user']['username'];
        $stats = $auth->getUserStats($username);
        include __DIR__.'/app/views/user/profil.php';
        break;
    case 'comment':
        if(!isset($_SESSION['user'])){
            header('Location: index.php?action=login');
            exit;
        }
        $username = $_SESSION['user']['username'];
        $commentController->showComments();
        break;
    case 'add_comment_process':
        
        $commentController->addComment();
        break;
    case 'delete_comment':
        $commentController->deleteComment();
        break;
    

    case 'admin_add_topic':
        
        if($_SESSION['user']['role']!=='admin'){
            header('Location: index.php?action=login');
            exit;
        }
        if($_SERVER['REQUEST_METHOD']==='POST'){
            $topicName=$_POST['name'];
            $auth->addTopic($topicName);
            header('Location: index.php?action=admin_add_topic');
            exit;
        }
        include 'app/views/admin/dodajTematiku.php';
        break;

    case 'admin_add_question':
        if($_SESSION['user']['role']!=='admin'){
            header('Location: index.php?action=login');
            exit;
        }
        if($_SERVER['REQUEST_METHOD']==='POST'){
            $id_tematike =$_POST['id_tematike'];
            $text=$_POST['question'];
            $auth->dodajPitanje($id_tematike,$text);
            header("Location: index.php?action=admin_add_question");
            exit;

        }
        $topics = $auth->getTopics();
        include 'app/views/admin/add_question.php';
        break;
     case 'dodaj_pitanje':
        $quizController->dodajPitanje();
        break;
    case 'ajax_add_topic':
        session_start();
        header('Content-Type: application/json');
        if(!isset($_SESSION['user']) || $_SESSION['user']['role']!=='admin'){
            echo json_encode(['success'=>false,'error'=>'Unauthorized']);
            exit;
        }
        $name = $_POST['name'] ?? '';
        if(!$name){
            echo json_encode(['success' => false, 'error' => 'Missing name']);
            exit;
        }
        $success = $auth->addTopic($name);
        echo json_encode(['success' => $success]);
        break;
    case 'rezultat':
        include 'app/views/rezultat.php';
        break;
    
    case 'obrisiTematiku':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once 'app/controllers/autocontrollers.php';
            $controller = new AuthController();
            $controller->obrisiTematiku();
        }
        break;
    
    case 'start_quiz':
        if(!isset($_SESSION['user'])){
            header('Location: index.php?action=login');
            exit;
        }
        $quizController->startQuiz();
        break;
    case 'begin_quiz':
        if(!isset($_SESSION['user'])){
            header('Loction: index.php?action=login');
            exit;
        }
         $topicName = $_GET['topic'] ?? null;
        if (!$topicName) {
            header('Location: index.php?action=home');
            exit;
        }

        $tematika = (new Tematike())->getTematikaByName($topicName);
        if (!$tematika) {
            echo "Nepostojeća tematika.";
            exit;
        }

        $questions = (new Pitanja())->getQuestionsByTopicId($tematika['id'], 5);
        if (empty($questions)) {
            echo "Nema pitanja za ovu tematiku.";
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
    case 'process_answer':
        $quizController->processAnswer();
        break;

    case 'navigate_quiz':
        if (!isset($_SESSION['quiz'])) {
            header('Location: index.php?action=home');
            exit;
        }

        $dir = $_GET['dir'] ?? '';
        if ($dir === 'prev') $_SESSION['quiz']['current_question_index'] = max(0, $_SESSION['quiz']['current_question_index'] - 1);
        elseif ($dir === 'next') $_SESSION['quiz']['current_question_index'] = min($_SESSION['quiz']['total_questions'] - 1, $_SESSION['quiz']['current_question_index'] + 1);

        header('Location: index.php?action=process_answer');
        exit;
    
    case 'finish_quiz':
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $quizController->finishQuiz();
        break;
    
    default:
        echo "404 - Stranica ne postoji.";
}
?>
</div>
</body>
</html>


