<?php
// edit.php
require_once 'classes/Game.php';
$game = new Game();
$errors = [];
$success = false;

// Get game ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id === 0) {
    header("Location: index.php");
    exit;
}

// Get game data
$gameData = $game->getGameById($id);
if (!$gameData) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validasi input
        $errors = $game->validateInput($_POST);
        
        if (empty($errors)) {
            // Handle file upload
            $coverFileName = $gameData['cover_url']; // Keep existing cover
            if (isset($_FILES['cover']) && $_FILES['cover']['error'] !== UPLOAD_ERR_NO_FILE) {
                try {
                    $newCover = $game->handleFileUpload($_FILES['cover']);
                    if ($newCover) {
                        // Delete old cover if exists
                        if ($gameData['cover_url'] && file_exists("uploads/" . $gameData['cover_url'])) {
                            unlink("uploads/" . $gameData['cover_url']);
                        }
                        $coverFileName = $newCover;
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
            
            if (empty($errors)) {
                $result = $game->updateGame(
                    $id,
                    trim($_POST['judul']),
                    trim($_POST['genre']),
                    trim($_POST['developer']),
                    trim($_POST['platform']),
                    floatval($_POST['rating']),
                    $_POST['release_date'],
                    $coverFileName
                );
                
                if ($result) {
                    $success = true;
                    header("refresh:2;url=index.php");
                } else {
                    $errors[] = "Gagal mengupdate data game";
                }
            }
        }
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Game - Game Library</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="overlay">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php"><i class="fas fa-home"></i> Beranda</a>
                <span><i class="fas fa-chevron-right"></i></span>
                <span>Edit Game</span>
            </div>

            <div class="form-container">
                <div class="form-header">
                    <h1><i class="fas fa-edit"></i> Edit Game</h1>
                    <p>Update informasi game</p>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Terjadi kesalahan:</strong>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Berhasil!</strong>
                            Game telah diupdate. Anda akan diarahkan ke halaman utama...
                        </div>
                    </div>
                <?php else: ?>
                    <form method="POST" enctype="multipart/form-data" class="game-form" id="gameForm">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="judul">
                                    <i class="fas fa-gamepad"></i> Judul Game *
                                </label>
                                <input type="text" 
                                       id="judul" 
                                       name="judul" 
                                       value="<?= isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : htmlspecialchars($gameData['nama_game']) ?>"
                                       placeholder="Masukkan judul game"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="genre">
                                    <i class="fas fa-tags"></i> Genre *
                                </label>
                                <select id="genre" name="genre" required>
                                    <option value="">Pilih Genre</option>
                                    <?php 
                                    $selectedGenre = isset($_POST['genre']) ? $_POST['genre'] : $gameData['genre'];
                                    $genres = ['Action', 'Adventure', 'RPG', 'Strategy', 'Simulation', 'Sports', 'Racing', 'Fighting', 'Horror', 'Puzzle'];
                                    foreach ($genres as $g): ?>
                                        <option value="<?= $g ?>" <?= $selectedGenre == $g ? 'selected' : '' ?>><?= $g ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="developer">
                                    <i class="fas fa-user"></i> Developer *
                                </label>
                                <input type="text" 
                                       id="developer" 
                                       name="developer" 
                                       value="<?= isset($_POST['developer']) ? htmlspecialchars($_POST['developer']) : htmlspecialchars($gameData['developer']) ?>"
                                       placeholder="Nama developer"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="platform">
                                    <i class="fas fa-desktop"></i> Platform *
                                </label>
                                <select id="platform" name="platform" required>
                                    <option value="">Pilih Platform</option>
                                    <?php 
                                    $selectedPlatform = isset($_POST['platform']) ? $_POST['platform'] : $gameData['platform'];
                                    $platforms = ['PC', 'PlayStation 5', 'PlayStation 4', 'Xbox Series X/S', 'Xbox One', 'Nintendo Switch', 'Mobile', 'Multi-Platform'];
                                    foreach ($platforms as $p): ?>
                                        <option value="<?= $p ?>" <?= $selectedPlatform == $p ? 'selected' : '' ?>><?= $p ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="rating">
                                    <i class="fas fa-star"></i> Rating *
                                </label>
                                <div class="rating-input">
                                    <input type="number" 
                                           id="rating" 
                                           name="rating" 
                                           min="0" 
                                           max="10" 
                                           step="0.1"
                                           value="<?= isset($_POST['rating']) ? $_POST['rating'] : $gameData['rating'] ?>"
                                           placeholder="0.0"
                                           required>
                                    <span class="rating-scale">/ 10</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="release_date">
                                    <i class="fas fa-calendar"></i> Tanggal Rilis *
                                </label>
                                <input type="date" 
                                       id="release_date" 
                                       name="release_date" 
                                       value="<?= isset($_POST['release_date']) ? $_POST['release_date'] : $gameData['release_date'] ?>"
                                       required>
                            </div>
                        </div>

                        <div class="form-group file-upload-group">
                            <label for="cover">
                                <i class="fas fa-image"></i> Cover Game
                            </label>
                            
                            <?php if ($gameData['cover_url']): ?>
                                <div class="current-cover">
                                    <p>Cover saat ini:</p>
                                    <img src="uploads/<?= htmlspecialchars($gameData['cover_url']) ?>" 
                                         alt="Current cover" 
                                         style="max-width: 200px; max-height: 150px; border-radius: 8px;">
                                </div>
                            <?php endif; ?>
                            
                            <div class="file-upload-container">
                                <input type="file" 
                                       id="cover" 
                                       name="cover" 
                                       accept="image/*"
                                       class="file-input">
                                <div class="file-upload-area" id="fileUploadArea">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Klik untuk memilih gambar baru atau drag & drop</p>
                                    <small>Format: JPG, PNG, GIF (Maks: 2MB)</small>
                                </div>
                                <div class="file-preview" id="filePreview" style="display: none;">
                                    <img id="previewImage" alt="Preview">
                                    <button type="button" class="remove-file" onclick="removeFile()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="index.php" class="btn btn-cancel">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-save"></i> Update Game
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // File upload functionality (same as add.php)
        const fileInput = document.getElementById('cover');
        const fileUploadArea = document.getElementById('fileUploadArea');
        const filePreview = document.getElementById('filePreview');
        const previewImage = document.getElementById('previewImage');

        fileUploadArea.addEventListener('click', () => fileInput.click());

        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('drag-over');
        });

        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.classList.remove('drag-over');
        });

        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('drag-over');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(files[0]);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });

        function handleFileSelect(file) {
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImage.src = e.target.result;
                    fileUploadArea.style.display = 'none';
                    filePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        function removeFile() {
            fileInput.value = '';
            fileUploadArea.style.display = 'block';
            filePreview.style.display = 'none';
            previewImage.src = '';
        }

        document.getElementById('gameForm').addEventListener('submit', function(e) {
            const rating = parseFloat(document.getElementById('rating').value);
            if (rating < 0 || rating > 10) {
                e.preventDefault();
                alert('Rating harus antara 0 dan 10');
                return false;
            }
        });

        <?php if ($success): ?>
        let countdown = 3;
        const countdownElement = document.createElement('span');
        countdownElement.innerHTML = ` (${countdown})`;
        document.querySelector('.alert-success div').appendChild(countdownElement);
        
        const timer = setInterval(() => {
            countdown--;
            countdownElement.innerHTML = ` (${countdown})`;
            if (countdown <= 0) {
                clearInterval(timer);
            }
        }, 1000);
        <?php endif; ?>
    </script>
</body>
</html>