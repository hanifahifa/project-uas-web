<?php
session_start(); // Memulai sesi

include '../db.php';

// Pastikan pengguna sudah login dan memiliki peran 'berqurban'
// if (!isset($_SESSION['nik']) || $_SESSION['role'] != 'berqurban') {
//     header('Location: ../login.php');
//     exit();
// }

$nik = $_SESSION['nik'];// Mendapatkan NIK dari session

// Membuat koneksi ke MySQL
$conn = mysqli_connect($host, $username, $password, $dbname);

// Mengecek apakah koneksi berhasil
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ambil data user berdasarkan nik
$query = "SELECT * FROM users WHERE nik = '$nik'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Pastikan data pengguna ditemukan
if (!$user) {
    die("Pengguna tidak ditemukan!");
}

// Ambil data hewan qurban berdasarkan sumber (NIK user)
$query_hewan = "SELECT * FROM hewan_qurban WHERE sumber = '$nik'";
$result_hewan = mysqli_query($conn, $query_hewan);
$data_hewan = mysqli_fetch_assoc($result_hewan);

// Ambil data pembagian daging
$query_daging = "SELECT * FROM pembagian_daging WHERE nik = '$nik'";
$result_daging = mysqli_query($conn, $query_daging);
$data_daging = mysqli_fetch_assoc($result_daging);

// Set default values jika data tidak ditemukan
$jenis_hewan = $data_hewan ? ucfirst($data_hewan['jenis']) : 'Tidak ada data';
$jumlah_hewan = $data_hewan ? $data_hewan['jumlah'] : 0;
$harga_total = $data_hewan ? ($data_hewan['harga_per_ekor'] + $data_hewan['biaya_admin_per_ekor']) * $data_hewan['jumlah'] : 0;
$status_pengambilan = $data_daging ? ($data_daging['status_pengambilan'] == 'sudah' ? 'Sudah Diambil' : 'Belum Diambil') : 'Belum Diambil';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Berqurban - QURBANA</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Styling remains the same as the previous code */
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="welcome-text">
                <h1>Dashboard Berqurban</h1>
                <p>Selamat datang, <?php echo htmlspecialchars($user['name'] ?? 'Nama Tidak Ditemukan'); ?></p>
            </div>
            <a href="../logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>

        <!-- Data Diri Pengguna -->
<div class="user-info">
    <h4>
        <div class="icon">
            <i class="fas fa-user"></i>
        </div>
        Informasi Pribadi
    </h4>
    <div class="user-detail">
        <span class="user-label">Nama Lengkap</span>
        <span class="user-value"><?php echo htmlspecialchars($user['name'] ?? 'Data tidak ditemukan'); ?></span>
    </div>
    <div class="user-detail">
        <span class="user-label">NIK</span>
        <span class="user-value"><?php echo htmlspecialchars($user['nik'] ?? 'Data tidak ditemukan'); ?></span>
    </div>
    <div class="user-detail">
        <span class="user-label">Alamat</span>
        <span class="user-value"><?php echo htmlspecialchars($user['alamat'] ?? 'Data tidak ditemukan'); ?></span>
    </div>
</div>

        <!-- Dashboard Cards -->
        <div class="dashboard-grid">
            <!-- Informasi Qurban -->
            <div class="dashboard-card card-primary">
                <h5>
                    <div class="icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    Informasi Qurban Anda
                </h5>
                <div class="info-row">
                    <span class="info-label">Jenis Hewan:</span>
                    <span class="info-value"><?php echo htmlspecialchars($jenis_hewan); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Jumlah Hewan:</span>
                    <span class="info-value"><?php echo htmlspecialchars($jumlah_hewan); ?> ekor</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Pembayaran:</span>
                    <span class="price-display">Rp <?php echo number_format($harga_total, 0, ',', '.'); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status Pembayaran:</span>
                    <span class="status-badge badge-success">Sudah Dibayar</span>
                </div>
            </div>

            <!-- QR Code -->
            <div class="dashboard-card card-info">
                <h5>
                    <div class="icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    QR Code Pengambilan Daging
                </h5>
                <div class="qr-container">
                    <?php
                    $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode("NIK: " . $user['nik'] . " - " . $user['name']);
                    ?>
                    <img src="<?php echo $qr_url; ?>" alt="QR Code" width="180">
                    <p class="qr-description">Scan QR Code ini saat pengambilan daging qurban</p>
                </div>
            </div>

            <!-- Status Pengambilan Daging -->
            <div class="dashboard-card card-warning">
                <h5>
                    <div class="icon">
                        <i class="fas fa-drumstick-bite"></i>
                    </div>
                    Status Pengambilan Daging
                </h5>
                <div class="info-row">
                    <span class="info-label">Status Pengambilan:</span>
                    <span class="status-badge <?php echo ($status_pengambilan == 'Sudah Diambil') ? 'badge-success' : 'badge-warning'; ?>">
                        <?php echo $status_pengambilan; ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Estimasi Waktu:</span>
                    <span class="info-value">Setelah pemotongan selesai</span>
                </div>
                <?php if($data_daging): ?>
                <div class="info-row">
                    <span class="info-label">Jumlah Daging:</span>
                    <span class="info-value"><?php echo $data_daging['jumlah_kg']; ?> Kg</span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Information Section - Moved to bottom like in warga dashboard -->
        <div class="info-section">
            <h3>
                <div class="icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                Informasi Penting
            </h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="alert-modern">
                        <h6><i class="fas fa-clipboard-list"></i> Pengambilan Daging:</h6>
                        <p><strong>Pengambilan Daging:</strong><br>
                        Daging dapat diambil setelah proses pemotongan selesai. Silakan bawa QR Code ini untuk memudahkan verifikasi.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert-modern">
                        <h6><i class="fas fa-heart"></i> Terima Kasih:</h6>
                        <p><strong>Terima Kasih:</strong><br>
                        Jazakallahu khair atas partisipasi Anda dalam ibadah qurban tahun ini.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2025 QURBANA - Sistem Manajemen Qurban RT 001</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
