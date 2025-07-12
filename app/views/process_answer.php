<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Kviz - pitanje <?= $_SESSION['quiz']['current_question_index'] + 1 ?></title>
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
    <h2>Pitanje <?= $_SESSION['quiz']['current_question_index'] + 1 ?></h2>
    <div id="question-text"><?= htmlspecialchars($currentQuestion['tekst_pitanja']) ?></div>

    <form id="answer-form">
        <div id="answer-area"></div>

        <div class="nav-buttons">
            <button type="button" id="prev">Prethodno</button>
            <button type="button" id="next">Iduće</button>
        </div>
    </form>
</div>

<script>
    const question = <?= json_encode($currentQuestion) ?>;
    const index = <?= $_SESSION['quiz']['current_question_index'] ?>;
    const total = <?= $_SESSION['quiz']['total_questions'] ?>;

    $(document).ready(function () {
        const $answerArea = $('#answer-area');

        if (question.tip === 'input') {
            $answerArea.html('<input type="text" name="odgovor" style="width:100%; padding: 8px;">');
        } else if (question.tip === 'multiple' && question.opcije) {
            const options = question.opcije.split(',').map(opt => opt.trim());
            let html = '';
            options.forEach(opt => {
                html += `<div><label><input type="radio" name="odgovor" value="${opt}"> ${opt}</label></div>`;
            });
            $answerArea.html(html);
        } else {
            $answerArea.html('<em>Tip pitanja nije podržan.</em>');
        }

        $('#prev').on('click', function () {
            window.location.href = 'index.php?action=navigate_quiz&dir=prev';
        });

        $('#next').on('click', function () {
            window.location.href = 'index.php?action=navigate_quiz&dir=next';
        });
    });
</script>
</body>
</html>
