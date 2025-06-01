<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'panitia') {
    header('Location: login.php');
}

include 'header.php';
?>

<div class="container mt-4">
    <h1>Panitia Dashboard</h1>
    <div class="row mt-4">
        <div class="col-md-6">
            <h3>Daftar Pembagian Daging</h3>
            <p>Pengambilan: <strong>20 kg</strong> daging sapi</p>
            <p>Status Pengambilan: <strong>Belum diambil</strong></p>
            <a href="scan_qr.php" class="btn btn-primary">Scan QR Code</a>
        </div>
        <div class="col-md-6">
            <h3>Laporan Pembagian Daging</h3>
            <a href="distribution_report.php" class="btn btn-primary">Lihat Laporan Pembagian</a>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>
