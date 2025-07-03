<?php
$pageTitle = 'Kelola Praktikum';
$activePage = 'praktikum'; // Untuk menandai link aktif di header
require_once 'templates/header.php';
require_once '../config.php';

// Ambil semua data dari tabel mata_praktikum
$result = $conn->query("SELECT * FROM mata_praktikum ORDER BY id DESC");
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Daftar Mata Praktikum</h2>
    <a href="praktikum_tambah.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg mb-4 inline-block">
        + Tambah Praktikum Baru
    </a>

    <table class="min-w-full bg-white">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="py-3 px-4 uppercase font-semibold text-sm">Kode</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm">Nama Praktikum</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm">Deskripsi</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($row['kode_praktikum']); ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($row['nama_praktikum']); ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                    <td class="py-3 px-4">
                        <a href="praktikum_edit.php?id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                        <a href="praktikum_hapus.php?id=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-700 ml-2" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center py-4">Belum ada data mata praktikum.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once 'templates/footer.php';
?>