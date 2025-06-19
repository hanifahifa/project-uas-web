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
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <style>
    /* General Reset and Base Styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }

    body {
      background: linear-gradient(135deg, #4CAF50, #66BB6A, #81C784);
      min-height: 100vh;
      padding: 20px;
    }

    .dashboard-container {
      max-width: 1100px;
      margin: 0 auto;
      background: rgba(255, 255, 255, 0.97);
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1);
      padding: 40px;
      backdrop-filter: blur(10px);
    }

    /* Header Section */
    .header-section {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 25px 30px;
      background: linear-gradient(135deg, #4CAF50, #66BB6A, #81C784);
      color: #ffffff;
      border-radius: 18px;
      box-shadow: 0 8px 32px rgba(76, 175, 80, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
      margin-bottom: 25px;
      position: relative;
      overflow: auto;
    }

    .header-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.05) 50%, transparent 70%);
      pointer-events: none;
    }

    .welcome-text h1 {
      font-size: 1.8rem;
      font-weight: 700;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      color: #ffffff;
    }

    .welcome-text p {
      font-size: 1rem;
      opacity: 0.9;
      color: #f1f8e9;
    }

    .logout-btn,
    .btn-custom-secondary {
      display: flex;
      align-items: center;
      gap: 8px;
      background: linear-gradient(135deg, #ffffff, #f8f9fa);
      color: #4CAF50;
      padding: 12px 24px;
      border-radius: 25px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.8);
      border: 1px solid rgba(76, 175, 80, 0.1);
    }

    .logout-btn:hover,
    .btn-custom-secondary:hover {
      background: linear-gradient(135deg, #f8f9fa, #e9ecef);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
      color: #388E3C;
    }

    /* Buttons */
    .btn-custom-success {
      background: linear-gradient(135deg, #4CAF50, #66BB6A, #81C784);
      border: none;
      color: #ffffff;
      font-weight: 600;
      padding: 12px 24px;
      border-radius: 12px;
      box-shadow: 0 4px 16px rgba(76, 175, 80, 0.3);
      transition: all 0.3s ease;
    }

    .btn-custom-success:hover {
      background: linear-gradient(135deg, #66BB6A, #81C784, #A5D6A7);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
      color: #ffffff;
    }

    .btn-custom-danger {
      background: linear-gradient(135deg, #FF5722, #FF7043, #FF8A65);
      border: none;
      color: #ffffff;
      font-weight: 600;
      padding: 12px 24px;
      border-radius: 12px;
      box-shadow: 0 4px 16px rgba(255, 87, 34, 0.3);
      transition: all 0.3s ease;
    }

    .btn-custom-danger:hover {
      background: linear-gradient(135deg, #FF7043, #FF8A65, #FFAB91);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(255, 87, 34, 0.4);
      color: #ffffff;
    }

    /* Cards */
    .card-custom {
      border: none;
      border-radius: 18px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1), 0 1px 0 rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
      overflow: hidden;
      position: relative;
    }

    .card-custom:hover {
      transform: translateY(-8px);
      box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
    }

    .card-custom .card-header {
      background: none;
      border-bottom: none;
      font-weight: 700;
      color: rgba(255, 255, 255, 0.95);
      font-size: 0.95rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .card-custom .card-body .card-title {
      font-size: 1.6rem;
      font-weight: 800;
      color: rgba(255, 255, 255, 1);
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .bg-success {
      background: linear-gradient(135deg, #4CAF50, #66BB6A, #81C784) !important;
    }

    .bg-danger {
      background: linear-gradient(135deg, #FF5722, #FF7043, #FF8A65) !important;
    }

    .bg-info {
      background: linear-gradient(135deg, #2196F3, #42A5F5, #64B5F6) !important;
    }

    /* Table */
    .table-custom {
      margin-top: 25px;
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .table-custom .table {
      margin-bottom: 0;
      border-radius: 18px;
      overflow: hidden;
    }

    .table-custom th {
      background: linear-gradient(135deg, #4CAF50, #66BB6A);
      color: #ffffff;
      font-weight: 700;
      padding: 16px 12px;
      text-transform: uppercase;
      font-size: 0.85rem;
      letter-spacing: 0.5px;
      border: none;
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .table-custom td {
      vertical-align: middle;
      padding: 14px 12px;
      border-top: 1px solid rgba(76, 175, 80, 0.1);
      font-weight: 500;
    }

    .table-success {
      background: linear-gradient(135deg, rgba(76, 175, 80, 0.08), rgba(102, 187, 106, 0.12));
      border-left: 4px solid #4CAF50;
    }

    .table-danger {
      background: linear-gradient(135deg, rgba(255, 87, 34, 0.08), rgba(255, 112, 67, 0.12));
      border-left: 4px solid #FF5722;
    }

    .table-hover tbody tr:hover {
      background: rgba(76, 175, 80, 0.05) !important;
      transform: scale(1.01);
      transition: all 0.2s ease;
    }

    .text-end {
      font-family: 'Courier New', monospace;
      font-weight: 600;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .dashboard-container {
        padding: 25px;
        margin: 10px;
      }

      .header-section {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
        padding: 20px;
      }

      .welcome-text h1 {
        font-size: 1.5rem;
      }

      .card-custom .card-body .card-title {
        font-size: 1.3rem;
      }

      .table-custom th,
      .table-custom td {
        font-size: 0.85rem;
        padding: 10px 8px;
      }

      .btn-custom-success,
      .btn-custom-danger {
        padding: 10px 20px;
        font-size: 0.9rem;
      }
    }

    /* Additional subtle animations */
    .card-custom::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
      transition: left 0.6s ease;
    }

    .card-custom:hover::before {
      left: 100%;
    }
  </style>
</head>

<body>
  <div class="dashboard-container">
    <!-- Header Section -->
    <div class="header-section">
      <div class="welcome-text">
        <h1>Laporan Keuangan</h1>
      </div>
      <a href="<?= $dashboard_url ?>" class="btn btn-custom-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
      </a>
    </div>

    <!-- Tombol Tambah Transaksi -->
    <div class="mb-4">
      <a href="financial_input.php?jenis=masuk" class="btn btn-custom-success me-2">
        <i class="fas fa-plus me-2"></i>Tambah Pemasukan
      </a>
      <a href="financial_input.php?jenis=keluar" class="btn btn-custom-danger">
        <i class="fas fa-minus me-2"></i>Tambah Pengeluaran
      </a>
    </div>

    <!-- Statistik Keuangan -->
    <div class="row">
      <div class="col-md-4 mb-3">
        <div class="card card-custom text-white bg-success">
          <div class="card-header">Total Pemasukan</div>
          <div class="card-body">
            <h5 class="card-title">Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></h5>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card card-custom text-white bg-danger">
          <div class="card-header">Total Pengeluaran</div>
          <div class="card-body">
            <h5 class="card-title">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h5>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card card-custom text-white bg-info">
          <div class="card-header">Saldo Tersisa</div>
          <div class="card-body">
            <h5 class="card-title">Rp <?= number_format($saldo, 0, ',', '.') ?></h5>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabel Keuangan -->
    <div class="table-custom">
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
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>