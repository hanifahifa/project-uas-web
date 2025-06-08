<?php
session_start();
include '../db.php';  // Menghubungkan ke database

// Pastikan pengguna sudah login dan memiliki peran 'panitia'
if (!isset($_SESSION['user_nik']) || $_SESSION['role'] !== 'panitia') {
    header('Location: ../login.php');
    exit();
}

// Ambil data pengguna yang login
$user_nik = $_SESSION['user_nik'];
$query = $pdo->prepare("SELECT * FROM users WHERE nik = ?");
$query->execute([$user_nik]);
$user = $query->fetch();

// Ambil data tambahan untuk Panitia (misal, statistik qurban)
$ambil_data_qurban_sql = "SELECT * FROM pembagian_daging";
$ambil_data_qurban = $pdo->prepare($ambil_data_qurban_sql);
$ambil_data_qurban->execute();
$data_qurban = $ambil_data_qurban->fetchAll();

// Menghitung total berat daging yang dibagikan
$total_daging = 0;
$jumlah_daging_sudah_diambil = 0;
foreach ($data_qurban as $row) {
    $total_daging += $row['jumlah_kg'];
    if ($row['status_pengambilan'] == 'sudah') {
        $jumlah_daging_sudah_diambil += $row['jumlah_kg'];
    }
}

// Menghitung persentase pembagian daging
$persen_daging_terdistribusi = ($total_daging > 0) ? ($jumlah_daging_sudah_diambil / $total_daging) * 100 : 0;

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Panitia - QURBANA</title>
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

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow);
            border: none;
        }

        .stat-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .stat-card .icon {
            width: 40px;
            height: 40px;
            background: var(--primary-light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-dark);
        }

        .progress-container {
            margin-top: 1rem;
        }

        .progress {
            height: 12px;
            border-radius: 8px;
            background: var(--primary-light);
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--primary-medium), var(--primary-dark));
            border-radius: 8px;
            transition: width 0.6s ease;
        }

        .percentage-text {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }

        .stat-detail {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1rem 0;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--primary-light);
        }

        .stat-detail:last-child {
            border-bottom: none;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .stat-value {
            font-weight: 600;
            color: var(--primary-dark);
            font-size: 1.1rem;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .menu-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow);
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .menu-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 32px rgba(26, 79, 46, 0.12);
        }

        .menu-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-medium), var(--primary-dark));
        }

        .menu-card h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .menu-card .icon {
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

        .menu-card p {
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .menu-btn {
            background: var(--primary-dark);
            color: var(--white);
            border: none;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            width: 100%;
            justify-content: center;
        }

        .menu-btn:hover {
            background: var(--primary-medium);
            color: var(--white);
            transform: translateY(-1px);
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
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .header-section {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .menu-grid {
                grid-template-columns: 1fr;
            }
            
            .dashboard-container {
                padding: 1rem;
            }
        }
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