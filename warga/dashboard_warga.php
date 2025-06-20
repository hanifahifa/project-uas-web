<?php
session_start();
include '../db.php';

// // Pastikan pengguna sudah login dan memiliki peran 'warga'
// if (!isset($_SESSION['nik']) || $_SESSION['role'] !== 'warga') {
//     header('Location: ../login.php');
//     exit();
// }

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

        .user-info-card {
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 12px;
            width: 100%;
            margin-bottom: 20px;
        }

        .user-info-card h3 {
            margin-bottom: 15px;
            font-size: 1.2em;
            display: flex;
            align-items: center;
        }

        .user-info-card .icon {
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

        .main-grid {
            display: flex;
            justify-content: space-between;
            width: 100%;
            gap: 20px;
            margin-bottom: 20px;
        }

        .card-modern {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 48%;
            text-align: center;
        }

        .card-modern h3 {
            margin-bottom: 15px;
            font-size: 1.2em;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-modern .icon {
            margin-right: 10px;
        }

        .meat-weight {
            font-size: 2.5em;
            font-weight: bold;
            color: #28a745;
            margin: 10px 0;
        }

        .meat-subtitle {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 15px;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9em;
        }

        .status-success {
            background-color: #d4edda;
            color: #155724;
        }

        .status-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .qr-container {
            margin-top: 10px;
        }

        .qr-container img {
            border-radius: 8px;
            margin-bottom: 10px;
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
        }

        .alert-modern ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        .alert-modern li {
            margin-bottom: 5px;
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

            .main-grid {
                flex-direction: column;
            }

            .card-modern {
                width: 100%;
            }
        }
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

        <!-- User Information Card -->
        <!-- User Information Card -->
        <div class="user-info-card">
            <h3>
                <div class="icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                Informasi Pribadi
            </h3>
            <div class="user-detail">
                <span class="user-label">Nama Lengkap</span>
                <span class="user-value"><?php echo htmlspecialchars($user['name']); ?></span>
            </div>
            <div class="user-detail">
                <span class="user-label">NIK</span>
                <span class="user-value"><?php echo htmlspecialchars($user['nik']); ?></span>
            </div>
            <div class="user-detail">
                <span class="user-label">Jenis Kelamin</span>
                <span
                    class="user-value"><?php echo ($user['jenis_kelamin'] == 'L') ? 'Laki-laki' : 'Perempuan'; ?></span>
            </div>
            <div class="user-detail">
                <span class="user-label">Alamat</span>
                <span class="user-value"><?php echo htmlspecialchars($user['alamat']); ?></span>
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
                    <span
                        class="status-badge <?php echo ($status_pengambilan == 'Sudah Diambil') ? 'status-success' : 'status-warning'; ?>">
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
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Tunjukkan QR Code ini saat pengambilan daging
                        qurban</p>
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