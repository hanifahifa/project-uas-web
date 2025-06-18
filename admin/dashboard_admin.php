<?php
session_start();
include '../db.php';

// Pastikan pengguna sudah login dan memiliki peran 'admin'
// if (!isset($_SESSION['user_nik']) || $_SESSION['role'] !== 'admin') {
//     header('Location: ../login.php');
//     exit();
// }

// Membuat koneksi ke MySQL
$conn = mysqli_connect($host, $username, $password, $dbname);

// Mengecek apakah koneksi berhasil
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ambil data pengguna yang login
$nik = $_SESSION['nik'];
$query = mysqli_prepare($conn, "SELECT * FROM users WHERE nik = ?");
mysqli_stmt_bind_param($query, 's', $user_nik);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);
$user = mysqli_fetch_assoc($result);

// Ambil data total warga
$total_warga_sql = "SELECT COUNT(*) AS total_warga FROM users";
$total_warga_result = mysqli_query($conn, $total_warga_sql);
$total_warga = mysqli_fetch_assoc($total_warga_result)['total_warga'];

// Ambil total iuran yang terkumpul dari tabel hewan_qurban
$total_iuran_sql = "SELECT SUM(harga_per_ekor * jumlah) AS total_iuran FROM hewan_qurban";
$total_iuran_result = mysqli_query($conn, $total_iuran_sql);
$total_iuran = mysqli_fetch_assoc($total_iuran_result)['total_iuran'];

// Jika total_iuran adalah null, set ke 0
if ($total_iuran === null) {
    $total_iuran = 0;
}

// Ambil total hewan qurban
$total_hewan_sql = "SELECT COUNT(*) AS total_hewan FROM hewan_qurban";
$total_hewan_result = mysqli_query($conn, $total_hewan_sql);
$total_hewan = mysqli_fetch_assoc($total_hewan_result)['total_hewan'];

// Ambil status pembagian daging (misalnya persentase)
$ambil_daging_sql = "SELECT COUNT(*) AS total_ambil_daging FROM pembagian_daging WHERE status_pengambilan = 'sudah'";
$ambil_daging_result = mysqli_query($conn, $ambil_daging_sql);
$total_ambil_daging = mysqli_fetch_assoc($ambil_daging_result)['total_ambil_daging'];

// Ambil total daging yang ada
$total_daging_sql = "SELECT COUNT(*) AS total_daging FROM pembagian_daging";
$total_daging_result = mysqli_query($conn, $total_daging_sql);
$total_daging = mysqli_fetch_assoc($total_daging_result)['total_daging'];

// Periksa jika total daging tidak nol untuk menghindari pembagian dengan nol
if ($total_daging > 0) {
    $status_pembagian = ($total_ambil_daging / $total_daging) * 100;
} else {
    $status_pembagian = 0; // Set ke 0 jika tidak ada data
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - QURBANA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Styling dashboard (same as previously provided) */
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="welcome-text">
                <h1>
                    Dashboard Admin
                    <span class="admin-badge">
                        <i class="fas fa-crown"></i> Administrator
                    </span>
                </h1>
                <p>Selamat datang, <?php echo htmlspecialchars($user['name'] ?? $user['nama'] ?? 'Administrator'); ?></p>
            </div>
            <a href="../logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>

        <!-- Statistics Section -->
        <div class="stats-grid">
            <!-- Total Warga -->
            <div class="stat-card">
                <div class="stat-trend">Aktif</div>
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo number_format($total_warga); ?></div>
                <div class="stat-label">Total Warga Terdaftar</div>
            </div>

            <!-- Total Iuran -->
            <div class="stat-card">
                <div class="stat-trend">Terkumpul</div>
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
                <div class="stat-value">Rp <?php echo number_format($total_iuran, 0, ',', '.'); ?></div>
                <div class="stat-label">Total Iuran Terkumpul</div>
            </div>

            <!-- Total Warga Qurban -->
            <div class="stat-card">
                <div class="stat-trend">Peserta</div>
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo number_format($total_hewan); ?></div>
                <div class="stat-label">Warga Peserta Qurban</div>
            </div>

            <!-- Status Pembagian -->
            <div class="stat-card">
                <div class="stat-trend">Progress</div>
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo number_format($status_pembagian, 1); ?>%</div>
                <div class="stat-label">Daging Telah Terdistribusi</div>
                <div class="progress-container">
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $status_pembagian; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Section -->
        <div class="menu-section">
            <h2>Menu Administrasi</h2>
            <div class="menu-grid">
                <!-- Manajemen Pengguna -->
                <div class="menu-card">
                    <div class="menu-header">
                        <div class="menu-icon">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <h3>Manajemen Pengguna</h3>
                    </div>
                    <p>Kelola data pengguna yang terdaftar dalam sistem, tambah pengguna baru, dan atur hak akses.</p>
                    <a href="../admin/manage_user.php" class="menu-btn">
                        <i class="fas fa-arrow-right"></i>
                        Kelola Pengguna
                    </a>
                </div>

                <!-- Laporan Keuangan -->
                <div class="menu-card">
                    <div class="menu-header">
                        <div class="menu-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Laporan Keuangan</h3>
                    </div>
                    <p>Pantau dan kelola laporan keuangan dari iuran qurban, termasuk pemasukan dan pengeluaran.</p>
                    <a href="../admin/financial_report.php" class="menu-btn">
                        <i class="fas fa-arrow-right"></i>
                        Lihat Laporan
                    </a>
                </div>

                <!-- Pembagian Daging -->
                <div class="menu-card">
                    <div class="menu-header">
                        <div class="menu-icon">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <h3>Distribusi Daging</h3>
                    </div>
                    <p>Pantau dan kelola proses distribusi daging qurban kepada warga yang berhak menerima.</p>
                    <a href="../admin/meat_distribution.php" class="menu-btn">
                        <i class="fas fa-arrow-right"></i>
                        Kelola Distribusi
                    </a>
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
