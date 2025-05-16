<?php
require_once "config/database.php";
require_once "classes/Game.php";

$database = new Database();
$db = $database->connect();

$game = new Game($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama_game'];
    $genre = $_POST['genre'];
    $developer = $_POST['developer'];
    $platform = $_POST['platform'];
    $rating = $_POST['rating'];

    if ($game->addGame($nama, $genre, $developer, $platform, $rating)) {
        header("Location: index.php");
        exit;
    } else {
        $error = "❌ Gagal menambahkan game.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tambah Game</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>➕ Tambah Game Baru</h1>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST" class="form-container">
        <input type="text" name="nama_game" placeholder="Nama Game" required><br>
        <input type="text" name="genre" placeholder="Genre" required><br>
        <input type="text" name="developer" placeholder="Developer" required><br>
        <input type="text" name="platform" placeholder="Platform" required><br>
        <input type="number" step="0.1" name="rating" placeholder="Rating (0.0 - 10.0)" required><br>
        <button type="submit">Simpan</button>
    </form>
    <br>
    <a href="index.php" class="back-link">← Kembali ke Daftar Game</a>
</body>
</html>
