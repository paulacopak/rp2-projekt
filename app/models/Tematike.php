<?php
// Uključite Database klasu, prilagodite putanju ako je potrebno
require_once __DIR__ . '/../Core/database.php';

class Tematike {
    private $db;

    public function __construct() {
        // Dobivanje instance baze podataka
        $this->db = Database::getInstance();
    }

    /**
     * Dohvaća sve tematike iz baze podataka.
     * @return array Niz svih tematika.
     */
    public function getAllTematike() {
        $stmt = $this->db->query("SELECT * FROM tematike");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Dohvaća tematiku po njezinom nazivu.
     * @param string $name Naziv tematike.
     * @return array|false Tematika kao asocijativni niz ili false ako nije pronađena.
     */
    public function getTematikaByName($name) {
        $stmt = $this->db->prepare("SELECT * FROM tematike WHERE name = ?");
        $stmt->execute([$name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Dodaje novu tematiku u bazu podataka.
     * @param string $name Naziv tematike koju treba dodati.
     * @return bool True ako je dodano uspješno, false inače.
     */
    public function addTematika($name) {
        $stmt = $this->db->prepare("INSERT INTO tematike (name) VALUES (?)");
        return $stmt->execute([$name]);
    }

    public static function deleteTematika($tematika_id) {
    $db = Database::getInstance();
    $stmt = $db->prepare("DELETE FROM tematike WHERE id = ?");
    return $stmt->execute([$tematika_id]);
    }

    public function obrisiTematiku($id) {
    $db = Database::getInstance();

    // Brisanje povezanih pitanja
    $stmt = $db->prepare("DELETE FROM pitanja WHERE tema_id = ?");
    $stmt->execute([$id]);

    // Brisanje same tematike
    $stmt = $db->prepare("DELETE FROM tematike WHERE id = ?");
    $stmt->execute([$id]);
    }




    // Možete dodati i metode za ažuriranje i brisanje tematika po potrebi
    // public function updateTematika($id, $newName) { /* ... */ }
    // public function deleteTematika($id) { /* ... */ }
}