<?php
require_once __DIR__ .'/../models/LeaderboardModel.php';
require_once __DIR__ .'/../Core/database.php';

class LeaderboardController {
    private $leaderboardModel;

    public function __construct() {
        $db = Database::getInstance();
        $this->leaderboardModel = new LeaderboardModel($db);
    }

    public function show() {
        $players = $this->leaderboardModel->getLeaderboard();
        include __DIR__. '/../views/leaderboard.php';
    }
}
?>