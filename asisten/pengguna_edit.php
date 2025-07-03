<?php
$pageTitle = 'Edit Pengguna';
$activePage = 'pengguna';
require_once 'templates/header.php';
require_once '../config.php';

$id_user = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    
    // Logika untuk update password (jika diisi)
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $sql = "UPDATE users SET nama=?, email=?, password=?, role=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nama, $email, $password, $role, $id_user);
    } else {
        // Jika password tidak diisi, jangan update password
        $sql = "UPDATE users SET nama=?, email=?, role=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nama, $email, $role, $id_user);
    }
    
    if ($stmt->execute()) {
        header("location: pengguna.php");
        exit();
    }
}

// Ambil data user yang akan diedit
$user = $conn->query("SELECT nama, email, role FROM users WHERE id = $id_user")->fetch_assoc();
?>

<div class="bg-white p-6 rounded-lg shadow-md max-w-lg mx-auto">
    <h2 class="text-2xl font-bold mb-4">Edit Pengguna</h2>
    <form action="pengguna_edit.php" method="post">
        <input type="hidden" name="id" value="<?php echo $id_user; ?>">
        <div class="mb-4">
            <label for="nama" class="block text-gray-700">Nama Lengkap</label>
            <input type="text" name="nama" id="nama" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
        </div>
        <div class="mb-4">
            <label for="email" class="block text-gray-700">Email</label>
            <input type="email" name="email" id="email" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-4">
            <label for="password" class="block text-gray-700">Password Baru</label>
            <input type="password" name="password" id="password" class="w-full px-3 py-2 border rounded-lg" placeholder="Kosongkan jika tidak ingin ganti">
        </div>
        <div class="mb-4">
            <label for="role" class="block text-gray-700">Role</label>
            <select name="role" id="role" class="w-full px-3 py-2 border rounded-lg" required>
                <option value="mahasiswa" <?php echo ($user['role'] == 'mahasiswa') ? 'selected' : ''; ?>>Mahasiswa</option>
                <option value="asisten" <?php echo ($user['role'] == 'asisten') ? 'selected' : ''; ?>>Asisten</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Update</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>