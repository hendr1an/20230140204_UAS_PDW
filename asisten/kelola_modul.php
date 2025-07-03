<?php
$pageTitle = 'Kelola Modul';
$activePage = 'modul'; // Untuk menandai link aktif di header
require_once 'templates/header.php';
require_once '../config.php';

// Ambil semua data modul dengan nama praktikumnya menggunakan JOIN
$sql = "SELECT m.id, m.judul_modul, mp.nama_praktikum, m.file_materi
        FROM modul m
        JOIN mata_praktikum mp ON m.mata_praktikum_id = mp.id
        ORDER BY mp.nama_praktikum, m.id";
$result = $conn->query($sql);
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Daftar Modul Praktikum</h2>
    <a href="modul_tambah.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg mb-4 inline-block">
        + Tambah Modul Baru
    </a>

    <table class="min-w-full bg-white">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Judul Modul</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Mata Praktikum</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm">File Materi</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($row['judul_modul']); ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($row['nama_praktikum']); ?></td>
                    <td class="py-3 px-4 text-center">
                        <?php if(!empty($row['file_materi'])): ?>
                            <a href="../<?php echo htmlspecialchars($row['file_materi']); ?>" target="_blank" class="text-blue-500 hover:underline">Lihat</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <a href="modul_edit.php?id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                        <a href="modul_hapus.php?id=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-700 ml-2" onclick="return confirm('Yakin ingin menghapus modul ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center py-4">Belum ada modul yang ditambahkan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once 'templates/footer.php';
?>