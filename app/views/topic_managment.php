<?php
// Provjera da je korisnik administrator
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php?action=login');
    exit;
}

// Provjera da su podaci o tematici dostupni (proslijeđeni iz QuizController::startQuiz)
if (!isset($topic) || !isset($topic['id']) || !isset($topic['name'])) {
    header('Location: index.php?action=home');
    exit;
}
//ovdje bi trebalo doci view za admin moze upravljati pojedinom tematikom:
/*
1. gumb- Povratak na home
2. gumb - pregled svih pitanja
3. gumb Započni kviz

*/
