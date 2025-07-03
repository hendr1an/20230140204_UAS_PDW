<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}
require_once '../config.php';

// Pastikan ID ada di URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_modul = $_GET['id'];

    // 1. Ambil path file dari database sebelum menghapus record
    $sql_select = "SELECT file_materi FROM modul WHERE id = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        $stmt_select->bind_param("i", $id_modul);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        if ($row = $result->fetch_assoc()) {
            $file_path = $row['file_materi'];

            // 2. Hapus file fisik dari server jika ada
            if (!empty($file_path) && file_exists('../' . $file_path)) {
                unlink('../' . $file_path);
            }
        }
        $stmt_select->close();
    }

    // 3. Hapus record dari database
    $sql_delete = "DELETE FROM modul WHERE id = ?";
    if ($stmt_delete = $conn->prepare($sql_delete)) {
        $stmt_delete->bind_param("i", $id_modul);
        $stmt_delete->execute();
        $stmt_delete->close();
    }
}

// Kembali ke halaman daftar modul
header("location: kelola_modul.php");
exit();
?>