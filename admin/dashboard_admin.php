<?php
session_start();
include '../db.php';

// Pastikan pengguna sudah login dan memiliki peran 'admin'
if (!isset($_SESSION['user_nik']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Ambil data pengguna yang login
$user_nik = $_SESSION['user_nik'];
$query = $pdo->prepare("SELECT * FROM users WHERE nik = ?");
$query->execute([$user_nik]);
$user = $query->fetch();

// Ambil data total warga
$total_warga_sql = "SELECT COUNT(*) AS total_warga FROM users";
$total_warga_result = $pdo->query($total_warga_sql);
$total_warga = $total_warga_result->fetch()['total_warga'];

// Ambil total iuran yang terkumpul dari tabel hewan_qurban (sesuai dengan financial_report.php)
$total_iuran_sql = "SELECT SUM(harga_per_ekor * jumlah) AS total_iuran FROM hewan_qurban";
$total_iuran_result = $pdo->query($total_iuran_sql);
$total_iuran = $total_iuran_result->fetch()['total_iuran'];

// Jika total_iuran adalah null, set ke 0
if ($total_iuran === null) {
    $total_iuran = 0;
}

// Ambil total hewan qurban
$total_hewan_sql = "SELECT COUNT(*) AS total_hewan FROM hewan_qurban";
$total_hewan_result = $pdo->query($total_hewan_sql);
$total_hewan = $total_hewan_result->fetch()['total_hewan'];

// Ambil status pembagian daging (misalnya persentase)
$ambil_daging_sql = "SELECT COUNT(*) AS total_ambil_daging FROM pembagian_daging WHERE status_pengambilan = 'sudah'";
$ambil_daging_result = $pdo->query($ambil_daging_sql);
$total_ambil_daging = $ambil_daging_result->fetch()['total_ambil_daging'];

// Ambil total daging yang ada
$total_daging_sql = "SELECT COUNT(*) AS total_daging FROM pembagian_daging";
$total_daging_result = $pdo->query($total_daging_sql);
$total_daging = $total_daging_result->fetch()['total_daging'];

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
            --shadow-hover: 0 8px 32px rgba(26, 79, 46, 0.12);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--primary-light) 0%, #f8fffe 100%);
            min-height: 100vh;
            color: var(--text-dark);
        }

        .dashboard-container {
            max-width: 1400px;
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
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin: 0;
        }

        .welcome-text p {
            color: var(--text-light);
            margin: 0.5rem 0 0 0;
            font-size: 0.95rem;
        }

        .admin-badge {
            background: linear-gradient(135deg, var(--accent), #f0c674);
            color: var(--text-dark);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-left: 1rem;
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
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow);
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-medium), var(--primary-dark));
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: var(--primary-light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-dark);
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 0.95rem;
            font-weight: 500;
        }

        .stat-trend {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--accent);
            color: var(--text-dark);
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .menu-section h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .menu-section h2::before {
            content: '';
            width: 4px;
            height: 30px;
            background: linear-gradient(135deg, var(--primary-medium), var(--primary-dark));
            border-radius: 2px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
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
            box-shadow: var(--shadow-hover);
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

        .menu-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .menu-icon {
            width: 56px;
            height: 56px;
            background: var(--primary-light);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-dark);
            font-size: 1.75rem;
        }

        .menu-card h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin: 0;
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
            margin-top: 3rem;
        }

        /* Progress bar for pembagian daging */
        .progress-container {
            margin-top: 1rem;
        }

        .progress {
            height: 8px;
            border-radius: 6px;
            background: var(--primary-light);
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--primary-medium), var(--primary-dark));
            border-radius: 6px;
            transition: width 0.6s ease;
        }

        @media (max-width: 768px) {
            .header-section {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .dashboard-container {
                padding: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .menu-grid {
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
                    <a href="manage_user.php" class="menu-btn">
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
                    <a href="financial_report.php" class="menu-btn">
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
                    <a href="meat_distribution.php" class="menu-btn">
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