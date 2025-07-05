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
    default:
        echo "404 - Stranica ne postoji.";
}
?>
</div>
</body>
</html>
