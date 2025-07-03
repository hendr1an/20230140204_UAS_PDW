<?php
session_start(); // Mulai session di paling atas
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../index.php");
    exit();
}

require_once '../config.php';

$id_laporan = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
$asisten_id = $_SESSION['user_id'];
$message = '';

// SEMUA PROSES FORM (POST) DI ATAS SINI, SEBELUM HTML APAPUN
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nilai = $_POST['nilai'];
    $feedback = $_POST['feedback'];

    $sql = "UPDATE pengumpulan_laporan SET nilai = ?, feedback = ?, dinilai_oleh = ? WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        // Gunakan "d" untuk tipe data decimal/double
        $stmt->bind_param("dsii", $nilai, $feedback, $asisten_id, $id_laporan);
        
        // Perintah redirect yang tadinya error, sekarang akan berhasil
        if ($stmt->execute()) {
            header("location: laporan.php");
            exit();
        } else {
            $message = "Gagal menyimpan nilai.";
        }
        $stmt->close();
    }
}

// SETELAH SEMUA PROSES PHP SELESAI, BARU KITA SIAPKAN UNTUK TAMPILAN
$pageTitle = 'Beri Nilai Laporan';
$activePage = 'laporan';
require_once 'templates/header.php'; // Panggil header di sini

// Ambil data laporan untuk ditampilkan di form
$sql_get = "SELECT pl.id, u.nama, m.judul_modul, pl.file_laporan, pl.nilai, pl.feedback 
            FROM pengumpulan_laporan pl 
            JOIN users u ON pl.mahasiswa_id = u.id 
            JOIN modul m ON pl.modul_id = m.id 
            WHERE pl.id = ?";
$stmt_get = $conn->prepare($sql_get);
$stmt_get->bind_param("i", $id_laporan);
$stmt_get->execute();
$laporan = $stmt_get->get_result()->fetch_assoc();
?>

<div class="bg-white p-6 rounded-lg shadow-md max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold mb-1">Penilaian Laporan</h2>
    <p class="text-gray-600 mb-4">Beri nilai dan feedback untuk laporan ini.</p>
    
    <?php if(!empty($message)) echo '<p class="text-red-500 mb-4">'.$message.'</p>'; ?>

    <div class="mb-4 border-t pt-4">
        <p><strong class="w-24 inline-block">Mahasiswa</strong>: <?php echo htmlspecialchars($laporan['nama']); ?></p>
        <p><strong class="w-24 inline-block">Modul</strong>: <?php echo htmlspecialchars($laporan['judul_modul']); ?></p>
        <p><strong class="w-24 inline-block">File Laporan</strong>: 
            <a href="../<?php echo htmlspecialchars($laporan['file_laporan']); ?>" target="_blank" class="text-blue-500 hover:underline">
                Unduh & Lihat Laporan
            </a>
        </p>
    </div>

    <form action="penilaian.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $laporan['id']; ?>">
        <div class="mb-4">
            <label for="nilai" class="block text-gray-700 font-semibold">Nilai (0-100)</label>
            <input type="number" step="0.01" min="0" max="100" name="nilai" id="nilai" value="<?php echo htmlspecialchars($laporan['nilai']); ?>" class="w-full px-3 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label for="feedback" class="block text-gray-700 font-semibold">Feedback (Opsional)</label>
            <textarea name="feedback" id="feedback" rows="5" class="w-full px-3 py-2 border rounded-lg"><?php echo htmlspecialchars($laporan['feedback']); ?></textarea>
        </div>
        <div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Simpan Nilai</button>
            <a href="laporan.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Kembali</a>
        </div>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>