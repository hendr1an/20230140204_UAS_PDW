<?php
$pageTitle = 'Detail Praktikum';
$activePage = 'my_courses';
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

$mahasiswa_id = $_SESSION['user_id'];
$praktikum_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';

// Proses upload laporan
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kumpul_laporan'])) {
    $modul_id = $_POST['modul_id'];

    if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] == 0) {
        $target_dir = "../uploads/laporan/";
        $file_name = $mahasiswa_id . '_' . time() . '_' . basename($_FILES["file_laporan"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["file_laporan"]["tmp_name"], $target_file)) {
            $file_path = "uploads/laporan/" . $file_name;
            $sql = "INSERT INTO pengumpulan_laporan (modul_id, mahasiswa_id, file_laporan) VALUES (?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("iis", $modul_id, $mahasiswa_id, $file_path);
                $stmt->execute();
                $stmt->close();
                $message = '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert"><p>Laporan berhasil dikumpulkan!</p></div>';
            }
        }
    } else {
        $message = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert"><p>Gagal mengunggah file. Pastikan Anda memilih file.</p></div>';
    }
}

// Ambil info praktikum
$praktikum_info = $conn->query("SELECT nama_praktikum FROM mata_praktikum WHERE id = $praktikum_id")->fetch_assoc();

// Ambil daftar modul DAN status pengumpulannya dengan LEFT JOIN
$sql_modul = "SELECT m.id, m.judul_modul, m.deskripsi, m.file_materi, pl.file_laporan, pl.nilai
              FROM modul m
              LEFT JOIN pengumpulan_laporan pl ON m.id = pl.modul_id AND pl.mahasiswa_id = ?
              WHERE m.mata_praktikum_id = ?
              ORDER BY m.id";

$stmt = $conn->prepare($sql_modul);
$stmt->bind_param("ii", $mahasiswa_id, $praktikum_id);
$stmt->execute();
$result_modul = $stmt->get_result();

?>

<h2 class="text-3xl font-bold mb-4">Detail Praktikum: <?php echo htmlspecialchars($praktikum_info['nama_praktikum']); ?></h2>
<?php echo $message; ?>

<div class="space-y-6">
    <?php while($modul = $result_modul->fetch_assoc()): ?>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($modul['judul_modul']); ?></h3>
                <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($modul['deskripsi']); ?></p>
            </div>
            <?php if(!empty($modul['file_materi'])): ?>
            <a href="../<?php echo htmlspecialchars($modul['file_materi']); ?>" target="_blank" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg ml-4 flex-shrink-0">
                Unduh Materi
            </a>
            <?php endif; ?>
        </div>
        
        <hr class="my-4">

        <div>
            <h4 class="font-semibold text-gray-700">Status Laporan Anda:</h4>
            <?php if(!empty($modul['file_laporan'])): // Jika sudah mengumpulkan ?>
                <div class="bg-green-100 p-4 rounded-lg mt-2">
                    <p class="font-bold text-green-800">Telah Dikumpulkan</p>
                    <?php if(!is_null($modul['nilai'])): ?>
                        <p class="text-green-700 mt-1">Nilai: <span class="font-extrabold text-2xl"><?php echo htmlspecialchars($modul['nilai']); ?></span></p>
                    <?php else: ?>
                        <p class="text-yellow-700 mt-1">Status: Menunggu penilaian dari asisten.</p>
                    <?php endif; ?>
                </div>
            <?php else: // Jika belum mengumpulkan, tampilkan form upload ?>
                <form action="praktikum_detail.php?id=<?php echo $praktikum_id; ?>" method="post" enctype="multipart/form-data" class="mt-2">
                    <input type="hidden" name="modul_id" value="<?php echo $modul['id']; ?>">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                        <input type="file" name="file_laporan" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <button type="submit" name="kumpul_laporan" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg mt-3">
                            Kumpulkan Laporan
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php
require_once 'templates/footer_mahasiswa.php';
?>