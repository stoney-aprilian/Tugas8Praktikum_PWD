<?php
// index.php
require_once 'classes/Game.php';
$game = new Game();
$games = $game->getAllGames();

// Handle success/error messages
$message = '';
$messageType = '';

if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
    $message = 'Game berhasil dihapus!';
    $messageType = 'success';
} elseif (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'delete_failed':
            $message = 'Gagal menghapus game!';
            $messageType = 'error';
            break;
        case 'invalid_id':
            $message = 'ID game tidak valid!';
            $messageType = 'error';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Game Library - Koleksi Game Terbaik</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="overlay">
        <header>
            <h1><i class="fas fa-gamepad"></i> Game Library</h1>
            <p class="subtitle">Kelola koleksi game favoritmu</p>
        </header>

        <div class="container">
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <i class="fas fa-<?= $messageType == 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                    <div>
                        <strong><?= htmlspecialchars($message) ?></strong>
                    </div>
                </div>
            <?php endif; ?>

            <div class="actions">
                <a href="add.php" class="add-button">
                    <i class="fas fa-plus"></i> Tambah Game Baru
                </a>
                <div class="stats">
                    <div class="stat-item">
                        <i class="fas fa-list"></i>
                        <span>Total Game: <?= count($games) ?></span>
                    </div>
                </div>
            </div>

            <?php if (empty($games)): ?>
                <div class="empty-state">
                    <i class="fas fa-ghost"></i>
                    <h3>Belum ada game tersimpan</h3>
                    <p>Mulai dengan menambahkan game pertamamu!</p>
                    <a href="add.php" class="add-button">Tambah Game</a>
                </div>
            <?php else: ?>
                <!-- View Toggle -->
                <div class="view-toggle">
                    <button class="toggle-btn active" data-view="card">
                        <i class="fas fa-th-large"></i> Card View
                    </button>
                    <button class="toggle-btn" data-view="table">
                        <i class="fas fa-table"></i> Table View
                    </button>
                </div>

                <!-- Card View -->
                <div id="card-view" class="game-list">
                    <?php foreach ($games as $g): ?>
                    <div class="game-card">
                        <div class="game-cover-container">
                            <?php if (!empty($g['cover_url'])): ?>
                                <img src="uploads/<?= htmlspecialchars($g['cover_url']) ?>" 
                                     alt="<?= htmlspecialchars($g['nama_game']) ?>" 
                                     class="game-cover">
                            <?php else: ?>
                                <div class="no-cover">
                                    <i class="fas fa-image"></i>
                                    <span>No Cover</span>
                                </div>
                            <?php endif; ?>
                            <div class="rating-badge">
                                <i class="fas fa-star"></i> <?= $g['rating'] ?>
                            </div>
                        </div>
                        
                        <div class="game-info">
                            <h3 class="game-title"><?= htmlspecialchars($g['nama_game']) ?></h3>
                            <div class="game-details">
                                <p><i class="fas fa-tags"></i> <?= htmlspecialchars($g['genre']) ?></p>
                                <p><i class="fas fa-user"></i> <?= htmlspecialchars($g['developer']) ?></p>
                                <p><i class="fas fa-desktop"></i> <?= htmlspecialchars($g['platform']) ?></p>
                                <p><i class="fas fa-calendar"></i> <?= date('d M Y', strtotime($g['release_date'])) ?></p>
                            </div>
                            
                            <div class="game-actions">
                                <a href="edit.php?id=<?= $g['id'] ?>" class="btn btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="#" class="btn btn-delete" 
                                   onclick="confirmDelete(<?= $g['id'] ?>, '<?= addslashes($g['nama_game']) ?>')">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Table View -->
                <div id="table-view" class="table-container" style="display: none;">
                    <table class="games-table">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Judul</th>
                                <th>Genre</th>
                                <th>Developer</th>
                                <th>Platform</th>
                                <th>Rating</th>
                                <th>Tanggal Rilis</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($games as $g): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($g['cover_url'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($g['cover_url']) ?>" 
                                             alt="<?= htmlspecialchars($g['nama_game']) ?>" 
                                             class="cover-img">
                                    <?php else: ?>
                                        <div class="no-cover-small">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="game-title-cell"><?= htmlspecialchars($g['nama_game']) ?></td>
                                <td><?= htmlspecialchars($g['genre']) ?></td>
                                <td><?= htmlspecialchars($g['developer']) ?></td>
                                <td><?= htmlspecialchars($g['platform']) ?></td>
                                <td>
                                    <span class="rating">
                                        <i class="fas fa-star"></i> <?= $g['rating'] ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y', strtotime($g['release_date'])) ?></td>
                                <td class="action-cell">
                                    <a href="edit.php?id=<?= $g['id'] ?>" class="btn btn-edit btn-small">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-delete btn-small" 
                                       onclick="confirmDelete(<?= $g['id'] ?>, '<?= addslashes($g['nama_game']) ?>')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus</h3>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus game <strong id="gameToDelete"></strong>?</p>
                <p class="warning">Aksi ini tidak dapat dibatalkan!</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-cancel" onclick="closeModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button class="btn btn-delete" id="confirmDeleteBtn">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        </div>
    </div>

    <script>
        // View Toggle Functionality
        document.querySelectorAll('.toggle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const view = this.dataset.view;
                
                // Update active button
                document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Toggle views
                if (view === 'card') {
                    document.getElementById('card-view').style.display = 'flex';
                    document.getElementById('table-view').style.display = 'none';
                } else {
                    document.getElementById('card-view').style.display = 'none';
                    document.getElementById('table-view').style.display = 'block';
                }
                
                // Save preference
                localStorage.setItem('preferredView', view);
            });
        });

        // Load saved view preference
        const savedView = localStorage.getItem('preferredView') || 'card';
        if (savedView === 'table') {
            document.querySelector('[data-view="table"]').click();
        }

        // Delete Confirmation Modal
        let gameIdToDelete = null;

        function confirmDelete(id, title) {
            gameIdToDelete = id;
            document.getElementById('gameToDelete').textContent = title;
            document.getElementById('deleteModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            gameIdToDelete = null;
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (gameIdToDelete) {
                window.location.href = 'delete.php?id=' + gameIdToDelete;
            }
        });

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // ESC key to close modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Auto-hide alerts
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }, 5000);
        });

        // Smooth animations on load
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.game-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>