<?php
session_start();
include '../db.php';

// Pastikan hanya pengguna dengan role "berqurban" yang bisa mengakses halaman ini
if (!isset($_SESSION['user_nik']) || $_SESSION['role'] != 'berqurban') {
    header('Location: ../login.php');
    exit;
}

$user_nik = $_SESSION['user_nik'];

// Ambil data user
$query = $pdo->prepare("SELECT * FROM users WHERE nik = ?");
$query->execute([$user_nik]);
$user = $query->fetch();

// Ambil data hewan qurban berdasarkan sumber (NIK user)
$query_hewan = $pdo->prepare("SELECT * FROM hewan_qurban WHERE sumber = ?");
$query_hewan->execute([$user_nik]);
$data_hewan = $query_hewan->fetch();

// Ambil data pembagian daging
$query_daging = $pdo->prepare("SELECT * FROM pembagian_daging WHERE nik = ?");
$query_daging->execute([$user_nik]);
$data_daging = $query_daging->fetch();

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #1a4f2e;
            --primary-medium: #8fbc8f;
            --primary-light: #e8f5e8;
            --accent: #f4d4a7;
            --text-dark: #2c3e50;
            --text-light: #6c757d;
            --white: #ffffff;
            --border-radius: 16px;
            --shadow: 0 4px 20px rgba(26, 79, 46, 0.08);
            --success: #28a745;
            --warning: #ffc107;
            --info: #17a2b8;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--primary-light) 0%, #f8fffe 100%);
            min-height: 100vh;
            color: var(--text-dark);
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            background: var(--white);
            padding: 1.5rem 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .welcome-text h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin: 0;
        }

        .welcome-text p {
            color: var(--text-light);
            margin: 0.5rem 0 0 0;
            font-size: 0.95rem;
        }

        .logout-btn {
            background: var(--primary-medium);
            color: var(--white);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logout-btn:hover {
            background: var(--primary-dark);
            color: var(--white);
            transform: translateY(-1px);
        }

        .user-info {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2.5rem;
            box-shadow: var(--shadow);
            border: none;
            position: relative;
            overflow: hidden;
        }

        .user-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-medium), var(--primary-dark));
        }

        .user-info h4 {
            color: var(--primary-dark);
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-info .icon {
            width: 40px;
            height: 40px;
            background: var(--primary-light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-dark);
        }

        .user-detail {
            margin-bottom: 1.5rem;
        }

        .user-detail h5 {
            color: var(--primary-dark);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .user-detail p {
            color: var(--text-light);
            margin: 0;
            font-size: 1rem;
            font-weight: 500;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .dashboard-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow);
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(26, 79, 46, 0.12);
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .card-primary::before {
            background: linear-gradient(90deg, var(--primary-medium), var(--primary-dark));
        }

        .card-info::before {
            background: linear-gradient(90deg, #28a745, #157347);
        }

        .card-warning::before {
            background: linear-gradient(90deg, #28a745, #157347);
        }

        .card-success::before {
            background: linear-gradient(90deg, #28a745, #157347);
        }

        .dashboard-card h5 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .dashboard-card .icon {
            width: 48px;
            height: 48px;
            background: var(--primary-light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-dark);
            font-size: 1.5rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.875rem 1rem;
            background: var(--primary-light);
            border-radius: 12px;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            font-weight: 500;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .info-value {
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 1rem;
        }

        .price-display {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--success);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-warning {
            background: #ffc107;
            color: #212529;
        }

        .qr-container {
            text-align: center;
            padding: 1.5rem;
        }

        .qr-container img {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }

        .qr-description {
            color: var(--text-light);
            font-size: 0.9rem;
            margin: 0;
        }

        .alert-custom {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
        }

        .alert-info-custom {
            background: var(--primary-light);
            color: var(--primary-dark);
        }

        .alert-success-custom {
            background: #d4edda;
            color: #155724;
        }

        /* Styling untuk Information Section seperti di dashboard warga */
        .info-section {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .info-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-medium), var(--primary-dark));
        }

        .info-section h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .info-section .icon {
            width: 48px;
            height: 48px;
            background: var(--primary-light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-dark);
            font-size: 1.5rem;
        }

        .alert-modern {
            background: var(--primary-light);
            border: 1px solid var(--primary-medium);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .alert-modern h6 {
            color: var(--primary-dark);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .alert-modern p {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .alert-modern p:last-child {
            margin-bottom: 0;
        }

        .footer {
            text-align: center;
            padding: 2rem 0;
            color: var(--text-light);
            font-size: 0.9rem;
            background: var(--white);
            border-radius: var(--border-radius);
            margin-top: 2rem;
        }

        @media (max-width: 768px) {
            .header-section {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .dashboard-container {
                padding: 1rem;
            }

            .user-info .row {
                flex-direction: column;
            }

            .user-info .col-md-4 {
                margin-bottom: 1rem;
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
                <p>Selamat datang, <?php echo htmlspecialchars($user['name']); ?></p>
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
            <div class="row">
                <div class="col-md-4">
                    <div class="user-detail">
                        <h5>Nama Lengkap:</h5>
                        <p><?php echo htmlspecialchars($user['name']); ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="user-detail">
                        <h5>NIK:</h5>
                        <p><?php echo htmlspecialchars($user['nik']); ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="user-detail">
                        <h5>Alamat:</h5>
                        <p><?php echo htmlspecialchars($user['alamat']); ?></p>
                    </div>
                </div>
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
                    <span class="info-value"><?php echo $jenis_hewan; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Jumlah Hewan:</span>
                    <span class="info-value"><?php echo $jumlah_hewan; ?> ekor</span>
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