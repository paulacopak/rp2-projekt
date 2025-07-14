<?php
require_once __DIR__ . '/../Models/pitanja.php'; // prilagodi putanju prema svojoj strukturi

header('Content-Type: application/json');

if (!isset($_GET['tematika_id'])) {
    echo json_encode(['error' => 'Nedostaje parametar tematika_id']);
    exit;
}

$tematikaId = intval($_GET['tematika_id']); // npr. 1

$pitanjaObj = new Pitanja();
$pitanja = $pitanjaObj->getQuestionsByTopicId($tematikaId, 5);

// Formatiraj podatke za frontend
$formatted = array_map(function ($pitanje) {
    return [
        'id' => $pitanje['id'],
        'question' => $pitanje['tekst_pitanja'],
        'type' => $pitanje['tip'],
        'answers' => array_filter([$pitanje['odgovor1'], $pitanje['odgovor2'], $pitanje['odgovor3']]),
        'correctAnswer' => $pitanje['tocan_odgovor']
    ];
}, $pitanja);

echo json_encode($formatted);
