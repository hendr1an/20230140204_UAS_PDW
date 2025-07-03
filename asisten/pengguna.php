<?php
$pageTitle = 'Kelola Pengguna';
$activePage = 'pengguna';
require_once 'templates/header.php';
require_once '../config.php';

$result = $conn->query("SELECT id, nama, email, role FROM users ORDER BY created_at DESC");
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Daftar Akun Pengguna</h2>
    <a href="pengguna_tambah.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg mb-4 inline-block">
        + Tambah Pengguna Baru
    </a>

    <table class="min-w-full bg-white">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Nama</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Email</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm">Role</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($row['email']); ?></td>
                    <td class="py-3 px-4 text-center">
                        <span class="capitalize py-1 px-3 rounded-full text-xs <?php echo $row['role'] == 'asisten' ? 'bg-green-200 text-green-800' : 'bg-blue-200 text-blue-800'; ?>">
                            <?php echo htmlspecialchars($row['role']); ?>
                        </span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <a href="pengguna_edit.php?id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                        <?php if ($_SESSION['user_id'] != $row['id']): // Cegah admin menghapus diri sendiri ?>
                        <a href="pengguna_hapus.php?id=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-700 ml-2" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'templates/footer.php'; ?>