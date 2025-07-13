<?php
$questions = $_SESSION['quiz']['questions'];
$userAnswers = $_SESSION['quiz']['answers'] ?? [];
?>
<h2>Provjera odgovora</h2>
<?php foreach ($questions as $i => $pitanje): ?>
    <div onclick="location.href='index.php?action=go_to_question&index=<?= $i ?>&from_review=1'">
        <p><?= $i + 1 ?>. <?= $pitanje['tekst_pitanja'] ?></p>
        <p><strong>Tvoj odgovor:</strong> <?= htmlspecialchars($userAnswers[$i] ?? 'Nema odgovora') ?></p>
    </div>
<?php endforeach; ?>
<form method="post" action="index.php?action=predaj_kviz">
    <button type="submit">Predaj sve i završi</button>
</form>
