<?php
require_once __DIR__ . '/../models/Users.php';

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
                session_start();
                $_SESSION['user'] = $user;
                header('Location: index.php?action=dashboard');
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
        session_start();
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }
    public function getUserStats($username) {
        return $this->userModel->getUserStats($username);
    }   
}
