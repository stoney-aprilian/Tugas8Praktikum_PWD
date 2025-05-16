<?php
class Game {
    private $conn;
    private $table = "games";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllGames() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    public function addGame($nama, $genre, $developer, $platform, $rating, $cover) {
        $query = "INSERT INTO " . $this->table . " 
                (nama_game, genre, developer, platform, rating, cover_url)
                VALUES (:nama, :genre, :developer, :platform, :rating, :cover)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':developer', $developer);
        $stmt->bindParam(':platform', $platform);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':cover', $cover);

        return $stmt->execute();
    }

}
