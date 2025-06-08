<?php
session_start();
include '../db.php';

// Pastikan pengguna sudah login dan memiliki peran 'warga'
if (!isset($_SESSION['user_nik']) || $_SESSION['role'] !== 'warga') {
    header('Location: ../login.php');
    exit();
}

// Ambil data dari database sesuai hak akses Warga
$user_nik = $_SESSION['user_nik'];
$query = $pdo->prepare("SELECT * FROM users WHERE nik = ?");
$query->execute([$user_nik]);
$user = $query->fetch();

// Ambil data tambahan untuk Warga (misal, status qurban)
$ambil_data_qurban_sql = "SELECT * FROM pembagian_daging WHERE nik = ?";
$ambil_data_qurban = $pdo->prepare($ambil_data_qurban_sql);
$ambil_data_qurban->execute([$user_nik]);
$data_qurban = $ambil_data_qurban->fetch();

// Pastikan data ditemukan
if ($data_qurban) {
    $jumlah_kg = $data_qurban['jumlah_kg'];
    $status_pengambilan = $data_qurban['status_pengambilan'] == 'sudah' ? 'Sudah Diambil' : 'Belum Diambil';
} else {
    $jumlah_kg = 0;
    $status_pengambilan = 'Belum Diambil';
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
        }

        .logout-btn:hover {
            background: var(--primary-dark);
            color: var(--white);
            transform: translateY(-1px);
        }

        .user-info-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow);
            margin-bottom: 2.5rem;
            position: relative;
            overflow: hidden;
        }

        .user-info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-medium), var(--primary-dark));
        }

        .user-info-card h3 {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-info-card .icon {
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

        .user-detail {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--primary-light);
        }

        .user-detail:last-child {
            border-bottom: none;
        }

        .user-label {
            color: var(--text-light);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .user-value {
            font-weight: 600;
            color: var(--primary-dark);
            font-size: 1rem;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .card-modern {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow);
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(26, 79, 46, 0.12);
        }

        .card-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-medium), var(--primary-dark));
        }

        .card-modern h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-modern .icon {
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

        .meat-weight {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-dark);
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .meat-subtitle {
            color: var(--text-light);
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .status-badge {
            font-size: 1rem;
            padding: 0.75rem 1.25rem;
            border-radius: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status-success {
            background: var(--success);
            color: var(--white);
        }

        .status-warning {
            background: var(--warning);
            color: var(--text-dark);
        }

        .qr-container {
            text-align: center;
            padding: 1.5rem;
            background: var(--primary-light);
            border-radius: 12px;
            margin-top: 1rem;
        }

        .qr-container img {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(26, 79, 46, 0.1);
            margin-bottom: 1rem;
        }

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

        .alert-modern ul {
            margin-bottom: 0;
            padding-left: 1.2rem;
        }

        .alert-modern li {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
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
            .main-grid {
                grid-template-columns: 1fr;
            }
            
            .header-section {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .dashboard-container {
                padding: 1rem;
            }

            .meat-weight {
                font-size: 2rem;
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