<?php

require_once __DIR__ . '/../Core/database.php';

class Pitanja {
    private $db;

    public function __construct() {
       
        $this->db = Database::getInstance();
    }


    public function getQuestionsByTopicId($topic_id, $limit = 5) {
      
        $stmt = $this->db->prepare("SELECT * FROM pitanja WHERE id_tematike = ? ORDER BY RAND() LIMIT ?");
        $stmt->bindValue(1, $topic_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuestionById($question_id) {
        $stmt = $this->db->prepare("SELECT * FROM questions WHERE id = ?");
        $stmt->execute([$question_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function addQuestion($topic_id, $tekst_pitanja, $odgovor = null, $tip = 'text_input', $odgovor1 = null, $odgovor2 = null, $odgovor3 = null, $tocan_odgovor = null) {
        
        $sql = "INSERT INTO questions (id_tematike, tekst_pitanja, odgovor, tip) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$topic_id, $tekst_pitanja, $odgovor, $tip]);

       
    }

    // Opcionalno, dodajte metodu za provjeru odgovora, iako se to može raditi i u kontroleru
    // public function checkAnswer($questionId, $userAnswer) { /* ... */ }
}