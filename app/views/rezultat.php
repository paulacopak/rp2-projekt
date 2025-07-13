<?php
require_once __DIR__.'/../Core/database.php';
$rez = $_SESSION['quiz']['rezultat'];
$questions = $_SESSION['quiz']['questions'];
$answers = $_SESSION['quiz']['answers'] ?? [];
?>
<h2>Rezultat: <?= $rez['tocno'] ?>/<?= $rez['ukupno'] ?></h2>
<p>Vrijeme rješavanja: <?= gmdate("i:s", $rez['trajanje']) ?> sek</p>

<h3>Pregled odgovora</h3>
<?php foreach ($questions as $i => $q): ?>
    <div style="margin-bottom: 20px;">
        <p><?= $i + 1 ?>. <?= $q['tekst_pitanja'] ?></p>
        <?php if ($q['tip'] === 'input'): ?>
            <p><strong>Tvoj odgovor:</strong> <?= htmlspecialchars($answers[$i] ?? '') ?></p>
            <p><strong>Točan odgovor:</strong> <?= $q['odgovor'] ?></p>
        <?php else:
            $opcije = explode(',', $q['opcije']);
            foreach ($opcije as $opcija): ?>
                <div style="color:
                    <?= trim($opcija) === trim($q['odgovor']) ? 'green' : (
                        trim($opcija) === trim($answers[$i] ?? '') ? 'red' : 'black'
                    ) ?>;">
                    <?= trim($opcija) ?>
                </div>
            <?php endforeach;
        endif; ?>
    </div>
<?php endforeach; ?>

<!-- Gumb za povratak na početnu -->
<div style="text-align:center; margin-top: 30px;">
    <a href="index.php?action=home" style="
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        background-color: #4CAF50;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        ">Natrag na početnu</a>
</div>