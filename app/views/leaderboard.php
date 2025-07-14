<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Rang lista korisnika</title>
    <style>
       body {
            background-color: #fff9c4; /* blago žuta */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* visina prozora */
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            padding: 30px;
            flex-grow: 1;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        table {
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #fffde7;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        thead {
            background-color: #fbc02d;
            color: white;
        }
        th, td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        tbody tr:nth-child(even) {
            background-color: #fff9c4;
        }
        tbody tr:hover {
            background-color: #f1f8e9;
        }
        td:first-child {
            font-weight: bold;
            color: #555;
        }
        .gold {
            background-color: #fff8e1 !important;
        }
        .silver {
            background-color: #eceff1 !important;
        }
        .bronze {
            background-color: #efebe9 !important;
        }
    </style>
</head>
<body>
    <div class="container">
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
                    <tr class="<?php 
                        echo $index === 0 ? 'gold' : ($index === 1 ? 'silver' : ($index === 2 ? 'bronze' : '')); 
                    ?>">
                    <td>
            <?php
        if ($index === 0) {
            echo '🥇';
        } elseif ($index === 1) {
            echo '🥈';
        } elseif ($index === 2) {
            echo '🥉';
        } else {
            echo $index + 1;
        }
    ?>
</td>
                        <td><?= htmlspecialchars($player['username']) ?></td>
                        <td><?= $player['total_score'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    echo "<p><a href='index.php?action=home'>HOME</a></p>";
    ?>
</body>
</html>
