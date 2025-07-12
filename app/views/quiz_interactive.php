<?php
// Ovo je frontend za kviz koji radi preko jQueryja i AJAX-a
if (!isset($_GET['topic']) || !isset($_SESSION['user'])) {
    header("Location: index.php?action=home");
    exit;
}

$tematikaId = $topic['id'];
?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Kviz: <?= htmlspecialchars($topic['name']) ?></title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial; background: #fffde7; padding: 20px; }
        .quiz-container { background: white; padding: 20px; border-radius: 8px; max-width: 700px; margin: auto; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
        .question-text { font-weight: bold; margin-bottom: 15px; }
        .navigation-buttons { margin-top: 20px; }
        .navigation-buttons button { padding: 8px 20px; margin: 5px; background-color: #fbc02d; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .navigation-buttons button:hover { background-color: #f9a825; }
    </style>
</head>
<body>

<div class="quiz-container">
    <h2>Kviz: <?= htmlspecialchars($topic['name']) ?></h2>
    <div id="question-number"></div>
    <div class="question-text" id="question-text"></div>
    <div id="answer-area"></div>

    <div class="navigation-buttons">
        <button id="prev-btn">Prethodno pitanje</button>
        <button id="next-btn">Iduće pitanje</button>
    </div>
</div>

<script>
let pitanja = [];
let current = 0;
let korisnikoviOdgovori = {}; // mapa id -> odgovor

// Dohvati pitanja
$.getJSON("app/models/getPitanja.php?tematika_id=<?= $tematikaId ?>", function (data) {
    pitanja = data;
    prikaziPitanje();
});

function prikaziPitanje() {
    if (pitanja.length === 0) return;

    let p = pitanja[current];
    $("#question-number").text("Pitanje " + (current + 1) + " od " + pitanja.length);
    $("#question-text").text(p.question);

    let html = '';
    if (p.type === 'text_input') {
        html = `<input type="text" id="odgovor" value="${korisnikoviOdgovori[p.id] || ''}">`;
    } else {
        p.answers.forEach((ans, idx) => {
            const checked = korisnikoviOdgovori[p.id] === ans ? "checked" : "";
            html += `<div><input type="radio" name="odgovor" value="${ans}" ${checked}> ${ans}</div>`;
        });
    }

    $("#answer-area").html(html);
}

function spremiOdgovor() {
    const p = pitanja[current];
    if (p.type === 'text_input') {
        korisnikoviOdgovori[p.id] = $("#odgovor").val();
    } else {
        korisnikoviOdgovori[p.id] = $("input[name='odgovor']:checked").val();
    }
}

$("#next-btn").click(() => {
    spremiOdgovor();
    if (current < pitanja.length - 1) {
        current++;
        prikaziPitanje();
    }
});

$("#prev-btn").click(() => {
    spremiOdgovor();
    if (current > 0) {
        current--;
        prikaziPitanje();
    }
});
</script>

</body>
</html>
