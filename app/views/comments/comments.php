<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Komentari</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff9c4; /* blago žuta */
        }
        .navbar {
            background-color: #fbc02d;
            padding: 15px;
            text-align: center;
        }
        .navbar a {
            margin: 0 15px;
            color: white;
            font-weight: bold;
            text-decoration: none;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #fbc02d;
            text-align: center;
            margin-bottom: 20px;
        }
        .comment-section {
            margin-top: 30px;
        }
        .comment-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            background-color: #f9f9f9;
            position: relative; /* Za pozicioniranje gumba za brisanje */
        }
        .comment-item strong {
            color: #333;
            font-size: 1.1em;
        }
        .comment-item p {
            margin: 10px 0 0;
            color: #555;
            line-height: 1.5;
        }
        .comment-item small {
            display: block;
            margin-top: 10px;
            color: #888;
            font-size: 0.85em;
        }
        .comment-form {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .comment-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box; /* Važno za padding unutar widtha */
            margin-bottom: 10px;
            font-size: 1em;
            resize: vertical; /* Omogućuje vertikalno mijenjanje veličine */
            min-height: 80px;
        }
        .comment-form button {
            background-color: #fbc02d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.2s;
        }
        .comment-form button:hover {
            background-color: #e6a700;
        }
        .message.success {
            color: green;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }
        .message.error {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }
        .delete-comment-btn {
            background-color: #dc3545; /* Crvena boja za brisanje */
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.8em;
            transition: background-color 0.2s;
        }
        .delete-comment-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="navbar">
    <a href="index.php?action=home">HOME</a>
    <a href="index.php?action=profile">Profil</a>
    <a href="index.php?action=ranking">Rang Lista</a>
    <a href="index.php?action=comment">Komentari</a>
        
    <a href="index.php?action=logout">Odjava</a>
    </div>

    <div class="container">
        <h1>Komentari</h1>

        <?php if (isset($_SESSION['comment_message'])): ?>
            <p class="message success"><?= htmlspecialchars($_SESSION['comment_message']); unset($_SESSION['comment_message']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['comment_error'])): ?>
            <p class="message error"><?= htmlspecialchars($_SESSION['comment_error']); unset($_SESSION['comment_error']); ?></p>
        <?php endif; ?>

        <div class="comment-section">
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div id="comment-<?= $comment['id'] ?>" class="comment-item">
                        <strong><?= htmlspecialchars($comment['username']) ?></strong>
                        <small><?= htmlspecialchars((new DateTime($comment['created_at']))->format('d.m.Y H:i')) ?></small>
                        <p><?= nl2br(htmlspecialchars($comment['comment_text'])) ?></p>
                        <?php if (isset($_SESSION['user']) &&
                                 ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['id'] == $comment['user_id'])): ?>
                            <button class="delete-comment-btn"
                                    data-comment-id="<?= $comment['id'] ?>">
                                Obriši
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Još nema komentara. Budite prvi koji će ostaviti komentar!</p>
            <?php endif; ?>
        </div>

        <div class="comment-form">
            <h3>Ostavi komentar</h3>
            <form action="index.php?action=add_comment_process" method="POST">
                <textarea name="comment_text" placeholder="Unesite svoj komentar..." required></textarea><br>
                <button type="submit">Pošalji komentar</button>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.delete-comment-btn').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.getAttribute('data-comment-id');
                if (!confirm('Jeste li sigurni da želite obrisati ovaj komentar?')) return;

                fetch('index.php?action=delete_comment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'comment_id=' + encodeURIComponent(commentId)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const elem = document.getElementById(`comment-${commentId}`);
                        if (elem) elem.remove();
                        alert(data.message);
                    } else {
                        alert('Greška: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Greška pri brisanju komentara:', error);
                    alert('Došlo je do greške prilikom brisanja komentara.');
                });
            });
        });
    });
</script>
</body>
</html>
