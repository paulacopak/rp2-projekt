<?php if (isset($_GET['success'])): ?>
    <p style="color: green;">Pitanje je uspješno dodano!</p>
<?php endif; ?>


<form method="POST" action="/quiz/addQuestion">

    <label>Tematika:</label>
    <select name="tematika_id" required>
        <?php foreach ($tematike as $tematika): ?>
            <option value="<?= $tematika['id'] ?>"><?= htmlspecialchars($tematika['naziv']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Tip pitanja:</label>
    <select name="tip" id="tipPitanja" required onchange="toggleOpcije()">
        <option value="text_input">Tekstualni odgovor</option>
        <option value="multiple_choice">Višestruki izbor</option>
    </select>

    <label>Pitanje:</label>
    <textarea name="tekst_pitanja" required></textarea>

    <!-- Polje za točan odgovor za tekstualni unos -->
    <div id="textInputOdgovor">
        <label>Točan odgovor:</label>
        <input type="text" name="odgovor" >
    </div>

    <!-- Polja za opcije i točan odgovor za multiple choice -->
    <div id="multipleChoiceOpcije" style="display:none;">
        <label>Opcija 1:</label>
        <input type="text" name="opcije[]" >

        <label>Opcija 2:</label>
        <input type="text" name="opcije[]" >

        <label>Opcija 3:</label>
        <input type="text" name="opcije[]" >

        <label>Indeks točnog odgovora (1-3):</label>
        <input type="number" name="tocan_odgovor" min="1" max="3" >
    </div>

    <button type="submit">Dodaj pitanje</button>
</form>

<script>
function toggleOpcije() {
    const tip = document.getElementById('tipPitanja').value;
    if (tip === 'multiple_choice') {
        document.getElementById('multipleChoiceOpcije').style.display = 'block';
        document.getElementById('textInputOdgovor').style.display = 'none';
    } else {
        document.getElementById('multipleChoiceOpcije').style.display = 'none';
        document.getElementById('textInputOdgovor').style.display = 'block';
    }
}
</script>
