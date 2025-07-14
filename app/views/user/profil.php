<!DOCTYPE html>
<html>
<head>
    <title>Moj profil</title>
</head>
<body>
    <h2>Profil: <?= htmlspecialchars($_SESSION['user']['username']) ?></h2>
    <p>Uloga: <?= htmlspecialchars($_SESSION['user']['role']) ?></p>
    <?php if (!empty($stats)): ?>
    <p>Kreirano: <?= htmlspecialchars($stats[0]['start_time']) ?></p>
    <?php else: ?>
    <p>Korisnik još nema kviz statistiku.</p>
    <?php endif; ?>
    <h3>Statistika kvizova</h3>
    <?php if (empty($stats)): ?>
        <p>Još nemaš rješenih kvizova.</p>
    <?php else: ?>
        <?php if (!empty($stats)): ?>
        <table border="1" cellpadding="5">
            <tr>
                <th>Tematika</th>
                <th>Bodovi</th>
                <th>Ukupno pitanja</th>
                <th>Datum</th>
                <th>Trajanje</th>
                
            </tr>
            <?php foreach ($stats as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['topic']) ?></td>
                <td><?= $s['bodovi'] ?></td>
                <td><?= $s['ukupno'] ?></td>
                <td><?= $s['start_time'] ?></td>
                <td>
                    <?php
                        if ($s['start_time'] && $s['end_time']) {
                            $start = new DateTime($s['start_time']);
                            $end = new DateTime($s['end_time']);
                            $trajanje = $start->diff($end)->format('%i') * 60 + $start->diff($end)->format('%s');
                            echo $trajanje . " sek";
                        } else {
                            echo "n/a";
                        }
                    ?>
                </td>
                

            </tr>
            <?php endforeach; ?>
        </table>
        <button id="updateStatsBtn">Ažuriraj statistiku</button>
            <?php endif; ?>
    <?php endif; ?>
    <p><a href="index.php?action=home">HOME</a></p>
<script>
document.getElementById("updateStatsBtn")?.addEventListener("click", function () {
    const bodovi = <?= json_encode($stats[0]['bodovi'] ?? 0) ?>;
    const ukupno = <?= json_encode($stats[0]['ukupno'] ?? 0) ?>;

    fetch("index.php?action=update_profile", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ bodovi, ukupno })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert("Statistika ažurirana!");
            location.reload();
        } else {
            alert("Greška: " + (result.error || "Pokušaj ponovno."));
        }
    })
    .catch(error => {
        console.error("Greška:", error);
        alert("Došlo je do greške u mreži.");
    });
});
</script>
</body>
</html>
