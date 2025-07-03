<?php
$pageTitle = 'Tambah Praktikum';
$activePage = 'praktikum';
require_once 'templates/header.php';
require_once '../config.php';

// Cek jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode = $_POST['kode_praktikum'];
    $nama = $_POST['nama_praktikum'];
    $deskripsi = $_POST['deskripsi'];
    
    $sql = "INSERT INTO mata_praktikum (kode_praktikum, nama_praktikum, deskripsi) VALUES (?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $kode, $nama, $deskripsi);
        if ($stmt->execute()) {
            // Jika berhasil, redirect kembali ke halaman utama
            header("location: kelola_praktikum.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Tambah Praktikum Baru</h2>
    <form action="praktikum_tambah.php" method="post">
        <div class="mb-4">
            <label for="kode_praktikum" class="block text-gray-700">Kode Praktikum</label>
            <input type="text" name="kode_praktikum" id="kode_praktikum" class="w-full px-3 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label for="nama_praktikum" class="block text-gray-700">Nama Praktikum</label>
            <input type="text" name="nama_praktikum" id="nama_praktikum" class="w-full px-3 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label for="deskripsi" class="block text-gray-700">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="4" class="w-full px-3 py-2 border rounded-lg"></textarea>
        </div>
        <div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Simpan</button>
            <a href="kelola_praktikum.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Batal</a>
        </div>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>