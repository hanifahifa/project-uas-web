<?php
// Menghubungkan ke database
include '../db.php';
session_start();

// Hanya admin dan panitia yang dapat mengakses
if (!isset($_SESSION['user_nik']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'panitia')) {
  header('Location: ../login.php');
  exit;
}

// Cek apakah pengguna sudah login dan hanya admin/panitia yang dapat mengakses
if (!isset($_SESSION['user_nik']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'panitia')) {
  header('Location: ../login.php');
  exit;
}

// Tentukan URL untuk kembali ke dashboard berdasarkan peran pengguna
$dashboard_url = ($_SESSION['role'] == 'admin') ? '../admin/dashboard_admin.php' : '../panitia/dashboard_panitia.php';


// Ambil data pemasukan dari tabel hewan_qurban dengan join ke users
$stmt_pemasukan = $pdo->prepare("
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
");

// Pastikan query pemasukan berhasil sebelum eksekusi
if ($stmt_pemasukan) {
  $stmt_pemasukan->execute();
  $pemasukan_data = $stmt_pemasukan->fetchAll();
} else {
  echo "Query Pemasukan Error!";
  $pemasukan_data = [];
}

// Ambil data pengeluaran dari tabel keuangan_keluar
$stmt_pengeluaran = $pdo->prepare("
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
");

// Pastikan query pengeluaran berhasil sebelum eksekusi
if ($stmt_pengeluaran) {
  $stmt_pengeluaran->execute();
  $pengeluaran_data = $stmt_pengeluaran->fetchAll();
} else {
  echo "Query Pengeluaran Error!";
  $pengeluaran_data = [];
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
  </style>
</head>

<body>

  <div class="container">

    <!-- Tombol Kembali -->
    <a href="<?= $dashboard_url ?>" class="btn btn-secondary mb-4">&larr; Kembali ke Dashboard</a>

    <!-- Judul -->
    <h1>Laporan Keuangan</h1>
    <p>Data pemasukan dan pengeluaran dari iuran qurban.</p>

    <!-- Ringkasan Keuangan -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card bg-success text-white">
          <div class="card-body text-center">
            <h5>Total Pemasukan</h5>
            <h4>Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></h4>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-danger text-white">
          <div class="card-body text-center">
            <h5>Total Pengeluaran</h5>
            <h4>Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h4>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card <?= $saldo >= 0 ? 'bg-info' : 'bg-warning' ?> text-white">
          <div class="card-body text-center">
            <h5>Saldo</h5>
            <h4>Rp <?= number_format($saldo, 0, ',', '.') ?></h4>
          </div>
        </div>
      </div>
    </div>

    <!-- Tombol Input -->
    <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
      <a href="financial_input.php?jenis=masuk" class="btn btn-success">+ Input Keuangan Masuk</a>
      <a href="financial_input.php?jenis=keluar" class="btn btn-danger">+ Input Keuangan Keluar</a>
    </div>

    <!-- Laporan Keuangan -->
    <div class="card">
      <div class="card-header bg-success text-white">
        <h4 class="mb-0">Laporan Keuangan</h4>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered table-hover mb-0">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Hewan/Keperluan</th>
                <th>Sumber</th>
                <th>Jumlah</th>
                <th>Harga </th>
                <th>Total</th>
                <th>Biaya Admin</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($data_keuangan as $row): ?>
                <tr class="<?= $row['jenis_transaksi'] == 'Pemasukan' ? 'table-success' : 'table-danger' ?>">
                  <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
                  <td>
                    <span class="badge <?= $row['jenis_transaksi'] == 'Pemasukan' ? 'bg-success' : 'bg-danger' ?>">
                      <?= $row['jenis_transaksi'] ?>
                    </span>
                  </td>
                  <td><?= htmlspecialchars($row['jenis_hewan']) ?></td>
                  <td><?= htmlspecialchars($row['sumber']) ?></td>
                  <td class="text-center"><?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                  <td class="text-end">
                    <?php if ($row['harga_per_ekor']): ?>
                      Rp <?= number_format($row['harga_per_ekor'], 0, ',', '.') ?>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </td>
                  <td class="text-end">
                    <strong>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></strong>
                  </td>
                  <td class="text-end">
                    <?php if ($row['biaya_admin_per_ekor']): ?>
                      Rp <?= number_format($row['biaya_admin_per_ekor'], 0, ',', '.') ?>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
                </tr>
              <?php endforeach; ?>

              <?php if (empty($data_keuangan)): ?>
                <tr>
                  <td colspan="9" class="text-center text-muted">Belum ada data keuangan.</td>
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