<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Poèetna</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0; padding: 0;
            background-color: #fff9c4;
        }
        .navbar {
            background-color: #fbc02d;
            padding: 15px;
            text-align: center;
        }
        .navbar a {
            margin: 0 15px;
            color: white;
            font-weight: bold;
            text-decoration: none;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .container {
            padding: 30px;
        }
        .topics {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .topic-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            width: 200px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.2s;
        }
        .topic-card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.php?action=home">HOME</a>
    <a href="index.php?action=profile">Profil</a>
    <a href="index.php?action=ranking">Rang Lista</a>
    <a href="index.php?action=logout">Odjava</a>
</div>

<div class="container">
    <h2>Odaberi tematski kviz:</h2>
    <div class="topics">
        <?php foreach ($topics as $topic): ?>
            <div class="topic-card" onclick="location.href='index.php?action=start_quiz&topic=<?= urlencode($topic['name']) ?>'">
                <?= htmlspecialchars($topic['name']) ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
