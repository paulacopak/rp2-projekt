<h2>Lista pitanja</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tematika</th>
            <th>Pitanje</th>
            <th>Opcije</th>
            <th>Točni odgovor</th>
            <th>Akcija</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pitanja as $p): ?>
            <tr>
                <td><?= $p->id ?></td>
                <td><?= htmlspecialchars($p->tematika_naziv) ?></td>
                <td><?= htmlspecialchars($p->pitanje) ?></td>
                <td>
                    1. <?= htmlspecialchars($p->opcija1) ?><br>
                    2. <?= htmlspecialchars($p->opcija2) ?><br>
                    3. <?= htmlspecialchars($p->opcija3) ?>
                </td>
                <td><?= $p->tocni_odgovor ?></td>
                <td>
                    <form method="POST" action="/quiz/deleteQuestion/<?= $p->id ?>" onsubmit="return confirm('Jeste li sigurni da želite obrisati ovo pitanje?');">
                        <button type="submit">Obriši</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
