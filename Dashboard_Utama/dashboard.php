<?php
session_start(); // Memulai sesi

// Pastikan pengguna sudah login dan memiliki nik di sesi
if (!isset($_SESSION['nik'])) {
    header('Location: login.php'); // Arahkan ke halaman login jika nik tidak ada
    exit;
}

// Ambil nik pengguna dari sesi
$nik = $_SESSION['nik'];

// Koneksi ke database
include '../db.php';

// Ambil semua role pengguna berdasarkan NIK
$sql = "SELECT role FROM user_roles WHERE nik = '$nik'";
$result = mysqli_query($conn, $sql);

// Menyimpan role pengguna dalam array
$roles = [];
while ($row = mysqli_fetch_assoc($result)) {
    $roles[] = $row['role'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar" style="width: 250px; float: left; padding-top: 20px;">
        <ul class="list-group">
            <?php if (in_array('admin', $roles)): ?>
                <li class="list-group-item"><a href="../admin/dashboard_admin.php">Dashboard Admin</a></li>
            <?php endif; ?>

            <?php if (in_array('panitia', $roles)): ?>
                <li class="list-group-item"><a href="../panitia/dashboard_panitia.php">Dashboard Panitia</a></li>
            <?php endif; ?>

            <?php if (in_array('berqurban', $roles)): ?>
                <li class="list-group-item"><a href="../Berqurban/dashboard_berqurban.php">Dashboard Berqurban</a></li>
            <?php endif; ?>

            <?php if (in_array('warga', $roles)): ?>
                <li class="list-group-item"><a href="../warga/dashboard_warga.php">Dashboard Warga</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Konten -->
    <div class="content" style="margin-left: 270px; padding: 20px;">
        <h1>Selamat datang di Dashboard!</h1>
        <p>Konten khusus untuk role <?php echo implode(', ', $roles); ?> akan ditampilkan di sini.</p>
    </div>

</body>

</html>
