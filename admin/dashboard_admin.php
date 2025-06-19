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
$query = "SELECT * FROM users WHERE nik = '$nik'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Ambil data total warga
$total_warga_sql = "SELECT COUNT(*) AS total_warga FROM users WHERE role = 'warga'";
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

// Ambil total peserta qurban
$total_berqurban_sql = "SELECT COUNT(*) AS total_berqurban FROM user_roles WHERE role = 'berqurban'";
$total_berqurban_result = mysqli_query($conn, $total_berqurban_sql);
$total_berqurban = mysqli_fetch_assoc($total_berqurban_result)['total_berqurban'];

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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-green: #2d7a41;
            --light-green: #4ade80;
            --mint-green: #6ee7b7;
            --emerald-green: #10b981;
            --sage-green: #84cc16;
            --forest-green: #15803d;
            
            --bg-primary: #f0fdf4;
            --bg-card: #ffffff;
            --bg-secondary: #ecfdf5;
            --text-primary: #064e3b;
            --text-secondary: #6b7280;
            --text-light: #9ca3af;
            --border-color: #d1fae5;
            --shadow-color: rgba(16, 185, 129, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header-section {
            background: linear-gradient(135deg, var(--emerald-green) 0%, var(--forest-green) 100%);
            color: white;
            padding: 2.5rem;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px var(--shadow-color), 0 10px 10px -5px var(--shadow-color);
            margin-bottom: 2rem;
            position: relative;
            overflow: auto;
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }

        .header-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(-30%, 30%);
        }

        .welcome-text {
            position: relative;
            z-index: 1;
        }

        .welcome-text h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .admin-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            border: 1px solid rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .welcome-text p {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 400;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: absolute;
            top: 2.5rem;
            right: 2.5rem;
            z-index: 1;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            transform: translateY(-2px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--bg-card);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px var(--shadow-color), 0 2px 4px -1px var(--shadow-color);
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px var(--shadow-color), 0 10px 10px -5px var(--shadow-color);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(135deg, var(--light-green) 0%, var(--emerald-green) 100%);
        }

        .stat-trend {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: linear-gradient(135deg, var(--mint-green) 0%, var(--light-green) 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .stat-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--mint-green) 0%, var(--emerald-green) 100%);
            color: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 6px -1px var(--shadow-color);
        }

        .stat-value-container {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
            flex-shrink: 0;
        }

        .progress-container {
            flex-grow: 1;
            background-color: var(--bg-secondary);
            border-radius: 50px;
            height: 12px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            position: relative;
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--light-green) 0%, var(--emerald-green) 100%);
            height: 100%;
            border-radius: 50px;
            transition: width 1.5s ease;
            position: relative;
            overflow: hidden;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .stat-label {
            font-size: 1rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .menu-section {
            margin-bottom: 3rem;
        }

        .menu-section h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .menu-card {
            background: var(--bg-card);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px var(--shadow-color), 0 2px 4px -1px var(--shadow-color);
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .menu-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px var(--shadow-color), 0 10px 10px -5px var(--shadow-color);
        }

        .menu-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--light-green) 0%, var(--emerald-green) 100%);
        }

        .menu-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .menu-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--mint-green) 0%, var(--emerald-green) 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1rem;
            box-shadow: 0 8px 15px -3px var(--shadow-color);
        }

        .menu-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .menu-card p {
            font-size: 0.95rem;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .menu-btn {
            background: linear-gradient(135deg, var(--emerald-green) 0%, var(--forest-green) 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
        }

        .menu-btn:hover {
            background: linear-gradient(135deg, var(--forest-green) 0%, var(--primary-green) 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px -3px var(--shadow-color);
        }

        .footer {
            text-align: center;
            padding: 2rem;
            background: var(--bg-card);
            border-radius: 20px;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            box-shadow: 0 4px 6px -1px var(--shadow-color);
        }

        .footer p {
            margin: 0;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }
            
            .header-section {
                padding: 1.5rem;
                text-align: center;
            }
            
            .logout-btn {
                position: relative;
                top: auto;
                right: auto;
                margin-top: 1rem;
            }
            
            .welcome-text h1 {
                font-size: 2rem;
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .stat-value-container {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .progress-container {
                width: 100%;
            }
            
            .menu-section h2 {
                font-size: 1.5rem;
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
                <div class="stat-value"><?php echo number_format($total_berqurban); ?></div>
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
                <div class="stat-value-container">
                    <div class="stat-value"><?php echo number_format($status_pembagian, 1); ?>%</div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: <?php echo $status_pembagian; ?>%"></div>
                    </div>
                </div>
                <div class="stat-label">Daging Telah Terdistribusi</div>
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