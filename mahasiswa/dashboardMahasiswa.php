<?php

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once 'templates/header_mahasiswa.php'; // Sudah ada
require_once '../config.php'; // Tambahkan ini

// Ambil ID mahasiswa dari session
$mahasiswa_id = $_SESSION['user_id'];

// --- MULAI LOGIKA PENGAMBILAN DATA ---

// 1. Hitung praktikum yang diikuti
$stmt_praktikum = $conn->prepare("SELECT COUNT(id) as total FROM pendaftaran_praktikum WHERE mahasiswa_id = ?");
$stmt_praktikum->bind_param("i", $mahasiswa_id);
$stmt_praktikum->execute();
$result_praktikum = $stmt_praktikum->get_result()->fetch_assoc();
$total_praktikum = $result_praktikum['total'];

// 2. Hitung tugas selesai (sudah dinilai)
$stmt_selesai = $conn->prepare("SELECT COUNT(id) as total FROM pengumpulan_laporan WHERE mahasiswa_id = ? AND nilai IS NOT NULL");
$stmt_selesai->bind_param("i", $mahasiswa_id);
$stmt_selesai->execute();
$result_selesai = $stmt_selesai->get_result()->fetch_assoc();
$tugas_selesai = $result_selesai['total'];

// 3. Hitung tugas menunggu (sudah dikumpul, belum dinilai)
$stmt_menunggu = $conn->prepare("SELECT COUNT(id) as total FROM pengumpulan_laporan WHERE mahasiswa_id = ? AND nilai IS NULL");
$stmt_menunggu->bind_param("i", $mahasiswa_id);
$stmt_menunggu->execute();
$result_menunggu = $stmt_menunggu->get_result()->fetch_assoc();
$tugas_menunggu = $result_menunggu['total'];

// 4. Ambil notifikasi terbaru (contoh: 3 laporan terakhir)
$sql_notif = "SELECT pl.id, m.judul_modul, pl.nilai, mp.nama_praktikum
              FROM pengumpulan_laporan pl
              JOIN modul m ON pl.modul_id = m.id
              JOIN mata_praktikum mp ON m.mata_praktikum_id = mp.id
              WHERE pl.mahasiswa_id = ? ORDER BY pl.tanggal_pengumpulan DESC LIMIT 3";
$stmt_notif = $conn->prepare($sql_notif);
$stmt_notif->bind_param("i", $mahasiswa_id);
$stmt_notif->execute();
$notifikasi = $stmt_notif->get_result();


// --- AKHIR LOGIKA PENGAMBILAN DATA ---

?>


<div class="bg-gradient-to-r from-blue-500 to-cyan-400 text-white p-8 rounded-xl shadow-lg mb-8">
    <h1 class="text-3xl font-bold">Selamat Datang Kembali, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h1>
    <p class="mt-2 opacity-90">Terus semangat dalam menyelesaikan semua modul praktikummu.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-blue-600"><?php echo $total_praktikum; ?></div>
        <div class="mt-2 text-lg text-gray-600">Praktikum Diikuti</div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-green-500"><?php echo $tugas_selesai; ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Selesai</div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-yellow-500"><?php echo $tugas_menunggu; ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Menunggu</div>
    </div>
    
</div>

<div class="bg-white p-6 rounded-xl shadow-md">
    <h3 class="text-2xl font-bold text-gray-800 mb-4">Notifikasi Terbaru</h3>
    <ul class="space-y-4">
        
        <?php if ($notifikasi->num_rows > 0): ?>
            <?php while($notif = $notifikasi->fetch_assoc()): ?>
            <li class="flex items-start p-3 border-b border-gray-100 last:border-b-0">
                <?php if (!is_null($notif['nilai'])): ?>
                    <span class="text-xl mr-4">🔔</span>
                    <div>
                        Nilai untuk <a href="#" class="font-semibold text-blue-600 hover:underline"><?php echo htmlspecialchars($notif['judul_modul']); ?></a> telah diberikan. Nilai Anda: <?php echo htmlspecialchars($notif['nilai']); ?>
                    </div>
                <?php else: ?>
                    <span class="text-xl mr-4">✅</span>
                     <div>
                        Anda berhasil mengumpulkan laporan untuk <a href="#" class="font-semibold text-blue-600 hover:underline"><?php echo htmlspecialchars($notif['judul_modul']); ?></a>. Menunggu penilaian dari asisten.
                    </div>
                <?php endif; ?>
            </li>
            <?php endwhile; ?>
        <?php else: ?>
             <li class="flex items-start p-3">
                <span class="text-xl mr-4">🎉</span>
                <div>Belum ada aktivitas terbaru.</div>
            </li>
        <?php endif; ?>
        
    </ul>
</div>


<?php
// Panggil Footer
require_once 'templates/footer_mahasiswa.php';
?>