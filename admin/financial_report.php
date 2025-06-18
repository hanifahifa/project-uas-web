<?php
// Menghubungkan ke database menggunakan MySQLi
include '../db.php';
session_start();

// Hanya admin dan panitia yang dapat mengakses
// if (!isset($_SESSION['user_nik']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'panitia')) {
//   header('Location: ../login.php');
//   exit;
// }

// Tentukan URL untuk kembali ke dashboard berdasarkan peran pengguna
$dashboard_url = ($_SESSION['role'] == 'admin') ? '../admin/dashboard_admin.php' : '../panitia/dashboard_panitia.php';

// Ambil data pemasukan dari tabel hewan_qurban dengan join ke users
$query_pemasukan = "
    SELECT 
        hq.created_at AS tanggal, 
        CONCAT(UPPER(SUBSTRING(hq.jenis, 1, 1)), LOWER(SUBSTRING(hq.jenis, 2))) AS jenis_hewan, 
        hq.jumlah, 
        hq.harga_per_ekor, 
        hq.biaya_admin_per_ekor, 
        (hq.harga_per_ekor * hq.jumlah) AS total_harga,
        'Pemasukan' AS jenis_transaksi, 
        hq.keterangan,
        CONCAT(u.nik, ' - ', u.name) AS sumber
    FROM hewan_qurban hq
    LEFT JOIN users u ON hq.sumber = u.nik
    ORDER BY hq.created_at DESC
";
$result_pemasukan = mysqli_query($conn, $query_pemasukan);

$pemasukan_data = [];
if ($result_pemasukan) {
  while ($row = mysqli_fetch_assoc($result_pemasukan)) {
    $pemasukan_data[] = $row;
  }
} else {
  echo "Query Pemasukan Error!";
}

// Ambil data pengeluaran dari tabel keuangan_keluar
$query_pengeluaran = "
    SELECT 
        kk.tanggal, 
        kk.keperluan AS jenis_hewan, 
        kk.jumlah, 
        kk.harga AS harga_per_ekor,
        NULL AS biaya_admin_per_ekor,
        kk.harga AS total_harga,
        'Pengeluaran' AS jenis_transaksi, 
        kk.keterangan,
        '-' AS sumber
    FROM keuangan_keluar kk
    ORDER BY kk.tanggal DESC
";
$result_pengeluaran = mysqli_query($conn, $query_pengeluaran);

$pengeluaran_data = [];
if ($result_pengeluaran) {
  while ($row = mysqli_fetch_assoc($result_pengeluaran)) {
    $pengeluaran_data[] = $row;
  }
} else {
  echo "Query Pengeluaran Error!";
}

// Gabungkan hasil dari kedua query dan urutkan berdasarkan tanggal
$data_keuangan = array_merge($pemasukan_data, $pengeluaran_data);

// Urutkan berdasarkan tanggal (terbaru dulu)
usort($data_keuangan, function ($a, $b) {
  return strtotime($b['tanggal']) - strtotime($a['tanggal']);
});

// Hitung total pemasukan dan pengeluaran
$total_pemasukan = 0;
$total_pengeluaran = 0;

foreach ($data_keuangan as $row) {
  if ($row['jenis_transaksi'] == 'Pemasukan') {
    $total_pemasukan += $row['total_harga'];
  } else {
    $total_pengeluaran += $row['total_harga'];
  }
}

$saldo = $total_pemasukan - $total_pengeluaran;



?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan Keuangan - QURBANA</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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

    .btn-success {
      background-color: #0e6e3d;
      border: none;
    }

    .btn-success:hover {
      background-color: #138c50;
    }

    .btn-danger {
      background-color: #d9534f;
      border: none;
    }

    .btn-danger:hover {
      background-color: #c9302c;
    }

    .btn-secondary {
      background-color: #7f8c8d;
      border: none;
    }

    .btn-secondary:hover {
      background-color: #6c7a7b;
    }

    .table {
      margin-bottom: 0;
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Tombol Kembali ke Dashboard -->
    <a href="<?= $dashboard_url ?>" class="btn btn-secondary mb-4">
      <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
    </a>

    <!-- Tombol Tambah Transaksi -->
    <a href="financial_input.php?jenis=masuk" class="btn btn-success mb-4">
      <i class="fas fa-plus me-2"></i>Tambah Pemasukan
    </a>

    <a href="financial_input.php?jenis=keluar" class="btn btn-danger mb-4">
      <i class="fas fa-minus me-2"></i>Tambah Pengeluaran
    </a>

    <h2>Laporan Keuangan</h2>
    <div class="row">
      <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
          <div class="card-header">Total Pemasukan</div>
          <div class="card-body">
            <h5 class="card-title">Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></h5>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-white bg-danger mb-3">
          <div class="card-header">Total Pengeluaran</div>
          <div class="card-body">
            <h5 class="card-title">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h5>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-white bg-info mb-3">
          <div class="card-header">Saldo Tersisa</div>
          <div class="card-body">
            <h5 class="card-title">Rp <?= number_format($saldo, 0, ',', '.') ?></h5>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabel Keuangan -->
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Tanggal</th>
          <th>Jenis</th>
          <th>Hewan/Keperluan</th>
          <th>Sumber</th>
          <th>Jumlah</th>
          <th>Harga</th>
          <th>Total</th>
          <th>Biaya Admin</th>
          <th>Keterangan</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data_keuangan as $row): ?>
          <tr class="<?= $row['jenis_transaksi'] == 'Pemasukan' ? 'table-success' : 'table-danger' ?>">
            <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
            <td><?= $row['jenis_transaksi'] ?></td>
            <td><?= htmlspecialchars($row['jenis_hewan'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['sumber'] ?? '') ?></td>
            <td class="text-center"><?= number_format($row['jumlah'], 0, ',', '.') ?></td>
            <td class="text-end"><?= number_format($row['harga_per_ekor'] ?? 0, 0, ',', '.') ?></td>
            <td class="text-end"><?= number_format($row['total_harga'] ?? 0, 0, ',', '.') ?></td>
            <td class="text-end"><?= number_format($row['biaya_admin_per_ekor'] ?? 0, 0, ',', '.') ?></td>
            <td><?= htmlspecialchars($row['keterangan'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>

    </table>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>