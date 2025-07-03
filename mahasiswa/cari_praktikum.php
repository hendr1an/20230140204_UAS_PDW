<?php
$pageTitle = 'Cari Praktikum';
$activePage = 'courses'; // Sesuaikan dengan variabel di header Anda
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

$mahasiswa_id = $_SESSION['user_id'];
$message = '';

// Logika untuk mendaftar praktikum
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['daftar'])) {
    $praktikum_id = $_POST['praktikum_id'];

    // 1. Cek dulu apakah mahasiswa sudah terdaftar di praktikum ini
    $sql_check = "SELECT id FROM pendaftaran_praktikum WHERE mahasiswa_id = ? AND mata_praktikum_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $mahasiswa_id, $praktikum_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $message = '<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert"><p>Anda sudah terdaftar di mata praktikum ini.</p></div>';
    } else {
        // 2. Jika belum, masukkan data pendaftaran baru
        $sql_insert = "INSERT INTO pendaftaran_praktikum (mahasiswa_id, mata_praktikum_id) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ii", $mahasiswa_id, $praktikum_id);
        if ($stmt_insert->execute()) {
            $message = '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert"><p>Pendaftaran berhasil!</p></div>';
        } else {
            $message = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert"><p>Terjadi kesalahan. Gagal mendaftar.</p></div>';
        }
        $stmt_insert->close();
    }
    $stmt_check->close();
}

// Ambil semua data mata praktikum yang tersedia
$result = $conn->query("SELECT * FROM mata_praktikum ORDER BY kode_praktikum ASC");
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Katalog Mata Praktikum</h2>
    <p class="text-gray-600 mb-6">Pilih mata praktikum yang ingin Anda ikuti dan klik tombol "Daftar".</p>
    
    <?php echo $message; // Tampilkan pesan notifikasi di sini ?>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-4 uppercase font-semibold text-sm">Kode</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Nama Praktikum</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Deskripsi</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="py-3 px-4 text-center font-semibold"><?php echo htmlspecialchars($row['kode_praktikum']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['nama_praktikum']); ?></td>
                        <td class="py-3 px-4 text-sm"><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                        <td class="py-3 px-4 text-center">
                            <form action="cari_praktikum.php" method="post">
                                <input type="hidden" name="praktikum_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="daftar" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                                    Daftar
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-4">Saat ini belum ada mata praktikum yang tersedia.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'templates/footer_mahasiswa.php';
?>