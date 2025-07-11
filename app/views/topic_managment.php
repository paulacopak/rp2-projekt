<?php
// Provjera da je korisnik administrator
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php?action=login');
    exit;
}

// Provjera da su podaci o tematici dostupni (proslijeđeni iz QuizController::startQuiz)
if (!isset($topic) || !isset($topic['id']) || !isset($topic['name'])) {
    header('Location: index.php?action=home');
    exit;
}
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Upravljanje tematikom: <?= htmlspecialchars($topic['name']) ?></title>
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
            max-width: 800px;
            margin: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 {
            color: #fbc02d;
        }
        button {
            margin: 5px;
            padding: 10px 20px;
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
        .question-list {
            margin-top: 20px;
        }
        .question-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Tematika: <?= htmlspecialchars($topic['name']) ?></h2>

    <div>
        <button onclick="location.href='index.php?action=home'">Odustani</button>
        <button onclick="toggleQuestions()">Pregled pitanja</button>
        <button onclick="location.href='index.php?action=start_quiz&topic=<?= urlencode($topic['name']) ?>'">Započni kviz</button>
    </div>

    <div id="questions" class="question-list" style="display:none;">
        <h3>Sva pitanja:</h3>
        <?php if (!empty($questions)): ?>
            <?php foreach ($questions as $q): ?>
                <div class="question-item">
                    <?= htmlspecialchars($q['tekst_pitanja'] ?? $q['tekst'] ?? 'Bez teksta') ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nema dostupnih pitanja za ovu tematiku.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleQuestions() {
    const q = document.getElementById('questions');
    q.style.display = q.style.display === 'none' ? 'block' : 'none';
}
</script>
</body>
</html>
