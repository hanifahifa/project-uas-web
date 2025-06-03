<?php
session_start();
include '../db.php';

// Pastikan pengguna sudah login dan memiliki peran 'warga'
if (!isset($_SESSION['user_nik']) || $_SESSION['role'] !== 'warga') {
    header('Location: ../login.php');
    exit();
}

// Ambil data dari database sesuai hak akses Warga
$user_nik = $_SESSION['user_nik'];  // Menggunakan NIK sebagai identifikasi pengguna
$query = $pdo->prepare("SELECT * FROM users WHERE nik = ?");  // Pastikan untuk menggunakan 'nik' sebagai identifikasi
$query->execute([$user_nik]);
$user = $query->fetch();

// Ambil data tambahan untuk Warga (misal, status qurban)
$ambil_data_qurban_sql = "SELECT * FROM pembagian_daging WHERE nik = ?";  // Ganti 'user_id' dengan 'nik'
$ambil_data_qurban = $pdo->prepare($ambil_data_qurban_sql);
$ambil_data_qurban->execute([$user_nik]);
$data_qurban = $ambil_data_qurban->fetch();

// Pastikan data ditemukan
if ($data_qurban) {
    // Ambil data jumlah hewan qurban dan QR Code (tidak diperlukan lagi karena akan di-generate)
    $jumlah_kg = $data_qurban['jumlah_kg'];
} else {
    // Jika tidak ada data, tampilkan nilai default
    $jumlah_kg = 0;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Warga - Sistem Qurban</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Global Styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        /* Container Styling */
        .container-dashboard {
            max-width: 1200px;
            margin-top: 50px;
        }

        /* Card Styling */
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Title Styling */
        h2 {
            color: #007bff;
            font-size: 32px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 40px;
        }

        /* Card Content */
        .card-header {
            background-color: #007bff;
            color: white;
        }

        .card-body {
            text-align: center;
        }

        .card-body img {
            max-width: 100px;
            margin-top: 20px;
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
    </style>
</head>

<body>

    <div class="container-dashboard container">
        <!-- Tombol Kembali ke Dashboard -->
        <a href="../index.html" class="btn btn-outline-primary mb-4">&lt; log out</a>

        <!-- Judul Dashboard -->
        <h2>Dashboard Warga</h2>

        <!-- Data Diri Pengguna -->
        <div>

            <h5>Nama Lengkap: </h5>
            <p><?php echo $user['name']; ?></p>

            <h5>Alamat: </h5>
            <p><?php echo $user['alamat']; ?></p>
            <h5>Jenis Kelamin: </h5>
            <p><?php echo ($user['jenis_kelamin'] == 'L') ? 'Laki-laki' : 'Perempuan'; ?></p>

        </div>

        <!-- Statistik Qurban Warga -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Total Berat Hewan Qurban yang Diterima</h5>
                    </div>
                    <div class="card-body">
                        <p><?php echo $jumlah_kg; ?> Kg</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>QR Code Pengambilan Daging</h5>
                    </div>
                    <div class="card-body">
                        <!-- Membuat URL untuk QR Code menggunakan NIK -->
                        <?php
                        $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode("NIK: " . $user['nik']);
                        ?>
                        <!-- Menampilkan QR Code -->
                        <img src="<?php echo $qr_url; ?>" alt="QR Code" width="150">
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