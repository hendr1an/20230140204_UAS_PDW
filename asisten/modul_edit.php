<?php
$pageTitle = 'Edit Modul';
$activePage = 'modul';
require_once 'templates/header.php';
require_once '../config.php';

$id_modul = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
$message = '';

// Proses UPDATE saat form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $praktikum_id = $_POST['mata_praktikum_id'];
    $judul_modul = $_POST['judul_modul'];
    $deskripsi = $_POST['deskripsi'];
    $file_path = $_POST['current_file']; // Ambil path file yang lama

    // Cek jika ada file baru yang diunggah
    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] == 0) {
        // Hapus file lama jika ada
        if (!empty($file_path) && file_exists('../' . $file_path)) {
            unlink('../' . $file_path);
        }
        
        // Proses upload file baru
        $target_dir = "../uploads/materi/";
        $file_name = time() . '_' . basename($_FILES["file_materi"]["name"]);
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["file_materi"]["tmp_name"], $target_file)) {
            $file_path = "uploads/materi/" . $file_name;
        } else {
            $message = "Gagal mengunggah file baru.";
        }
    }

    if (empty($message)) {
        $sql = "UPDATE modul SET mata_praktikum_id=?, judul_modul=?, deskripsi=?, file_materi=? WHERE id=?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("isssi", $praktikum_id, $judul_modul, $deskripsi, $file_path, $id_modul);
            if ($stmt->execute()) {
                header("location: kelola_modul.php");
                exit();
            } else {
                $message = "Gagal mengupdate data.";
            }
            $stmt->close();
        }
    }
}

// Ambil data modul yang akan diedit
$modul = null;
$sql_get = "SELECT * FROM modul WHERE id = ?";
if ($stmt_get = $conn->prepare($sql_get)) {
    $stmt_get->bind_param("i", $id_modul);
    $stmt_get->execute();
    $result = $stmt_get->get_result();
    $modul = $result->fetch_assoc();
    $stmt_get->close();
}

// Ambil daftar praktikum untuk dropdown
$praktikum_list = $conn->query("SELECT id, nama_praktikum FROM mata_praktikum");
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Edit Modul</h2>
    <?php if(!empty($message)) echo '<p class="text-red-500">'.$message.'</p>'; ?>
    
    <form action="modul_edit.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $modul['id']; ?>">
        <input type="hidden" name="current_file" value="<?php echo $modul['file_materi']; ?>">

        <div class="mb-4">
            <label for="mata_praktikum_id" class="block text-gray-700">Pilih Mata Praktikum</label>
            <select name="mata_praktikum_id" id="mata_praktikum_id" class="w-full px-3 py-2 border rounded-lg" required>
                <?php while($prak = $praktikum_list->fetch_assoc()): ?>
                    <option value="<?php echo $prak['id']; ?>" <?php echo ($prak['id'] == $modul['mata_praktikum_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($prak['nama_praktikum']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-4">
            <label for="judul_modul" class="block text-gray-700">Judul Modul</label>
            <input type="text" name="judul_modul" id="judul_modul" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($modul['judul_modul']); ?>" required>
        </div>
        <div class="mb-4">
            <label for="deskripsi" class="block text-gray-700">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="4" class="w-full px-3 py-2 border rounded-lg"><?php echo htmlspecialchars($modul['deskripsi']); ?></textarea>
        </div>
        <div class="mb-4">
            <label for="file_materi" class="block text-gray-700">Ganti File Materi (Opsional)</label>
            <?php if(!empty($modul['file_materi'])): ?>
                <p class="text-sm text-gray-500 mb-2">File saat ini: <a href="../<?php echo $modul['file_materi']; ?>" target="_blank" class="text-blue-500"><?php echo basename($modul['file_materi']); ?></a></p>
            <?php endif; ?>
            <input type="file" name="file_materi" id="file_materi" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Update Modul</button>
            <a href="kelola_modul.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Batal</a>
        </div>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>