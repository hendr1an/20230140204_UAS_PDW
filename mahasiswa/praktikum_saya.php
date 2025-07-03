<?php
$pageTitle = 'Praktikum Saya';
$activePage = 'my_courses'; // Untuk menandai link aktif di header
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

$mahasiswa_id = $_SESSION['user_id'];

// Ambil daftar praktikum yang diikuti oleh mahasiswa yang sedang login
$sql = "SELECT mp.id, mp.kode_praktikum, mp.nama_praktikum, mp.deskripsi 
        FROM mata_praktikum mp
        JOIN pendaftaran_praktikum pp ON mp.id = pp.mata_praktikum_id
        WHERE pp.mahasiswa_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $mahasiswa_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6">Praktikum yang Anda Ikuti</h2>
    <div class="space-y-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while($praktikum = $result->fetch_assoc()): ?>
                <div class="border border-gray-200 p-4 rounded-lg">
                    <h3 class="font-bold text-lg text-blue-700"><?php echo htmlspecialchars($praktikum['nama_praktikum']); ?></h3>
                    <p class="text-gray-600 text-sm mb-3"><?php echo htmlspecialchars($praktikum['deskripsi']); ?></p>
                    <a href="praktikum_detail.php?id=<?php echo $praktikum['id']; ?>" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-3 rounded-md text-sm">
                        Lihat Detail & Tugas →
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-500">Anda belum terdaftar di mata praktikum manapun. Silakan cari praktikum di halaman "Cari Praktikum".</p>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'templates/footer_mahasiswa.php';
?>