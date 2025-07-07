<!DOCTYPE html>
<html>
<head><title>Prijava</title></head>
<body>
    <h2>Prijava</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Korisničko ime" required><br>
        <input type="password" name="password" placeholder="Lozinka" required><br>
        <button type="submit">Prijavi se</button>
    </form>
    <p>Nemate račun? <a href="index.php?action=register">Registrirajte se</a></p>
</body>
</html>
