<?php
// Menghubungkan ke database
include '../db.php';
session_start();

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['user_nik']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Mengambil data keuangan masuk
$sql_masuk = "SELECT * FROM keuangan_masuk ORDER BY tanggal DESC";
$stmt_masuk = $pdo->prepare($sql_masuk);
$stmt_masuk->execute();
$data_masuk = $stmt_masuk->fetchAll();

// Mengambil data keuangan keluar
$sql_keluar = "SELECT * FROM keuangan_keluar ORDER BY tanggal DESC";
$stmt_keluar = $pdo->prepare($sql_keluar);
$stmt_keluar->execute();
$data_keluar = $stmt_keluar->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Keuangan - Sistem Qurban</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: #f4f6f9;
      padding: 20px;
    }
    .card {
      margin-top: 20px;
    }
  </style>
  <a href="../admin/dashboard_admin.php" class="btn btn-secondary mb-4">&larr; Kembali ke Dashboard Admin</a>

</head>
<body>

<div class="container">
    <h1>Laporan Keuangan</h1>
    <p>Lihat laporan keuangan dari iuran qurban.</p>

    <!-- Tombol untuk input keuangan masuk dan keluar -->
    <a href="financial_input.php?jenis=masuk" class="btn btn-success">Input Keuangan Masuk</a>
    <a href="financial_input.php?jenis=keluar" class="btn btn-danger">Input Keuangan Keluar</a>

    <!-- Rekap Keuangan Masuk -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Laporan Keuangan Masuk</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Sumber</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data_masuk as $row) : ?>
                        <tr>
                            <td><?= $row['tanggal'] ?></td>
                            <td><?= $row['sumber'] ?></td>
                            <td>Rp. <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                            <td><?= $row['keterangan'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Rekap Keuangan Keluar -->
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h4>Laporan Keuangan Keluar</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keperluan</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data_keluar as $row) : ?>
                        <tr>
                            <td><?= $row['tanggal'] ?></td>
                            <td><?= $row['keperluan'] ?></td>
                            <td>Rp. <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                            <td><?= $row['keterangan'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
