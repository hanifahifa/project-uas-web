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
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="dashboard_admin.php">Sistem Qurban</a>
        <div class="navbar-nav">
            <a href="../logout.php" class="nav-link">Logout</a>
        </div>
    </nav>

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
            <img src="path/to/qr_code.png" alt="QR Code Pengambilan Daging" />
            <p>Scan QR Code saat pengambilan daging.</p>
        </div>
    </div>

    <!-- Menampilkan kontribusi & bukti pembayaran -->
    <div class="row mt-4">
        <div class="col-md-6">
            <h3>Kontribusi & Bukti Pembayaran</h3>
            <p>Jumlah Pembayaran: Rp. 10.000.000</p>
            <p>Tanggal Pembayaran: 20 Juni 2025</p>
            <p><a href="path/to/bukti_pembayaran.pdf" target="_blank">Lihat Bukti Pembayaran (PDF)</a></p>
        </div>
    </div>

    <!-- Menampilkan dokumentasi pemotongan hewan -->
    <div class="row mt-4">
        <div class="col-md-6">
            <h3>Dokumentasi Pemotongan Hewan</h3>
            <p>Dokumentasi pemotongan hewan akan ditampilkan di sini.</p>
            <img src="path/to/pemotongan_hewan.jpg" alt="Dokumentasi Pemotongan Hewan" />
        </div>
    </div>

    <!-- Menampilkan status pengambilan daging -->
    <div class="row mt-4">
        <div class="col-md-6">
            <h3>Status Pengambilan Daging</h3>
            <p>Status Pengambilan: <strong>Belum Diambil</strong></p>
            <p>Estimasi Waktu Pengambilan: 25 Juni 2025</p>
        </div>
    </div>

</div>

<?php
include 'footer.php';
?>