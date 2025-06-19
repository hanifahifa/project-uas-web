<?php
session_start();  // Make sure session_start() is called at the top

include '../db.php';  // Connecting to the database

// Connect to MySQL
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check if the connection is successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve the user's data based on their nik (from session)
$nik = $_SESSION['nik'];  // Use the session's nik directly
$query = mysqli_prepare($conn, "SELECT * FROM users WHERE nik = ?");
mysqli_stmt_bind_param($query, 's', $nik);  // Use the nik from the session
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);
$user = mysqli_fetch_assoc($result);

// Get additional data for Panitia (such as qurban statistics)
$ambil_data_qurban_sql = "SELECT * FROM pembagian_daging";
$ambil_data_qurban = mysqli_query($conn, $ambil_data_qurban_sql);
$data_qurban = mysqli_fetch_all($ambil_data_qurban, MYSQLI_ASSOC);

// Calculate the total weight of the meat distributed
$total_daging = 0;
$jumlah_daging_sudah_diambil = 0;
foreach ($data_qurban as $row) {
    $total_daging += $row['jumlah_kg'];
    if ($row['status_pengambilan'] == 'sudah') {
        $jumlah_daging_sudah_diambil += $row['jumlah_kg'];
    }
}

// Calculate the percentage of meat distributed
$persen_daging_terdistribusi = ($total_daging > 0) ? ($jumlah_daging_sudah_diambil / $total_daging) * 100 : 0;

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Panitia - QURBANA</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style >
        /* Styling yang sudah ada di HTML */
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="welcome-text">
                <h1>Dashboard Panitia</h1>
                <p>Selamat datang, <?php echo htmlspecialchars($user['name'] ?? $user['nama'] ?? 'Panitia'); ?></p>
            </div>

            <!-- Kembali Button -->            
            <!-- <a href="../Dashboard_Utama/dashboard.php" class="kembali-btn">
                <i class="fas fa-arrow-left"></i> Kembali
                $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
                $dashboard_link = ($role === 'panitia') ? '../Dashboard_Utama/dashboard.php' : '../dashboard.php'; // Modify as needed
             -->

            <a href="../Dashboard_Utama/dashboard.php" class="kembali-btn">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            
            <!-- Logout Button -->
            <a href="../logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>

        <!-- Statistics Section -->
        <div class="stats-grid">
            <!-- Progress Card -->
            <div class="stat-card">
                <h3>
                    <div class="icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    Progress Distribusi
                </h3>
                <div class="percentage-text"><?php echo number_format($persen_daging_terdistribusi, 1); ?>%</div>
                <p class="stat-label">dari total daging telah terdistribusi</p>
                <div class="progress-container">
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $persen_daging_terdistribusi; ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Statistics Details -->
            <div class="stat-card">
                <h3>
                    <div class="icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    Statistik Daging Qurban
                </h3>
                <div class="stat-detail">
                    <span class="stat-label">Total Daging Tersedia</span>
                    <span class="stat-value"><?php echo number_format($total_daging, 1); ?> Kg</span>
                </div>
                <div class="stat-detail">
                    <span class="stat-label">Sudah Terdistribusi</span>
                    <span class="stat-value"><?php echo number_format($jumlah_daging_sudah_diambil, 1); ?> Kg</span>
                </div>
                <div class="stat-detail">
                    <span class="stat-label">Belum Terdistribusi</span>
                    <span class="stat-value"><?php echo number_format($total_daging - $jumlah_daging_sudah_diambil, 1); ?> Kg</span>
                </div>
            </div>
        </div>

        <!-- Menu Section -->
        <div class="menu-grid">
            <!-- Distribusi Daging -->
            <div class="menu-card">
                <h3>
                    <div class="icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    Distribusi Daging
                </h3>
                <p>Kelola status pengambilan daging qurban oleh warga dan pantau distribusi secara real-time.</p>
                <a href="../admin/meat_distribution.php" class="menu-btn">
                    <i class="fas fa-arrow-right"></i>
                    Kelola Distribusi
                </a>
            </div>

            <!-- Laporan Keuangan -->
            <div class="menu-card">
                <h3>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    Laporan Keuangan
                </h3>
                <p>Lihat dan kelola laporan keuangan kegiatan qurban, termasuk pemasukan dan pengeluaran.</p>
                <a href="../admin/financial_report.php" class="menu-btn">
                    <i class="fas fa-arrow-right"></i>
                    Lihat Laporan
                </a>
            </div>

            <!-- Scan QR Code -->
            <div class="menu-card">
                <h3>
                    <div class="icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    Scan QR Code
                </h3>
                <p>Verifikasi pengambilan daging menggunakan QR Code untuk proses yang lebih efisien dan akurat.</p>
                <a href="scan_qr.php" class="menu-btn">
                    <i class="fas fa-arrow-right"></i>
                    Mulai Scan
                </a>
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
