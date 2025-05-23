<?php
// delete.php
require_once 'classes/Game.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $game = new Game();
    $result = $game->deleteGame($id);
    
    if ($result) {
        header("Location: index.php?deleted=1");
    } else {
        header("Location: index.php?error=delete_failed");
    }
} else {
    header("Location: index.php?error=invalid_id");
}
exit;
?>