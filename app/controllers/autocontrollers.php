<?php
require_once __DIR__ . '/../models/Users.php';
require_once __DIR__ . '/../models/Tematike.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->userModel->login($username, $password);
            if ($user) {
                
                $_SESSION['user'] = $user;
                header('Location: index.php?action=home');
                exit;
            } else {
                $error = "Pogrešno korisničko ime ili lozinka.";
                require __DIR__ . '/../views/auth/login.php';
            }
        } else {
            require __DIR__ . '/../views/auth/login.php';
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $success=$this->userModel->register($username, $password);
            if($success){
		$this->userModel->createStatisticsEntry($username);
                header('Location: index.php?action=login');
                exit;
            }else{
                $error = "Korisničko ime već postoji.";
                require __DIR__.'/../views/auth/register.php';
            }
        } else{
            $error = null;
            require __DIR__.'/../views/auth/register.php';
        }
            
    }
    public function getTopics(){
        return $this->userModel->getTopics();
    }
    public function getStatistics($ime){
        return $this->userModel->getStatistics($ime);
    }
    
    public function logout() {
        
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }
    public function getUserStats($username) {
        return $this->userModel->getUserStats($username);
    }
    public function addTopic($name) {
        return $this->userModel->addTopic($name);
    }
    public function addQuestion($topic_id,$question_text,$odgovor,$tip){
        return $this->userModel->addQuestion($topic_id,$question_text,$odgovor,$tip);

    }   

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
