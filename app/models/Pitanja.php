<?php

require_once __DIR__ . '/../Core/database.php';

class Pitanja {
    private $db;

    public function __construct() {
       
        $this->db = Database::getInstance();
    }

    public function getAllQuestionsByTopicId($topic_id){
        $stmt = $this->db->prepare("SELECT * FROM pitanja WHERE  id_tematike=?");
        $stmt->execute([$topic_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    }
    public function getQuestionsByTopicId($topic_id, $limit = 5) {
      
        $stmt = $this->db->prepare("SELECT * FROM pitanja WHERE id_tematike = ? ORDER BY RAND() LIMIT ?");
        $stmt->bindValue(1, $topic_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuestionById($question_id) {
        $stmt = $this->db->prepare("SELECT * FROM pitanja WHERE id = ?");
        $stmt->execute([$question_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function addQuestion($topic_id, $tekst_pitanja, $odgovor = null, $tip = 'text_input', $odgovor1 = null, $odgovor2 = null, $odgovor3 = null, $tocan_odgovor = null) {
        
        $sql = "INSERT INTO questions (id_tematike, tekst_pitanja, odgovor, tip) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$topic_id, $tekst_pitanja, $odgovor, $tip]);

       
    }
    public function getAllQuestions() {
    $stmt = $this->db->query("SELECT * FROM pitanja");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obrisiPitanje($id) {
    $stmt = $this->db->prepare("DELETE FROM pitanja WHERE id = ?");
    $stmt->execute([$id]);
    }

    public function dodajPitanje($data) {
        $stmt = $this->db->prepare("INSERT INTO pitanja (id_tematike, tekst_pitanja, tip, opcije, odgovor) VALUES (:id_tematike, :tekst_pitanja, :tip, :opcije, :odgovor)");
        return $stmt->execute([
            ':id_tematike' => $data['id_tematike'],
            ':tekst_pitanja' => $data['tekst_pitanja'],
            ':tip' => $data['tip'],
            ':opcije' => $data['opcije'],
            ':odgovor' => $data['odgovor']
        ]);
    }



    // Opcionalno, dodajte metodu za provjeru odgovora, iako se to može raditi i u kontroleru
    // public function checkAnswer($questionId, $userAnswer) { /* ... */ }
}
