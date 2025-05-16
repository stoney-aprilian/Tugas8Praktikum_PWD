<?php
require_once "config/database.php";
require_once "classes/Game.php";

$database = new Database();
$db = $database->connect();

$game = new Game($db);
$result = $game->getAllGames();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Daftar Game Favorit</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="overlay">
        <h1>🎮 Daftar Game Favorit</h1>
        <a href="add.php" class="add-button">➕ Tambah Game Baru</a>
        <div class="game-list">
            <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="game-card">
                    <img src="<?= htmlspecialchars($row['cover_url']) ?>" class="game-cover" alt="Cover <?= htmlspecialchars($row['nama_game']) ?>">
                    <div class="game-info">
                        <h2><?= htmlspecialchars($row['nama_game']) ?></h2>
                        <p><strong>Genre:</strong> <?= htmlspecialchars($row['genre']) ?></p>
                        <p><strong>Developer:</strong> <?= htmlspecialchars($row['developer']) ?></p>
                        <p><strong>Platform:</strong> <?= htmlspecialchars($row['platform']) ?></p>
                        <p><strong>Rating:</strong> ⭐ <?= $row['rating'] ?> / 10</p>
                        <button onclick='alert(`<?= json_encode($row, JSON_PRETTY_PRINT) ?>`)'>📄 Lihat JSON</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
