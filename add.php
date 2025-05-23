<?php
// add.php
require_once 'classes/Game.php';
$game = new Game();
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validasi input
        $errors = $game->validateInput($_POST);
        
        if (empty($errors)) {
            // Handle file upload
            $coverFileName = null;
            if (isset($_FILES['cover'])) {
                try {
                    $coverFileName = $game->handleFileUpload($_FILES['cover']);
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
            
            if (empty($errors)) {
                $result = $game->addGame(
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
                    // Redirect after 2 seconds
                    header("refresh:2;url=index.php");
                } else {
                    $errors[] = "Gagal menyimpan data game";
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
    <title>Tambah Game Baru - Game Library</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="overlay">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php"><i class="fas fa-home"></i> Beranda</a>
                <span><i class="fas fa-chevron-right"></i></span>
                <span>Tambah Game</span>
            </div>

            <div class="form-container">
                <div class="form-header">
                    <h1><i class="fas fa-plus-circle"></i> Tambah Game Baru</h1>
                    <p>Tambahkan game baru ke dalam koleksi</p>
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
                            Game telah ditambahkan. Anda akan diarahkan ke halaman utama...
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
                                       value="<?= isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : '' ?>"
                                       placeholder="Masukkan judul game"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="genre">
                                    <i class="fas fa-tags"></i> Genre *
                                </label>
                                <select id="genre" name="genre" required>
                                    <option value="">Pilih Genre</option>
                                    <option value="Action" <?= (isset($_POST['genre']) && $_POST['genre'] == 'Action') ? 'selected' : '' ?>>Action</option>
                                    <option value="Adventure" <?= (isset($_POST['genre']) && $_POST['genre'] == 'Adventure') ? 'selected' : '' ?>>Adventure</option>
                                    <option value="RPG" <?= (isset($_POST['genre']) && $_POST['genre'] == 'RPG') ? 'selected' : '' ?>>RPG</option>
                                    <option value="Strategy" <?= (isset($_POST['genre']) && $_POST['genre'] == 'Strategy') ? 'selected' : '' ?>>Strategy</option>
                                    <option value="Simulation" <?= (isset($_POST['genre']) && $_POST['genre'] == 'Simulation') ? 'selected' : '' ?>>Simulation</option>
                                    <option value="Sports" <?= (isset($_POST['genre']) && $_POST['genre'] == 'Sports') ? 'selected' : '' ?>>Sports</option>
                                    <option value="Racing" <?= (isset($_POST['genre']) && $_POST['genre'] == 'Racing') ? 'selected' : '' ?>>Racing</option>
                                    <option value="Fighting" <?= (isset($_POST['genre']) && $_POST['genre'] == 'Fighting') ? 'selected' : '' ?>>Fighting</option>
                                    <option value="Horror" <?= (isset($_POST['genre']) && $_POST['genre'] == 'Horror') ? 'selected' : '' ?>>Horror</option>
                                    <option value="Puzzle" <?= (isset($_POST['genre']) && $_POST['genre'] == 'Puzzle') ? 'selected' : '' ?>>Puzzle</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="developer">
                                    <i class="fas fa-user"></i> Developer *
                                </label>
                                <input type="text" 
                                       id="developer" 
                                       name="developer" 
                                       value="<?= isset($_POST['developer']) ? htmlspecialchars($_POST['developer']) : '' ?>"
                                       placeholder="Nama developer"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="platform">
                                    <i class="fas fa-desktop"></i> Platform *
                                </label>
                                <select id="platform" name="platform" required>
                                    <option value="">Pilih Platform</option>
                                    <option value="PC" <?= (isset($_POST['platform']) && $_POST['platform'] == 'PC') ? 'selected' : '' ?>>PC</option>
                                    <option value="PlayStation 5" <?= (isset($_POST['platform']) && $_POST['platform'] == 'PlayStation 5') ? 'selected' : '' ?>>PlayStation 5</option>
                                    <option value="PlayStation 4" <?= (isset($_POST['platform']) && $_POST['platform'] == 'PlayStation 4') ? 'selected' : '' ?>>PlayStation 4</option>
                                    <option value="Xbox Series X/S" <?= (isset($_POST['platform']) && $_POST['platform'] == 'Xbox Series X/S') ? 'selected' : '' ?>>Xbox Series X/S</option>
                                    <option value="Xbox One" <?= (isset($_POST['platform']) && $_POST['platform'] == 'Xbox One') ? 'selected' : '' ?>>Xbox One</option>
                                    <option value="Nintendo Switch" <?= (isset($_POST['platform']) && $_POST['platform'] == 'Nintendo Switch') ? 'selected' : '' ?>>Nintendo Switch</option>
                                    <option value="Mobile" <?= (isset($_POST['platform']) && $_POST['platform'] == 'Mobile') ? 'selected' : '' ?>>Mobile</option>
                                    <option value="Multi-Platform" <?= (isset($_POST['platform']) && $_POST['platform'] == 'Multi-Platform') ? 'selected' : '' ?>>Multi-Platform</option>
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
                                           value="<?= isset($_POST['rating']) ? $_POST['rating'] : '' ?>"
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
                                       value="<?= isset($_POST['release_date']) ? $_POST['release_date'] : '' ?>"
                                       required>
                            </div>
                        </div>

                        <div class="form-group file-upload-group">
                            <label for="cover">
                                <i class="fas fa-image"></i> Cover Game
                            </label>
                            <div class="file-upload-container">
                                <input type="file" 
                                       id="cover" 
                                       name="cover" 
                                       accept="image/*"
                                       class="file-input">
                                <div class="file-upload-area" id="fileUploadArea">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Klik untuk memilih gambar atau drag & drop</p>
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
                                <i class="fas fa-save"></i> Simpan Game
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // File upload functionality
        const fileInput = document.getElementById('cover');
        const fileUploadArea = document.getElementById('fileUploadArea');
        const filePreview = document.getElementById('filePreview');
        const previewImage = document.getElementById('previewImage');

        // Click to select file
        fileUploadArea.addEventListener('click', () => fileInput.click());

        // Drag and drop
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

        // File input change
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

        // Form validation
        document.getElementById('gameForm').addEventListener('submit', function(e) {
            const rating = parseFloat(document.getElementById('rating').value);
            if (rating < 0 || rating > 10) {
                e.preventDefault();
                alert('Rating harus antara 0 dan 10');
                return false;
            }
        });

        // Auto-redirect countdown
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