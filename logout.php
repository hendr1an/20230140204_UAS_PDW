<?php
session_start();

// Hapus semua variabel session
$_SESSION = array();

// Hancurkan session
session_destroy();

// Redirect ke halaman login
// Kode Seharusnya (Benar)
// Kode Seharusnya (Benar)
header("Location: index.php");
exit;
?>