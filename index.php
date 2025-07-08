<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__. '/app/controllers/autocontrollers.php';

$action = $_GET['action'] ?? 'login';
$auth = new AuthController();
?>

<!DOCTYPE html>
<html lang="en">
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
        session_start();
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
        session_start();
        if(!isset($_SESSION['user'])){
            header('Location: index.php?action=login');
            exit;
        }
        $topics = $auth->getTopics();
        include __DIR__.'/app/views/home.php';
        break;
    case 'profile':
        session_start();
        if(!isset($_SESSION['user'])){
            header('Location: index.php?action=login');
            exit;
        }
        $username = $_SESSION['user']['username'];
        $stats = $auth->getUserStats($username);
        include __DIR__.'/app/views/user/profil.php';
        break;
    case 'admin_add_topic':
        session_start();
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
        exit;
    default:
        echo "404 - Stranica ne postoji.";
}
?>
</div>
</body>
</html>
