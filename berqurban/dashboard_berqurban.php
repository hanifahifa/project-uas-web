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
        body {
            background: #f7f7f7;
        }

        .dashboard-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 20px 30px;
            background-color: #28a745;
            color: white;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .header-section h1 {
            font-size: 1.5em;
            margin: 0;
        }

        .welcome-text p {
            margin: 0;
            font-size: 0.9em;
            opacity: 0.9;
        }

        .header-buttons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .kembali-btn, .logout-btn {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9em;
        }

        .kembali-btn {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .kembali-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateY(-1px);
        }

        .logout-btn {
            background-color: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.4);
        }

        .logout-btn:hover {
            background-color: rgba(220, 53, 69, 0.3);
            color: white;
            transform: translateY(-1px);
        }

        .user-info {
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 12px;
            width: 100%;
            margin-bottom: 20px;
        }

        .user-info h4 {
            margin-bottom: 15px;
            font-size: 1.2em;
            display: flex;
            align-items: center;
        }

        .user-info .icon {
            margin-right: 10px;
        }

        .user-detail {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .user-detail:last-child {
            border-bottom: none;
        }

        .user-label {
            font-weight: 600;
            color: #666;
        }

        .user-value {
            font-weight: 500;
            color: #333;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            width: 100%;
            margin-bottom: 20px;
        }

        .dashboard-card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #28a745;
        }

        .dashboard-card h5 {
            margin-bottom: 15px;
            font-size: 1.1em;
            display: flex;
            align-items: center;
            color: #333;
        }

        .dashboard-card .icon {
            margin-right: 10px;
            color: #28a745;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #666;
        }

        .info-value {
            font-weight: 500;
            color: #333;
        }

        .price-display {
            font-weight: bold;
            color: #28a745;
            font-size: 1.1em;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.85em;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .qr-container {
            text-align: center;
            padding: 15px 0;
        }

        .qr-container img {
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .qr-description {
            color: #666;
            font-size: 0.9em;
            margin: 0;
        }

        .info-section {
            width: 100%;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .info-section h3 {
            margin-bottom: 15px;
            font-size: 1.2em;
            display: flex;
            align-items: center;
        }

        .info-section .icon {
            margin-right: 10px;
            color: #28a745;
        }

        .alert-modern {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .alert-modern h6 {
            margin-bottom: 10px;
            color: #28a745;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .alert-modern h6 i {
            margin-right: 8px;
        }

        .alert-modern p {
            margin-bottom: 0;
            line-height: 1.5;
        }

        .footer {
            text-align: center;
            color: #666;
            font-size: 0.9em;
            margin-top: 20px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .header-section {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .header-buttons {
                width: 100%;
                justify-content: center;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
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

            <div class="header-buttons">
                <!-- Kembali Button -->            
                <a href="../Dashboard_Utama/dashboard.php" class="kembali-btn">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>

                <a href="../logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
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