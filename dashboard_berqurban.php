<?php
session_start();

// Pastikan hanya pengguna dengan role "berqurban" yang bisa mengakses halaman ini
if (!isset($_SESSION['user_nik']) || $_SESSION['role'] != 'berqurban') {
    header('Location: login.php');  // Redirect ke login jika role bukan "berqurban"
    exit;
}

include 'header.php';
?>

<div class="container mt-4">
    <h1>Dashboard Berqurban</h1>

    <!-- Menampilkan informasi qurban -->
    <div class="row mt-4">
        <div class="col-md-6">
            <h3>Informasi Qurban Anda</h3>
            <p>Jenis Hewan: Sapi</p>
            <p>Jumlah Hewan: 1 ekor</p>
            <p>Status Pembayaran: <strong>Sudah Dibayar</strong></p>
        </div>
        <div class="col-md-6">
            <h3>QR Code Pengambilan Daging</h3>
            <!-- Menampilkan QR Code -->
            <img src="path/to/qr_code.png" alt="QR Code" />
            <p>Scan QR Code saat pengambilan daging.</p>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>
