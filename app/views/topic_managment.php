<?php
// Provjera da je korisnik administrator
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php?action=login');
    exit;
}

// Provjera da su podaci o tematici dostupni
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
            margin-bottom: 10px;
        }
        .question-item div {
            margin-bottom: 5px;
        }
        .delete-btn {
            background-color: #e53935;
        }
        .delete-btn:hover {
            background-color: #c62828;
        }
        .form-section {
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 20px;
        }
        label { display: block; margin-top: 10px; }
        input[type=text], textarea {
            width: 100%; padding: 8px; margin-top: 5px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container">
    <h2>Tematika: <?= htmlspecialchars($topic['name']) ?></h2>

    <div>
        <button onclick="location.href='index.php?action=home'">Odustani</button>
        <button onclick="toggleQuestions()">Pregled pitanja</button>
        <button onclick="toggleAddForm()">Dodaj pitanje</button>
        <button onclick="location.href='index.php?action=start_quiz&topic=<?= urlencode($topic['name']) ?>'">Započni kviz</button>
    </div>

    <div id="questions" class="question-list" style="display:none;">
        <h3>Sva pitanja:</h3>
        <?php if (!empty($questions)): ?>
            <?php foreach ($questions as $q): ?>
                <div class="question-item" id="question-<?= $q['id'] ?>">
                    <div><strong>Pitanje:</strong> <?= htmlspecialchars($q['tekst_pitanja'] ?? $q['tekst'] ?? 'Bez teksta') ?></div>
                    <div><strong>Odgovor:</strong> <?= htmlspecialchars($q['odgovor']) ?></div>
                    <div><strong>Tip:</strong> <?= htmlspecialchars($q['tip']) ?></div>
                    <?php if ($q['tip'] === 'multiple'): ?>
                        <div><strong>Opcije:</strong>
                            <ul>
                                <?php foreach (explode(',', $q['opcije']) as $opcija): ?>
                                    <li><?= htmlspecialchars(trim($opcija)) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <button class="delete-btn" onclick="obrisiPitanje(<?= $q['id'] ?>)">Obriši</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nema dostupnih pitanja za ovu tematiku.</p>
        <?php endif; ?>
    </div>

    <div class="form-section" id="add-form" style="display:none;">
        <h3>Dodaj novo pitanje</h3>
        <form action="index.php?action=dodaj_pitanje" method="post">
            <input type="hidden" name="id_tematike" value="<?= $topic['id'] ?>">

            <label for="tekst_pitanja">Tekst pitanja:</label>
            <textarea name="tekst_pitanja" required></textarea>

            <label>Tip pitanja:</label>
            <label><input type="radio" name="tip" value="input" checked> Unos</label>
            <label><input type="radio" name="tip" value="multiple"> Višestruki izbor</label>

            <div id="opcije-container" style="display:none;">
                <label for="opcije">Ponudeni odgovori (odvojeni zarezom):</label>
                <input type="text" name="opcije">
            </div>

            <label for="odgovor">Točan odgovor:</label>
            <input type="text" name="odgovor" required>

            <button type="submit">Spremi pitanje</button>
        </form>
    </div>
</div>

<script>
function toggleQuestions() {
    const q = document.getElementById('questions');
    q.style.display = q.style.display === 'none' ? 'block' : 'none';
}
function toggleAddForm() {
    const f = document.getElementById('add-form');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
}
function obrisiPitanje(id) {
    if (confirm("Jeste li sigurni da želite obrisati ovo pitanje?")) {
        $.post('index.php?action=obrisi_pitanje', { id: id }, function () {
            document.getElementById('question-' + id).remove();
        }).fail(function () {
            alert("Greška pri brisanju pitanja.");
        });
    }
}

// Dinamičko prikazivanje opcija
$(document).ready(function () {
    $('input[name="tip"]').on('change', function () {
        if ($(this).val() === 'multiple') {
            $('#opcije-container').show();
        } else {
            $('#opcije-container').hide();
        }
    });
});
</script>
</body>
</html>
