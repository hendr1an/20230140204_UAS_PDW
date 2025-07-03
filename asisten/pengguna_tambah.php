<?php
$pageTitle = 'Tambah Pengguna';
$activePage = 'pengguna';
require_once 'templates/header.php';
require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    // Enkripsi password untuk keamanan
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    $sql = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nama, $email, $password, $role);
    if ($stmt->execute()) {
        header("location: pengguna.php");
        exit();
    }
}
?>

<div class="bg-white p-6 rounded-lg shadow-md max-w-lg mx-auto">
    <h2 class="text-2xl font-bold mb-4">Tambah Pengguna Baru</h2>
    <form action="pengguna_tambah.php" method="post">
        <div class="mb-4">
            <label for="nama" class="block text-gray-700">Nama Lengkap</label>
            <input type="text" name="nama" id="nama" class="w-full px-3 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label for="email" class="block text-gray-700">Email</label>
            <input type="email" name="email" id="email" class="w-full px-3 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label for="password" class="block text-gray-700">Password</label>
            <input type="password" name="password" id="password" class="w-full px-3 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label for="role" class="block text-gray-700">Role</label>
            <select name="role" id="role" class="w-full px-3 py-2 border rounded-lg" required>
                <option value="mahasiswa">Mahasiswa</option>
                <option value="asisten">Asisten</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Simpan</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>