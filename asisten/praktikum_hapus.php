<?php
// Memulai session dan memastikan hanya asisten yang bisa akses
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

// Cek apakah ID ada di URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_to_delete = $_GET['id'];

    // Siapkan perintah DELETE
    $sql = "DELETE FROM mata_praktikum WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_to_delete);

        // Eksekusi perintah
        if ($stmt->execute()) {
            // Jika berhasil, kembali ke halaman daftar
            header("location: kelola_praktikum.php");
            exit();
        } else {
            echo "Terjadi kesalahan. Silakan coba lagi.";
        }
        $stmt->close();
    }
} else {
    // Jika tidak ada ID, kembali saja
    header("location: kelola_praktikum.php");
    exit();
}
?>