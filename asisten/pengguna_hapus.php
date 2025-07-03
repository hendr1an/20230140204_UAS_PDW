<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}
require_once '../config.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_to_delete = $_GET['id'];
    
    // Mencegah admin menghapus akunnya sendiri
    if ($id_to_delete == $_SESSION['user_id']) {
        // Redirect dengan pesan error atau langsung kembali
        header("location: pengguna.php?error=self_delete");
        exit();
    }
    
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_to_delete);
    $stmt->execute();
}

header("location: pengguna.php");
exit();
?>