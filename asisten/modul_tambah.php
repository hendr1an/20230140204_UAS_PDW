<?php
$pageTitle = 'Tambah Modul';
$activePage = 'modul';
require_once 'templates/header.php';
require_once '../config.php';

// Ambil daftar praktikum untuk dropdown
$praktikum_list = $conn->query("SELECT id, nama_praktikum FROM mata_praktikum");

$message = '';
// Cek jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $praktikum_id = $_POST['mata_praktikum_id'];
    $judul_modul = $_POST['judul_modul'];
    $deskripsi = $_POST['deskripsi'];
    $file_path = null;

    // Logika File Upload
    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] == 0) {
        $target_dir = "../uploads/materi/";
        // Buat nama file unik untuk menghindari menimpa file yang ada
        $file_name = time() . '_' . basename($_FILES["file_materi"]["name"]);
        $target_file = $target_dir . $file_name;
        
        // Pindahkan file dari temporary location ke target directory
        if (move_uploaded_file($_FILES["file_materi"]["tmp_name"], $target_file)) {
            $file_path = "uploads/materi/" . $file_name; // Path yang disimpan ke database
        } else {
            $message = "Gagal mengunggah file.";
        }
    }

    if(empty($message)) {
        $sql = "INSERT INTO modul (mata_praktikum_id, judul_modul, deskripsi, file_materi) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("isss", $praktikum_id, $judul_modul, $deskripsi, $file_path);
            if ($stmt->execute()) {
                header("location: kelola_modul.php");
                exit();
            } else {
                 $message = "Gagal menyimpan data ke database.";
            }
            $stmt->close();
        }
    }
}
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Tambah Modul Baru</h2>
    <?php if(!empty($message)) echo '<p class="text-red-500">'.$message.'</p>'; ?>
    
    <form action="modul_tambah.php" method="post" enctype="multipart/form-data">
        <div class="mb-4">
            <label for="mata_praktikum_id" class="block text-gray-700">Pilih Mata Praktikum</label>
            <select name="mata_praktikum_id" id="mata_praktikum_id" class="w-full px-3 py-2 border rounded-lg" required>
                <option value="">-- Pilih Praktikum --</option>
                <?php while($prak = $praktikum_list->fetch_assoc()): ?>
                    <option value="<?php echo $prak['id']; ?>"><?php echo htmlspecialchars($prak['nama_praktikum']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-4">
            <label for="judul_modul" class="block text-gray-700">Judul Modul</label>
            <input type="text" name="judul_modul" id="judul_modul" class="w-full px-3 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label for="deskripsi" class="block text-gray-700">Deskripsi</l abel>
            <textarea name="deskripsi" id="deskripsi" rows="4" class="w-full px-3 py-2 border rounded-lg"></textarea>
        </div>
        <div class="mb-4">
            <label for="file_materi" class="block text-gray-700">File Materi (PDF/DOCX)</label>
            <input type="file" name="file_materi" id="file_materi" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Simpan Modul</button>
            <a href="kelola_modul.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Batal</a>
        </div>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>