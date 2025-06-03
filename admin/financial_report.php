<?php
// Menghubungkan ke database
include '../db.php';
session_start();

// Hanya admin dan panitia yang dapat mengakses
if (!isset($_SESSION['user_nik']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'panitia')) {
    header('Location: ../login.php');
    exit;
}

// Tentukan dashboard yang sesuai
$dashboard_url = ($_SESSION['role'] == 'admin') ? '../admin/dashboard_admin.php' : '../panitia/dashboard_panitia.php';

// Ambil data keuangan
$stmt_masuk = $pdo->prepare("SELECT * FROM keuangan_masuk ORDER BY tanggal DESC");
$stmt_masuk->execute();
$data_masuk = $stmt_masuk->fetchAll();

$stmt_keluar = $pdo->prepare("SELECT * FROM keuangan_keluar ORDER BY tanggal DESC");
$stmt_keluar->execute();
$data_keluar = $stmt_keluar->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Laporan Keuangan - Sistem Qurban</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f7fc;
    }

    .container {
      max-width: 1100px;
      margin: 50px auto;
      background: #fff;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    h1 {
      color: #094029;
      font-weight: bold;
      text-align: center;
      margin-bottom: 10px;
    }

    p {
      text-align: center;
      color: #666;
      margin-bottom: 30px;
    }

    .btn {
      border-radius: 12px;
      font-weight: 600;
    }

    .btn-success {
      background-color: #0e6e3d;
      border: none;
    }

    .btn-success:hover {
      background-color: #138c50;
    }

    .btn-danger {
      background-color: #c0392b;
      border: none;
    }

    .btn-danger:hover {
      background-color: #e74c3c;
    }

    .btn-secondary {
      background-color: #7f8c8d;
      border: none;
    }

    .card {
      border: none;
      margin-top: 30px;
      border-radius: 16px;
      box-shadow: 0 6px 12px rgba(0,0,0,0.05);
    }

    .card-header {
      border-radius: 16px 16px 0 0;
    }

    table th {
      background-color: #0b5f3c;
      color: white;
      text-align: center;
    }

    table td {
      vertical-align: middle;
    }

    .table {
      margin-bottom: 0;
    }

    @media (max-width: 768px) {
      .btn {
        width: 100%;
        margin-bottom: 10px;
      }
    }
  </style>
</head>
<body>

  <div class="container">
    <!-- Tombol Kembali -->
    <a href="<?= $dashboard_url ?>" class="btn btn-secondary mb-4">&larr; Kembali ke Dashboard</a>

    <!-- Judul -->
    <h1>Laporan Keuangan</h1>
    <p>Data pemasukan dan pengeluaran dari iuran qurban.</p>

    <!-- Tombol Input -->
    <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
      <a href="financial_input.php?jenis=masuk" class="btn btn-success">+ Input Keuangan Masuk</a>
      <a href="financial_input.php?jenis=keluar" class="btn btn-danger">+ Input Keuangan Keluar</a>
    </div>

    <!-- Laporan Masuk -->
    <div class="card">
      <div class="card-header bg-success text-white">
        <h4 class="mb-0">Laporan Keuangan Masuk</h4>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered table-hover mb-0">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Sumber</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($data_masuk as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row['tanggal']) ?></td>
                  <td><?= htmlspecialchars($row['sumber']) ?></td>
                  <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                  <td><?= htmlspecialchars($row['keterangan']) ?></td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($data_masuk)): ?>
                <tr>
                  <td colspan="4" class="text-center text-muted">Belum ada data keuangan masuk.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Laporan Keluar -->
    <div class="card">
      <div class="card-header bg-danger text-white">
        <h4 class="mb-0">Laporan Keuangan Keluar</h4>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered table-hover mb-0">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Keperluan</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($data_keluar as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row['tanggal']) ?></td>
                  <td><?= htmlspecialchars($row['keperluan']) ?></td>
                  <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                  <td><?= htmlspecialchars($row['keterangan']) ?></td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($data_keluar)): ?>
                <tr>
                  <td colspan="4" class="text-center text-muted">Belum ada data keuangan keluar.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

</body>
</html>
