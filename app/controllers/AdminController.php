<?php

require_once __DIR__ . '/../models/Tematike.php';

class AdminController {

    public function obrisiTematiku() {
        session_start();
        
        // Provjera je li korisnik admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header("Location: index.php?action=login");
            exit;
        }

        if (isset($_POST['id'])) {
            $tematika_id = $_POST['id'];
            Tematike::deleteTematika($tematika_id);
        }

        // Preusmjeri nazad na stranicu s tematikama
        header("Location: index.php?action=home");
        exit;
    }
}
