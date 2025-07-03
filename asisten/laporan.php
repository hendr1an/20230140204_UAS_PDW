<?php
$pageTitle = 'Laporan Masuk';
$activePage = 'laporan';
require_once 'templates/header.php';
require_once '../config.php';

// Logika Filter
$where_clauses = [];
$params = [];
$types = '';

if (!empty($_GET['praktikum_id'])) {
    $where_clauses[] = "mp.id = ?";
    $params[] = $_GET['praktikum_id'];
    $types .= 'i';
}
if (!empty($_GET['status'])) {
    if ($_GET['status'] == 'dinilai') {
        $where_clauses[] = "pl.nilai IS NOT NULL";
    } elseif ($_GET['status'] == 'belum_dinilai') {
        $where_clauses[] = "pl.nilai IS NULL";
    }
}

// Query dasar dengan JOIN
$sql = "SELECT pl.id, u.nama as nama_mahasiswa, mp.nama_praktikum, m.judul_modul, pl.tanggal_pengumpulan, pl.nilai
        FROM pengumpulan_laporan pl
        JOIN users u ON pl.mahasiswa_id = u.id
        JOIN modul m ON pl.modul_id = m.id
        JOIN mata_praktikum mp ON m.mata_praktikum_id = mp.id";

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}
$sql .= " ORDER BY pl.tanggal_pengumpulan DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Data untuk filter dropdown
$praktikum_list = $conn->query("SELECT id, nama_praktikum FROM mata_praktikum");
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Laporan Masuk</h2>

    <form action="laporan.php" method="GET" class="mb-6 bg-gray-50 p-4 rounded-lg flex items-end space-x-4">
        <div>
            <label for="praktikum_id" class="block text-sm font-medium text-gray-700">Filter Praktikum</label>
            <select name="praktikum_id" id="praktikum_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                <option value="">Semua Praktikum</option>
                <?php while($prak = $praktikum_list->fetch_assoc()): ?>
                    <option value="<?php echo $prak['id']; ?>" <?php echo (isset($_GET['praktikum_id']) && $_GET['praktikum_id'] == $prak['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($prak['nama_praktikum']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Filter Status</label>
            <select name="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                <option value="">Semua Status</option>
                <option value="dinilai" <?php echo (isset($_GET['status']) && $_GET['status'] == 'dinilai') ? 'selected' : ''; ?>>Sudah Dinilai</option>
                <option value="belum_dinilai" <?php echo (isset($_GET['status']) && $_GET['status'] == 'belum_dinilai') ? 'selected' : ''; ?>>Belum Dinilai</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Filter</button>
        <a href="laporan.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Reset</a>
    </form>

    <table class="min-w-full bg-white">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Mahasiswa</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Modul</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm">Tgl Kumpul</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm">Status</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></td>
                        <td class="py-3 px-4">
                            <span class="font-semibold"><?php echo htmlspecialchars($row['judul_modul']); ?></span><br>
                            <span class="text-xs text-gray-500"><?php echo htmlspecialchars($row['nama_praktikum']); ?></span>
                        </td>
                        <td class="py-3 px-4 text-center"><?php echo date('d M Y', strtotime($row['tanggal_pengumpulan'])); ?></td>
                        <td class="py-3 px-4 text-center">
                            <?php if(is_null($row['nilai'])): ?>
                                <span class="bg-yellow-200 text-yellow-800 py-1 px-3 rounded-full text-xs">Belum Dinilai</span>
                            <?php else: ?>
                                <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">Dinilai: <?php echo htmlspecialchars($row['nilai']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="penilaian.php?id=<?php echo $row['id']; ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                <?php echo is_null($row['nilai']) ? 'Beri Nilai' : 'Edit Nilai'; ?>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center py-4">Tidak ada laporan yang ditemukan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'templates/footer.php'; ?>