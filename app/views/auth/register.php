<!DOCTYPE html>
<html>
<head><title>Registracija</title></head>
<body>
    <h2>Registracija</h2>
    <?php if (!empty($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Korisničko ime" required><br>
        <input type="password" name="password" placeholder="Lozinka" required><br>
        <button type="submit">Registriraj se</button>
    </form>
    <p>Već imate račun? <a href="index.php?action=login">Prijava</a></p>
</body>
</html>
