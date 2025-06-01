<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'warga') {
    header('Location: login.php');
}

include 'header.php';
?>

<div class="container mt-4">
    <h1>Warga Dashboard</h1>
    <div class="row mt-4">
        <div class="col-md-6">
            <h3>Informasi Qurban</h3>
            <p>Jenis Hewan: Sapi</p>
            <p>Jumlah Hewan: 10 ekor</p>
            <p>Status Pembayaran: <strong>Sudah Dibayar</strong></p>
        </div>
        <div class="col-md-6">
            <h3>QR Code Pengambilan Daging</h3>
            <img src="path/to/qr_code.png" alt="QR Code" />
            <p>Scan QR Code saat pengambilan daging.</p>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>
