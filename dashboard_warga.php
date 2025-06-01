<?php
session_start();
include 'db.php';

// Pastikan pengguna sudah login dan memiliki peran 'warga'
if (!isset($_SESSION['user_nik']) || $_SESSION['role'] !== 'warga') {
    header('Location: login.php');
    exit();
}

// Ambil data dari database sesuai hak akses Warga
$user_nik = $_SESSION['user_nik'];  // Ganti 'user_id' dengan 'user_nik'
$query = $pdo->prepare("SELECT * FROM users WHERE nik = ?");  // Ganti 'id' dengan 'nik'
$query->execute([$user_nik]);
$user = $query->fetch();

// Ambil data tambahan untuk Warga (misal, status qurban)
$ambil_data_qurban_sql = "SELECT * FROM pembagian_daging WHERE nik = ?";  // Ganti 'user_id' dengan 'nik'
$ambil_data_qurban = $pdo->prepare($ambil_data_qurban_sql);
$ambil_data_qurban->execute([$user_nik]);
$data_qurban = $ambil_data_qurban->fetch();

// Pastikan data ditemukan
if ($data_qurban) {
    // Ambil data jenis dan jumlah hewan qurban
    $jenis_daging = $data_qurban['jenis_daging'];
    $jumlah_kg = $data_qurban['jumlah_kg'];
} else {
    // Jika tidak ada data, tampilkan nilai default
    $jenis_daging = 'Tidak Ada Data';
    $jumlah_kg = 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Warga - Sistem Qurban</title>
    <style>
        /* Global Styling */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            margin: 0;
            padding: 0;
        }

       /* Dashboard Styling */
.container-dashboard {
    width: 100%;
    max-width: 1200px;
    margin: 50px auto;
    padding: 30px;
    background-color: #ffffff;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

/* Tombol Back */
.back-btn {
    font-size: 16px;
    color: #007bff;
    text-decoration: none;
    display: inline-block;
    margin-bottom: 20px;
    transition: color 0.3s ease-in-out;
}

.back-btn:hover {
    color: #0056b3;
}

/* Judul Dashboard */
h2 {
    color: #007bff;
    font-size: 32px;
    font-weight: 600;
    text-align: center;
    margin-bottom: 20px; /* Menurunkan jarak bawah judul */
}

/* Statistik Qurban */
.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;  /* Mengurangi jarak antar card */
    margin-bottom: 20px; /* Mengurangi margin bawah */
}

.card {
    background-color: #ffffff;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    transition: transform 0.3s ease-in-out;
    margin: 0; /* Menghapus margin di card */
}

.card:hover {
    transform: translateY(-5px);
}

.card h4 {
    font-size: 22px;
    color: #007bff;
}

.card p {
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

/* Menu Navigasi Warga */
.menu-nav .row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;  /* Mengurangi jarak antar kolom */
    justify-content: space-between;
}

.card-body {
    text-align: center;
}

.card-title {
    color: #007bff;
    font-size: 18px;
    font-weight: bold;
}

.card-text {
    font-size: 14px;
    margin-bottom: 20px;
}

/* Tombol */
.btn {
    display: inline-block;
    padding: 12px 30px;
    background-color: #007bff;
    color: white;
    border-radius: 12px;
    cursor: pointer;
    font-size: 16px;
    text-decoration: none;
    transition: background-color 0.3s ease-in-out;
    margin-bottom: 10px; /* Mengurangi margin bawah tombol */
}

.btn:hover {
    background-color: #0056b3;
}

/* Footer */
footer {
    margin-top: 50px;
    font-size: 14px;
    color: #777;
    text-align: center;
}

footer p {
    margin: 0;
}

/* Media Queries for Responsiveness */
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

    <div class="container-dashboard">
        <!-- Tombol Back ke Dashboard -->
        <a href="index.html" class="back-btn">&lt; Back</a>

        <!-- Judul Dashboard -->
        <h2>Dashboard Warga</h2>

        <!-- Statistik Qurban Warga -->
        <div class="dashboard-stats">
            <div class="card">
                <h4>Total Hewan Qurban</h4>
                <p><?php echo $jenis_daging; ?></p>
            </div>
            <div class="card">
                <h4>Total Berat Hewan Qurban yang Diterima</h4>
                <p><?php echo $jumlah_kg; ?> Kg</p>
            </div>
        </div>

        <!-- Menu Navigasi Warga -->
        <div class="menu-nav">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Lihat Status Qurban</h5>
                            <p class="card-text">Lihat status qurban Anda.</p>
                            <a href="status_qurban.php" class="btn">Lihat Status</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <p>&copy; 2025 Sistem Qurban. All rights reserved.</p>
        </footer>
    </div>

</body>

</html>