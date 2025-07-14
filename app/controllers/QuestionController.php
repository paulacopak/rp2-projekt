<?php

require_once __DIR__ . '/../models/Tematike.php';
require_once __DIR__ . '/../models/Pitanja.php';

class QuestionController {

    public function showAddForm() {
        // Dohvati sve tematike da ih možeš prikazati u <select>
        $tematikaModel = new Tematike();
        $tematike = $tematikaModel->getAllTematike(); 

        require_once __DIR__ . '/../views/question_managment.php';
    }

    public function addQuestion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $topic_id = $_POST['tematika_id'];
            $tekst_pitanja = $_POST['tekst_pitanja'];
            $tip = $_POST['tip'];

            $pitanjaModel = new Pitanja();

            if ($tip === 'text_input') {
                $odgovor = $_POST['odgovor'] ?? null;
                $pitanjaModel->addQuestion($topic_id, $tekst_pitanja, $tip, $odgovor, null);
            } elseif ($tip === 'multiple_choice') {
                $opcije = $_POST['opcije'] ?? [];
                $tocan_odgovor_index = isset($_POST['tocan_odgovor']) ? intval($_POST['tocan_odgovor']) - 1 : null;

                if ($tocan_odgovor_index < 0 || $tocan_odgovor_index >= count($opcije)) {
                    die("Nevažeći indeks točnog odgovora.");
                }

                // Točan odgovor je string vrijednost iz opcija
                $tocan_odgovor = $opcije[$tocan_odgovor_index];

                $pitanjaModel->addQuestion($topic_id, $tekst_pitanja, $tip, $tocan_odgovor, $opcije);
            }

            // Redirect na prikaz tematika ili neki success screen
            header('Location: /questions/add?success=1');
            exit();
        }
    }
}
