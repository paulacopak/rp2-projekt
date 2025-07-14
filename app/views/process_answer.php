<?php


if (!isset($_SESSION['quiz'])) {
    // Ako kviz nije započet, preusmjeri na početnu
    header('Location: index.php?action=home');
    exit;
}

$currentIndex = $_SESSION['quiz']['current_question_index'];
$questions = $_SESSION['quiz']['questions'];
$totalQuestions = count($questions);

if ($currentIndex < 0 || $currentIndex >= $totalQuestions) {
    echo "Greška: nevažeći indeks pitanja.";
    exit;
}

$currentQuestion = $questions[$currentIndex];

// Dohvati prethodno uneseni odgovor ako postoji
$uneseniOdgovor = $_SESSION['quiz']['answers'][$currentIndex] ?? '';

?>
<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Kviz - pitanje <?= $currentIndex + 1 ?></title>
    <style>
        body { font-family: Arial; background: #fff9c4; padding: 30px; }
        .quiz-container {
            background: white; padding: 20px; border-radius: 10px;
            max-width: 600px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .nav-buttons {
            margin-top: 20px; display: flex; justify-content: space-between;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="quiz-container">
    <h2>Pitanje <?= $currentIndex + 1 ?> od <?= $totalQuestions ?></h2>
    <div id="question-text"><?= htmlspecialchars($currentQuestion['tekst_pitanja']) ?></div>

    <form id="answer-form">
        <div id="answer-area"></div>

        <div class="nav-buttons">
            <button type="button" id="prev">Prethodno</button>
            <button type="button" id="cancel" style="margin: 0 10px; background-color: #f44336; color: white; border: none; border-radius: 5px; padding: 8px 16px;">
             Odustani
            </button>
            <button type="button" id="next">
                <?= ($currentIndex === $totalQuestions - 1) ? 'Završi kviz' : 'Iduće' ?>
            </button>
        </div>
    </form>
</div>

<script>
    const question = <?= json_encode($currentQuestion) ?>;
    const index = <?= $currentIndex ?>;
    const total = <?= $totalQuestions ?>;
    const savedAnswer = <?= json_encode($uneseniOdgovor) ?>;

    $(document).ready(function () {
        const $answerArea = $('#answer-area');

        if (question.tip === 'input') {
            $answerArea.html(`<input type="text" name="odgovor" style="width:100%; padding: 8px;" value="${savedAnswer}">`);
        } else if (question.tip === 'multiple' && question.opcije) {
            const options = question.opcije.split(',').map(opt => opt.trim());
            let html = '';
            options.forEach(opt => {
                const checked = (opt === savedAnswer) ? 'checked' : '';
                html += `<div><label><input type="radio" name="odgovor" value="${opt}" ${checked}> ${opt}</label></div>`;
            });
            $answerArea.html(html);
        } else {
            $answerArea.html('<em>Tip pitanja nije podržan.</em>');
        }

        $('#prev').on('click', function () {
            spremiOdgovor('prev');
        });

        $('#next').on('click', function () {
            if (index === total - 1) {
                spremiOdgovor('review');
            } else {
                spremiOdgovor('next');
            }
        });
        $('#cancel').on('click', function () {
        if(confirm('Jeste li sigurni da želite odustati od kviza? Svi uneseni odgovori će biti izbrisani.')) {
            $.post('index.php?action=odustani', {}, function () {
                window.location.href = 'index.php?action=home';
            });
        }
    });
    });

    function spremiOdgovor(dir) {
        let odgovor = $('input[name="odgovor"]:checked').val();
        if (odgovor === undefined) {
            odgovor = $('input[name="odgovor"]').val();  // za input
        }

        // Obavezna validacija: minimalno da je odgovor unešen
        if (!odgovor || odgovor.trim() === '') {
            alert('Molimo unesite odgovor prije nastavka.');
            return;
        }

        $.post('index.php?action=spremi_odgovor', {
            index: index,
            odgovor: odgovor
        }, function () {
            if (dir === 'next') {
                window.location.href = 'index.php?action=navigate_quiz&dir=next';
            } else if (dir === 'prev') {
                window.location.href = 'index.php?action=navigate_quiz&dir=prev';
            } else if (dir === 'review') {
                window.location.href = 'index.php?action=review_answers';
            }
        });
    }
</script>
</body>
</html>
