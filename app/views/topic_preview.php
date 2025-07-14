<?php
if (!isset($_SESSION['user'])) {
    header('Location: index.php?action=login');
    exit;
}

if (!isset($topic) || !isset($topic['id']) || !isset($topic['name'])) {
    header('Location: index.php?action=home');
    exit;
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Tematika: <?= htmlspecialchars($topic['name']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fffde7;
            padding: 20px;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            color: #fbc02d;
        }
        button {
            margin: 10px;
            padding: 12px 25px;
            background-color: #fbc02d;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #f9a825;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Tematika: <?= htmlspecialchars($topic['name']) ?></h2>
    <p>Jeste li spremni započeti kviz?</p>
    <button onclick="location.href='index.php?action=home'">Odustani</button>
    <button onclick="location.href='index.php?action=begin_quiz&topic=<?= urlencode($topic['name']) ?>'">Započni kviz</button>
</div>
</body>
</html>