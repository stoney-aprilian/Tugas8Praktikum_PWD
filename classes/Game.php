<?php
// classes/Database.php
class Database {
    private $host = "localhost";
    private $db_name = "db_game";
    private $username = "root";
    private $password = "";
    public $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}",
                                  $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        return $this->conn;
    }
}

// classes/Game.php
class Game {
    private $conn;
    private $table = "games";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Mendapatkan semua games
    public function getAllGames() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mendapatkan game berdasarkan ID
    public function getGameById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Menambah game baru
    public function addGame($nama, $genre, $developer, $platform, $rating, $release_date, $cover = null) {
        $query = "INSERT INTO " . $this->table . " 
                (nama_game, genre, developer, platform, rating, release_date, cover_url)
                VALUES (:nama, :genre, :developer, :platform, :rating, :release_date, :cover)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':developer', $developer);
        $stmt->bindParam(':platform', $platform);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':release_date', $release_date);
        $stmt->bindParam(':cover', $cover);

        return $stmt->execute();
    }

    // Update game
    public function updateGame($id, $nama, $genre, $developer, $platform, $rating, $release_date, $cover = null) {
        if ($cover) {
            $query = "UPDATE " . $this->table . " 
                    SET nama_game = :nama, genre = :genre, developer = :developer, 
                        platform = :platform, rating = :rating, release_date = :release_date, 
                        cover_url = :cover 
                    WHERE id = :id";
        } else {
            $query = "UPDATE " . $this->table . " 
                    SET nama_game = :nama, genre = :genre, developer = :developer, 
                        platform = :platform, rating = :rating, release_date = :release_date 
                    WHERE id = :id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':developer', $developer);
        $stmt->bindParam(':platform', $platform);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':release_date', $release_date);
        
        if ($cover) {
            $stmt->bindParam(':cover', $cover);
        }

        return $stmt->execute();
    }

    // Hapus game
    public function deleteGame($id) {
        // Hapus file cover jika ada
        $game = $this->getGameById($id);
        if ($game && $game['cover_url'] && file_exists("uploads/" . $game['cover_url'])) {
            unlink("uploads/" . $game['cover_url']);
        }

        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Validasi input
    public function validateInput($data) {
        $errors = [];

        // Validasi judul
        if (empty(trim($data['judul']))) {
            $errors[] = "Judul game tidak boleh kosong";
        } elseif (strlen(trim($data['judul'])) > 255) {
            $errors[] = "Judul game terlalu panjang (maksimal 255 karakter)";
        }

        // Validasi genre
        if (empty($data['genre'])) {
            $errors[] = "Genre harus dipilih";
        }

        // Validasi developer
        if (empty(trim($data['developer']))) {
            $errors[] = "Developer tidak boleh kosong";
        } elseif (strlen(trim($data['developer'])) > 255) {
            $errors[] = "Nama developer terlalu panjang (maksimal 255 karakter)";
        }

        // Validasi platform
        if (empty($data['platform'])) {
            $errors[] = "Platform harus dipilih";
        }

        // Validasi rating
        if (empty($data['rating']) || !is_numeric($data['rating'])) {
            $errors[] = "Rating harus diisi dengan angka";
        } elseif ($data['rating'] < 0 || $data['rating'] > 10) {
            $errors[] = "Rating harus antara 0 dan 10";
        }

        // Validasi tanggal rilis
        if (empty($data['release_date'])) {
            $errors[] = "Tanggal rilis harus diisi";
        } elseif (!strtotime($data['release_date'])) {
            $errors[] = "Format tanggal rilis tidak valid";
        }

        return $errors;
    }

    // Handle file upload
    public function handleFileUpload($file) {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return null; // Tidak ada file yang diupload
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error uploading file");
        }

        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Format file tidak didukung. Gunakan JPG, PNG, atau GIF");
        }

        if ($file['size'] > $maxSize) {
            throw new Exception("Ukuran file terlalu besar. Maksimal 2MB");
        }

        // Create uploads directory if not exists
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $destination = 'uploads/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        } else {
            throw new Exception("Gagal menyimpan file");
        }
    }
}
?>