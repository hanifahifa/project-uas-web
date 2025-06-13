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
  <title>Laporan Keuangan - QURBANA</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-dark: #1a4f2e;
      --primary-medium: #8fbc8f;
      --primary-light: #e8f5e8;
      --accent: #f4d4a7;
      --text-dark: #2c3e50;
      --text-light: #6c757d;
      --white: #ffffff;
      
      /* Warna hijau untuk pemasukan */
      --success-light: #e8f5e9;
      --success-medium: #6BCB77;
      --success-dark: #4a9c54;
      
      /* Warna merah untuk pengeluaran */
      --danger-light: #ffeaea;
      --danger-medium: #FB4141;
      --danger-dark: #c93030;
      
      /* Warna biru untuk saldo positif */
      --info-light: #f0f7ff;
      --info-medium: #4D96FF;
      --info-dark: #3a75cc;
      
      /* Warna orange untuk saldo negatif */
      --warning-light: #fff3cd;
      --warning-medium: #fd7e14;
      --warning-dark: #8b4513;
      
      --border-radius: 16px;
      --shadow-soft: 0 4px 20px rgba(26, 79, 46, 0.08);
      --shadow-medium: 0 8px 32px rgba(26, 79, 46, 0.12);
      --shadow-strong: 0 12px 40px rgba(26, 79, 46, 0.16);
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: linear-gradient(135deg, var(--primary-light) 0%, #f8fffe 100%);
      min-height: 100vh;
      color: var(--text-dark);
    }

    .container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 2rem 1rem;
    }

    /* Header Section */
    .header-section {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2.5rem;
      background: var(--white);
      padding: 1.5rem 2rem;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-soft);
    }

    .page-title h1 {
      font-size: 2.2rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 0;
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .page-title p {
      color: var(--text-light);
      margin: 0.5rem 0 0 0;
      font-size: 0.95rem;
    }

    .page-icon {
      width: 56px;
      height: 56px;
      background: linear-gradient(135deg, var(--primary-light), var(--primary-medium));
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary-dark);
      font-size: 1.75rem;
      box-shadow: var(--shadow-soft);
    }

    .back-btn {
      background: linear-gradient(135deg, var(--primary-medium), var(--primary-dark));
      color: var(--white);
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 12px;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      box-shadow: var(--shadow-soft);
    }

    .back-btn:hover {
      background: linear-gradient(135deg, var(--primary-dark), #0d2818);
      color: var(--white);
      transform: translateY(-2px);
      box-shadow: var(--shadow-medium);
    }

    /* Stats Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
      margin-bottom: 3rem;
    }

    .stat-card {
      background: var(--white);
      border-radius: var(--border-radius);
      padding: 2rem;
      box-shadow: var(--shadow-soft);
      border: none;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-medium);
    }

    .stat-card.success::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--success-medium), var(--success-dark));
    }

    .stat-card.danger::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--danger-medium), var(--danger-dark));
    }

    .stat-card.info::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--info-medium), var(--info-dark));
    }

    .stat-card.warning::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--warning-medium), var(--warning-dark));
    }

    .stat-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.5rem;
    }

    .stat-icon {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      box-shadow: var(--shadow-soft);
    }

    .stat-icon.success {
      background: linear-gradient(135deg, var(--success-light), var(--success-medium));
      color: var(--success-dark);
    }

    .stat-icon.danger {
      background: linear-gradient(135deg, var(--danger-light), var(--danger-medium));
      color: var(--danger-dark);
    }

    .stat-icon.info {
      background: linear-gradient(135deg, var(--info-light), var(--info-medium));
      color: var(--info-dark);
    }

    .stat-icon.warning {
      background: linear-gradient(135deg, var(--warning-light), var(--warning-medium));
      color: var(--warning-dark);
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }

    .stat-value.success {
      color: var(--success-dark);
    }

    .stat-value.danger {
      color: var(--danger-dark);
    }

    .stat-value.info {
      color: var(--info-dark);
    }

    .stat-value.warning {
      color: var(--warning-dark);
    }

    .stat-label {
      color: var(--text-light);
      font-size: 0.95rem;
      font-weight: 500;
    }

    /* Action Buttons */
    .actions-section {
      margin-bottom: 2.5rem;
    }

    .actions-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      justify-content: center;
    }

    .action-btn {
      border: none;
      padding: 0.875rem 1.5rem;
      border-radius: 12px;
      text-decoration: none;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
      gap: 0.75rem;
      transition: all 0.3s ease;
      font-size: 0.95rem;
      box-shadow: var(--shadow-soft);
    }

    .action-btn.success {
      background: linear-gradient(135deg, var(--success-medium), var(--success-dark));
      color: var(--white);
    }

    .action-btn.success:hover {
      background: linear-gradient(135deg, var(--success-dark), #0f3d19);
      color: var(--white);
      transform: translateY(-1px);
      box-shadow: var(--shadow-medium);
    }

    .action-btn.danger {
      background: linear-gradient(135deg, var(--danger-medium), var(--danger-dark));
      color: var(--white);
    }

    .action-btn.danger:hover {
      background: linear-gradient(135deg, var(--danger-dark), #4a1016);
      color: var(--white);
      transform: translateY(-1px);
      box-shadow: var(--shadow-medium);
    }

    /* Table Section */
    .table-section {
      background: var(--white);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-soft);
      overflow: hidden;
    }

    .table-header {
      background: linear-gradient(135deg, var(--primary-dark), var(--primary-medium));
      color: var(--white);
      padding: 1.5rem 2rem;
      margin: 0;
    }

    .table-header h4 {
      margin: 0;
      font-size: 1.3rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .table-responsive {
      max-height: 70vh;
      overflow-y: auto;
    }

    .table {
      margin: 0;
      font-size: 0.9rem;
    }

    .table th {
      background: var(--primary-dark);
      color: var(--white);
      border: none;
      padding: 1rem 0.75rem;
      font-weight: 600;
      text-align: center;
      position: sticky;
      top: 0;
      z-index: 10;
    }

    .table td {
      padding: 1rem 0.75rem;
      vertical-align: middle;
      border-bottom: 1px solid #e9ecef;
    }

    .table tbody tr:hover {
      background-color: rgba(26, 79, 46, 0.02);
    }

    .table .row-success {
      background: linear-gradient(90deg, var(--success-light), rgba(212, 237, 218, 0.5));
      border-left: 4px solid var(--success-medium);
    }

    .table .row-danger {
      background: linear-gradient(90deg, var(--danger-light), rgba(248, 215, 218, 0.5));
      border-left: 4px solid var(--danger-medium);
    }

    .badge {
      padding: 0.5rem 0.75rem;
      border-radius: 8px;
      font-size: 0.8rem;
      font-weight: 500;
      box-shadow: var(--shadow-soft);
    }

    .badge.bg-success {
      background: linear-gradient(135deg, var(--success-medium), var(--success-dark)) !important;
    }

    .badge.bg-danger {
      background: linear-gradient(135deg, var(--danger-medium), var(--danger-dark)) !important;
    }

    .empty-state {
      text-align: center;
      padding: 3rem 2rem;
      color: var(--text-light);
    }

    .empty-state i {
      font-size: 3rem;
      margin-bottom: 1rem;
      opacity: 0.5;
    }

    /* Admin fee text styling */
    .biaya-admin-text {
      color: var(--text-dark) !important;
      font-weight: 500;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .header-section {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
      }

      .page-title h1 {
        font-size: 1.8rem;
        flex-direction: column;
        gap: 0.5rem;
      }

      .container {
        padding: 1rem;
      }

      .stats-grid {
        grid-template-columns: 1fr;
      }

      .actions-grid {
        flex-direction: column;
      }

      .table-responsive {
        font-size: 0.8rem;
      }

      .table th,
      .table td {
        padding: 0.5rem 0.25rem;
      }
    }

    @media (max-width: 576px) {
      .page-title h1 {
        font-size: 1.5rem;
      }

      .stat-value {
        font-size: 1.5rem;
      }

      .table {
        font-size: 0.75rem;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Header Section -->
    <div class="header-section">
      <div class="page-title">
        <h1>
          <div class="page-icon">
            <i class="fas fa-chart-line"></i>
          </div>
          Laporan Keuangan
        </h1>
        <p>Pantau pemasukan dan pengeluaran dari iuran qurban secara real-time</p>
      </div>
      <a href="<?= $dashboard_url ?>" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        Kembali ke Dashboard
      </a>
    </div>

    <!-- Statistics Section -->
    <div class="stats-grid">
      <!-- Total Pemasukan -->
      <div class="stat-card success">
        <div class="stat-header">
          <div class="stat-icon success">
            <i class="fas fa-arrow-up"></i>
          </div>
        </div>
        <div class="stat-value success">Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></div>
        <div class="stat-label">Total Pemasukan</div>
      </div>

      <!-- Total Pengeluaran -->
      <div class="stat-card danger">
        <div class="stat-header">
          <div class="stat-icon danger">
            <i class="fas fa-arrow-down"></i>
          </div>
        </div>
        <div class="stat-value danger">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></div>
        <div class="stat-label">Total Pengeluaran</div>
      </div>

      <!-- Saldo -->
      <div class="stat-card <?= $saldo >= 0 ? 'info' : 'warning' ?>">
        <div class="stat-header">
          <div class="stat-icon <?= $saldo >= 0 ? 'info' : 'warning' ?>">
            <i class="fas fa-wallet"></i>
          </div>
        </div>
        <div class="stat-value <?= $saldo >= 0 ? 'info' : 'warning' ?>">Rp <?= number_format($saldo, 0, ',', '.') ?></div>
        <div class="stat-label">Saldo Tersisa</div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="actions-section">
      <div class="actions-grid">
        <a href="financial_input.php?jenis=masuk" class="action-btn success">
          <i class="fas fa-plus-circle"></i>
          Input Keuangan Masuk
        </a>
        <a href="financial_input.php?jenis=keluar" class="action-btn danger">
          <i class="fas fa-minus-circle"></i>
          Input Keuangan Keluar
        </a>
      </div>
    </div>

    <!-- Financial Report Table -->
    <div class="table-section">
      <div class="table-header">
        <h4>
          <i class="fas fa-table"></i>
          Detail Transaksi Keuangan
        </h4>
      </div>
      <div class="table-responsive">
        <?php if (!empty($data_keuangan)): ?>
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th><i class="fas fa-calendar-alt me-2"></i>Tanggal</th>
                <th><i class="fas fa-tag me-2"></i>Jenis</th>
                <th><i class="fas fa-list me-2"></i>Hewan/Keperluan</th>
                <th><i class="fas fa-user me-2"></i>Sumber</th>
                <th><i class="fas fa-sort-numeric-up me-2"></i>Jumlah</th>
                <th><i class="fas fa-money-bill me-2"></i>Harga</th>
                <th><i class="fas fa-calculator me-2"></i>Total</th>
                <th><i class="fas fa-percentage me-2"></i>Biaya Admin</th>
                <th><i class="fas fa-sticky-note me-2"></i>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($data_keuangan as $row): ?>
                <tr class="<?= $row['jenis_transaksi'] == 'Pemasukan' ? 'row-success' : 'row-danger' ?>">
                  <td class="fw-medium"><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
                  <td>
                    <span class="badge <?= $row['jenis_transaksi'] == 'Pemasukan' ? 'bg-success' : 'bg-danger' ?>">
                      <i class="fas fa-<?= $row['jenis_transaksi'] == 'Pemasukan' ? 'plus' : 'minus' ?> me-1"></i>
                      <?= $row['jenis_transaksi'] ?>
                    </span>
                  </td>
                  <td class="fw-medium"><?= htmlspecialchars($row['jenis_hewan']) ?></td>
                  <td class="text-muted"><?= htmlspecialchars($row['sumber']) ?></td>
                  <td class="text-center fw-bold"><?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                  <td class="text-end">
                    <?php if ($row['harga_per_ekor']): ?>
                      <span class="fw-medium">Rp <?= number_format($row['harga_per_ekor'], 0, ',', '.') ?></span>
                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-end">
                    <strong class="fs-6">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></strong>
                  </td>
                  <td class="text-end">
                    <?php if ($row['biaya_admin_per_ekor']): ?>
                      <span class="biaya-admin-text">Rp <?= number_format($row['biaya_admin_per_ekor'], 0, ',', '.') ?></span>
                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-muted"><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="empty-state">
            <i class="fas fa-chart-line"></i>
            <h5>Belum Ada Data Keuangan</h5>
            <p>Silakan tambahkan transaksi keuangan untuk melihat laporan.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>