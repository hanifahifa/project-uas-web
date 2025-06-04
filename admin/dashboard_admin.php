<?php
session_start();
include '../db.php';

// Pastikan pengguna sudah login dan memiliki peran 'admin'
if (!isset($_SESSION['user_nik']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Qurban</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Global Styling */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Navbar Styling */
        .navbar {
            background-color: rgb(12, 71, 41);
            padding: 10px;
        }

        .navbar a {
            color: white;
            font-size: 18px;
            padding: 10px 15px;
            text-decoration: none;
        }

        .navbar a:hover {
            background-color: #0056b3;
        }

        .navbar-brand {
            font-size: 24px;
            font-weight: bold;
        }

        /* Dashboard Styling */
        .container-dashboard {
            width: 100%;
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: rgb(12, 71, 41);
            font-size: 32px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 30px;
        }

        .back-btn {
            font-size: 16px;
            color: rgb(12, 71, 41);
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
            transition: color 0.3s ease-in-out;
        }

        .back-btn:hover {
            color: rgb(12, 71, 41);
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 30px;
        }

        .card {
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h4 {
            font-size: 20px;
            color: rgb(12, 71, 41);
        }

        .card p {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .menu-nav .row {
            display: flex;
            flex-wrap: wrap;
            gap: 0px;
            justify-content: space-between;
        }

        .card-body {
            text-align: center;
        }

        .card-title {
            color: rgb(12, 71, 41);
            font-size: 18px;
            font-weight: bold;
        }

        .card-text {
            font-size: 14px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: rgb(12, 71, 41);
            color: white;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            transition: background-color 0.3s ease-in-out;
            margin-bottom: 10px;
        }

        .btn:hover {
            background-color: rgb(8, 50, 29);
            color: white;
            text-decoration: none;
        }

        footer {
            margin-top: 50px;
            font-size: 14px;
            color: #777;
            text-align: center;
        }

        footer p {
            margin: 0;
        }

        @media (max-width: 1200px) {
            .dashboard-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .card {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .container-dashboard {
                padding: 15px;
            }

            .card {
                width: 100%;
            }

            .dashboard-stats {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .container-dashboard {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="dashboard_admin.php">Sistem Qurban</a>
        <div class="navbar-nav">
            <a href="../logout.php" class="nav-link">Logout</a>
        </div>
    </nav>

    <div class="container-dashboard">

        <h2>Dashboard Admin</h2>

        <!-- Statistik Dashboard -->
        <div class="dashboard-stats">
            <div class="card">
                <h4>Total Warga Terdaftar</h4>
                <p><?php echo $total_warga; ?></p>
            </div>
            <div class="card">
                <h4>Total Iuran yang Terkumpul</h4>
                <p>Rp <?php echo number_format($total_iuran, 0, ',', '.'); ?></p>
            </div>
            <div class="card">
                <h4>Total Warga yang ber-Qurban</h4>
                <p><?php echo $total_hewan; ?> Orang</p>
            </div>

            <div class="card">
                <h4>Status Pembagian Daging</h4>
                <p><?php echo number_format($status_pembagian, 2, ',', '.'); ?>% telah diambil</p>
            </div>
        </div>

        <!-- Menu Navigasi -->
        <div class="menu-nav">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manajemen Pengguna</h5>
                            <p class="card-text">Kelola pengguna yang terdaftar dalam sistem.</p>
                            <a href="manage_user.php" class="btn">Lihat Pengguna</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Laporan Keuangan</h5>
                            <p class="card-text">Lihat laporan keuangan dari iuran qurban.</p>
                            <a href="financial_report.php" class="btn">Lihat Laporan</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Pembagian Daging</h5>
                            <p class="card-text">Lihat dan kelola distribusi daging qurban.</p>
                            <a href="meat_distribution.php" class="btn">Kelola Pembagian</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer>
            &copy; 2025 QURBANA: Sistem Qurban RT 001.
        </footer>
    </div>

</body>

</html>