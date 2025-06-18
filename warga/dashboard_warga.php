<?php
session_start();
include '../db.php';

// Pastikan pengguna sudah login dan memiliki peran 'warga'
if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'warga') {
    header('Location: ../login.php');
    exit();
}

// Ambil nik dari session
$nik = $_SESSION['nik'];

// Membuat koneksi ke MySQL
$conn = mysqli_connect($host, $username, $password, $dbname);

// Mengecek apakah koneksi berhasil
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ambil data dari database sesuai hak akses Warga
$query = mysqli_prepare($conn, "SELECT * FROM users WHERE nik = ?");
mysqli_stmt_bind_param($query, 's', $nik); // Menggunakan $nik untuk bind parameter
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);
$user = mysqli_fetch_assoc($result);

// Pastikan data ditemukan sebelum digunakan
if ($user) {
    // Ambil data tambahan untuk Warga (misal, status qurban)
    $ambil_data_qurban_sql = "SELECT * FROM pembagian_daging WHERE nik = ?";
    $ambil_data_qurban = mysqli_prepare($conn, $ambil_data_qurban_sql);
    mysqli_stmt_bind_param($ambil_data_qurban, 's', $nik); // Menggunakan $nik untuk bind parameter
    mysqli_stmt_execute($ambil_data_qurban);
    $data_qurban = mysqli_stmt_get_result($ambil_data_qurban);
    $data_qurban = mysqli_fetch_assoc($data_qurban);

    // Pastikan data ditemukan untuk pembagian daging
    if ($data_qurban) {
        $jumlah_kg = $data_qurban['jumlah_kg'];
        $status_pengambilan = $data_qurban['status_pengambilan'] == 'sudah' ? 'Sudah Diambil' : 'Belum Diambil';
    } else {
        $jumlah_kg = 0;
        $status_pengambilan = 'Belum Diambil';
    }
} else {
    // Jika data pengguna tidak ditemukan, arahkan ke login atau tampilkan error
    $error = "Pengguna tidak ditemukan. Silakan login kembali.";
    header('Location: ../login.php'); // Atau tampilkan pesan error sesuai kebutuhan
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Warga - QURBANA</title>
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
                <h1>Dashboard Warga</h1>
                <p>Selamat datang, <?php echo htmlspecialchars($user['name']); ?></p>
            </div>
            <a href="../logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>

        <!-- User Information Card -->
        <div class="user-info-card">
            <h3>
                <div class="icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                Informasi Pribadi
            </h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="user-detail">
                        <span class="user-label">Nama Lengkap</span>
                        <span class="user-value"><?php echo htmlspecialchars($user['name']); ?></span>
                    </div>
                    <div class="user-detail">
                        <span class="user-label">NIK</span>
                        <span class="user-value"><?php echo htmlspecialchars($user['nik']); ?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="user-detail">
                        <span class="user-label">Jenis Kelamin</span>
                        <span class="user-value"><?php echo ($user['jenis_kelamin'] == 'L') ? 'Laki-laki' : 'Perempuan'; ?></span>
                    </div>
                    <div class="user-detail">
                        <span class="user-label">Alamat</span>
                        <span class="user-value"><?php echo htmlspecialchars($user['alamat']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="main-grid">
            <!-- Meat Weight Card -->
            <div class="card-modern">
                <h3>
                    <div class="icon">
                        <i class="fas fa-weight"></i>
                    </div>
                    Total Berat Daging Qurban
                </h3>
                <div class="meat-weight"><?php echo $jumlah_kg; ?> Kg</div>
                <p class="meat-subtitle">Jumlah daging qurban yang akan Anda terima</p>
                <div class="text-center">
                    <span class="status-badge <?php echo ($status_pengambilan == 'Sudah Diambil') ? 'status-success' : 'status-warning'; ?>">
                        Status: <?php echo $status_pengambilan; ?>
                    </span>
                </div>
            </div>

            <!-- QR Code Card -->
            <div class="card-modern">
                <h3>
                    <div class="icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    QR Code Pengambilan
                </h3>
                <div class="qr-container">
                    <?php
                    $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode("NIK: " . $user['nik'] . " - " . $user['name']);
                    ?>
                    <img src="<?php echo $qr_url; ?>" alt="QR Code" width="160">
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Tunjukkan QR Code ini saat pengambilan daging qurban</p>
                </div>
            </div>
        </div>

        <!-- Information Section -->
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
                        <h6><i class="fas fa-clipboard-list"></i> Cara Pengambilan Daging:</h6>
                        <ul>
                            <li>Datang ke tempat pembagian yang telah ditentukan</li>
                            <li>Tunjukkan QR Code di atas</li>
                            <li>Bawa kantong/wadah untuk daging</li>
                            <li>Serahkan kepada panitia untuk verifikasi</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert-modern">
                        <h6><i class="fas fa-clock"></i> Jadwal Pengambilan:</h6>
                        <p class="mb-2">Daging dapat diambil setelah proses pemotongan selesai.</p>
                        <p class="mb-0">Pantau pengumuman dari panitia untuk jadwal yang tepat.</p>
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
