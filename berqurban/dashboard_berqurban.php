<!-- <?php
session_start();

// Pastikan hanya pengguna dengan role "berqurban" yang bisa mengakses halaman ini
if (!isset($_SESSION['user_nik']) || $_SESSION['role'] != 'berqurban') {
    header('Location: login.php');  // Redirect ke login jika role bukan "berqurban"
    exit;
}

include '../header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
<!-- Gunakan CSS Bootstrap standar -->
<style>
    body {
        background-color: #f8f9fa;
        font-family: Arial, sans-serif;
    }

    .navbar {
        margin-bottom: 20px;
    }

    .card-header {
        font-size: 1.25rem;
        font-weight: bold;
    }

    .card-body {
        font-size: 1rem;
        line-height: 1.5;
    }

    .card {
        margin-bottom: 20px;
    }

    .text-success {
        color: #28a745 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .btn-info {
        background-color: #17a2b8;
    }

    .btn-info:hover {
        background-color: #138496;
    }

    .img-fluid {
        max-width: 100%;
        height: auto;
    }

    .container {
        max-width: 960px;
    }
</style>
</head>
<body>
    <div class="container mt-4">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="dashboard_admin.php">Sistem Qurban</a>
        <div class="navbar-nav">
            <a href="../logout.php" class="nav-link">Logout</a>
        </div>
    </nav>

    <!-- Menampilkan informasi qurban -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>Informasi Qurban Anda</h3>
                </div>
                <div class="card-body">
                    <p><strong>Jenis Hewan:</strong> Sapi</p>
                    <p><strong>Jumlah Hewan:</strong> 1 ekor</p>
                    <p><strong>Status Pembayaran:</strong> <span class="text-success">Sudah Dibayar</span></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h3>QR Code Pengambilan Daging</h3>
                </div>
                <div class="card-body text-center">
                    <img src="path/to/qr_code.png" alt="QR Code Pengambilan Daging" class="img-fluid mb-3" />
                    <p>Scan QR Code saat pengambilan daging.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Menampilkan kontribusi & bukti pembayaran -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3>Kontribusi & Bukti Pembayaran</h3>
                </div>
                <div class="card-body">
                    <p><strong>Jumlah Pembayaran:</strong> Rp. 10.000.000</p>
                    <p><strong>Tanggal Pembayaran:</strong> 20 Juni 2025</p>
                    <p><a href="path/to/bukti_pembayaran.pdf" target="_blank" class="btn btn-info btn-sm">Lihat Bukti Pembayaran (PDF)</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Menampilkan dokumentasi pemotongan hewan -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h3>Dokumentasi Pemotongan Hewan</h3>
                </div>
                <div class="card-body text-center">
                    <p>Dokumentasi pemotongan hewan akan ditampilkan di sini.</p>
                    <img src="path/to/pemotongan_hewan.jpg" alt="Dokumentasi Pemotongan Hewan" class="img-fluid mb-3" />
                </div>
            </div>
        </div>
    </div>

    <!-- Menampilkan status pengambilan daging -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3>Status Pengambilan Daging</h3>
                </div>
                <div class="card-body">
                    <p><strong>Status Pengambilan:</strong> <span class="text-danger">Belum Diambil</span></p>
                    <p><strong>Estimasi Waktu Pengambilan:</strong> 25 Juni 2025</p>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
include '../footer.php';
?>


</body>
</html> 
