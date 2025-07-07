<h2>Rang lista svih korisnika</h2>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Korisničko ime</th>
            <th>Ukupni rezultat</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($players as $index => $player): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($player['username']) ?></td>
                <td><?= $player['total_score'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>