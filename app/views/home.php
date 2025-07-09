<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Po�etna</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0; padding: 0;
            background-color: #fff9c4;
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
            padding: 30px;
        }
        .topics {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .topic-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            width: 200px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.2s;
        }
        .topic-card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="index.php?action=home">HOME</a>
    <a href="index.php?action=profile">Profil</a>
    <a href="index.php?action=ranking">Rang Lista</a>
    <a href="index.php?action=logout">Odjava</a>
</div>

<div class="container">
    <h2>Odaberi tematski kviz:</h2>
    <div class="topics" id="topics-container">
        <?php foreach ($topics as $topic): ?>
            <div class="topic-card" onclick="location.href='index.php?action=start_quiz&topic=<?= urlencode($topic['name']) ?>'">
                <?= htmlspecialchars($topic['name']) ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if($_SESSION['user']['role']==='admin'):?>
        <div id="admin-controls">
            <button id="show-add-topic">Dodaj tematiku</button>
            <div id="add-topic-form" style ="display:none;margin-top:10px;">
                <input type="text" id ="new-topic-name" placeholder ="Ime tematike">
                <button id ="cancel-add">Odustani</button>
                <button id="submit-add">Dodaj tematiku</button>

            </div>
        </div>
    <?php endif;?>
</div>
<script>
document.getElementById('show-add-topic').addEventListener('click', () => {
    document.getElementById('add-topic-form').style.display = 'block';
    document.getElementById('show-add-topic').style.display = 'none';
});

document.getElementById('cancel-add').addEventListener('click', () => {
    document.getElementById('add-topic-form').style.display = 'none';
    document.getElementById('show-add-topic').style.display = 'inline-block';
});

document.getElementById('submit-add').addEventListener('click', () => {
    const topicName = document.getElementById('new-topic-name').value.trim();
    if (!topicName) return;

    fetch('index.php?action=ajax_add_topic', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'name=' + encodeURIComponent(topicName)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const topicCard = document.createElement('div');
            topicCard.className = 'topic-card';
            topicCard.textContent = topicName;
            topicCard.onclick = () => {
                location.href = 'index.php?action=start_quiz&topic=' + encodeURIComponent(topicName);
            };
            document.querySelector('#topics-container').appendChild(topicCard);

            // očisti i sakrij formu
            document.getElementById('new-topic-name').value = '';
            document.getElementById('add-topic-form').style.display = 'none';
            document.getElementById('show-add-topic').style.display = 'inline-block';
        } else {
            alert('Greška pri dodavanju tematike.');
        }
    });
});
</script>


</body>
</html>
