<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Pristup zabranjen.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Nedostaje ID tematike.']);
        exit;
    }

    $topicId = (int)$data['id'];

    require_once __DIR__ . '/../models/Users.php';
    $userModel = new User();

    $deleted = $userModel->deleteTopic($topicId);

    if ($deleted) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Brisanje nije uspjelo.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Nedozvoljena metoda.']);
}
