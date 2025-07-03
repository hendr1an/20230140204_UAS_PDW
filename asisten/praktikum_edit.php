<?php
$pageTitle = 'Edit Praktikum';
$activePage = 'praktikum';
require_once 'templates/header.php';
require_once '../config.php';

$kode = $nama = $deskripsi = "";
$id = 0;

// Bagian 1: Proses saat form disubmit (method POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_to_update = $_POST['id'];
    $kode = $_POST['kode_praktikum'];
    $nama = $_POST['nama_praktikum'];
    $deskripsi = $_POST['deskripsi'];

    $sql = "UPDATE mata_praktikum SET kode_praktikum = ?, nama_praktikum = ?, deskripsi = ? WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssi", $kode, $nama, $deskripsi, $id_to_update);
        if ($stmt->execute()) {
            header("location: kelola_praktikum.php");
            exit();
        } else {
            echo "Terjadi kesalahan saat update.";
        }
        $stmt->close();
    }
} 
// Bagian 2: Proses saat halaman diakses (method GET)
else {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM mata_praktikum WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    $kode = $row['kode_praktikum'];
                    $nama = $row['nama_praktikum'];
                    $deskripsi = $row['deskripsi'];
                } else {
                    echo "Data tidak ditemukan.";
                    exit();
                }
            }
            $stmt->close();
        }
    }
}
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Edit Mata Praktikum</h2>
    <form action="praktikum_edit.php" method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <div class="mb-4">
            <label for="kode_praktikum" class="block text-gray-700">Kode Praktikum</label>
            <input type="text" name="kode_praktikum" id="kode_praktikum" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($kode); ?>" required>
        </div>
        <div class="mb-4">
            <label for="nama_praktikum" class="block text-gray-700">Nama Praktikum</label>
            <input type="text" name="nama_praktikum" id="nama_praktikum" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($nama); ?>" required>
        </div>
        <div class="mb-4">
            <label for="deskripsi" class="block text-gray-700">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="4" class="w-full px-3 py-2 border rounded-lg"><?php echo htmlspecialchars($deskripsi); ?></textarea>
        </div>
        <div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Update</button>
            <a href="kelola_praktikum.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Batal</a>
        </div>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>