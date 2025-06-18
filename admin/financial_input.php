<?php
// Menghubungkan ke database
include '../db.php';
session_start();

// Hanya admin dan panitia yang dapat mengakses
// if (!isset($_SESSION['user_nik']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'panitia')) {
//     header('Location: ../login.php');
//     exit;
// }

// Query untuk mengambil pengguna yang memiliki role 'berqurban' dan belum digunakan
$query_valid_sumber = "
    SELECT u.nik, u.name
    FROM users u
    INNER JOIN user_roles ur ON u.nik = ur.nik
    WHERE ur.role = 'berqurban' 
    AND NOT EXISTS (
        SELECT 1 FROM hewan_qurban hq WHERE hq.sumber = u.nik
    )
    ORDER BY u.name
";
$result_valid_sumber = mysqli_query($conn, $query_valid_sumber);

// Tentukan jenis input (masuk atau keluar)
$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : '';

// Tentukan URL untuk kembali ke dashboard berdasarkan peran pengguna
$dashboard_url = ($_SESSION['role'] == 'admin') ? '../admin/dashboard_admin.php' : '../panitia/dashboard_panitia.php';

// Proses saat form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];

    if ($jenis == 'masuk') {
        $jenis_hewan = $_POST['jenis_hewan'];
        $jumlah_hewan = $_POST['jumlah_hewan'];
        $harga_per_ekor = $_POST['harga_per_ekor'];
        $biaya_admin_per_ekor = $_POST['biaya_admin_per_ekor'];
        $sumber = $_POST['sumber'];

        $query = "INSERT INTO hewan_qurban (jenis, jumlah, harga_per_ekor, biaya_admin_per_ekor, sumber, created_at, keterangan) 
                  VALUES ('$jenis_hewan', '$jumlah_hewan', '$harga_per_ekor', '$biaya_admin_per_ekor', '$sumber', NOW(), '$keterangan')";
        mysqli_query($conn, $query);

        // Redirect ke halaman laporan setelah data disimpan
        header('Location: financial_report.php');
        exit;
    } elseif ($jenis == 'keluar') {
        $keperluan = $_POST['keperluan'];
        $jumlah = $_POST['jumlah'];
        $harga = $_POST['harga'];

        $query = "INSERT INTO keuangan_keluar (tanggal, keperluan, jumlah, harga, keterangan) 
                  VALUES ('$tanggal', '$keperluan', '$jumlah', '$harga', '$keterangan')";
        mysqli_query($conn, $query);

        // Redirect ke halaman laporan setelah data disimpan
        header('Location: financial_report.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Input Keuangan - QURBANA</title>
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
      overflow: hidden;
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

    /* Form Styles */
    .form-label {
      color: #2E7D32;
      font-weight: 600;
      margin-bottom: 8px;
    }

    .form-control {
      border-radius: 12px !important;
      border: 2px solid #E8F5E9 !important;
      padding: 12px !important;
      transition: all 0.3s ease !important;
      font-weight: 500;
    }

    .form-control:focus {
      border-color: #4CAF50 !important;
      box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25) !important;
    }

    .form-control[readonly] {
      background: #F8F9FA !important;
      color: #495057;
    }

    textarea.form-control {
      resize: vertical;
      min-height: 80px;
    }

    .btn-lg {
      padding: 15px 40px !important;
      font-size: 1.1rem !important;
      font-weight: 700 !important;
      border-radius: 15px !important;
    }
  </style>
</head>

<body>
  <div class="dashboard-container">
    <!-- Header Section -->
    <div class="header-section">
      <div class="welcome-text">
        <h1>Input Transaksi Keuangan</h1>
        <p><?= $jenis == 'masuk' ? 'Tambah Pemasukan' : 'Tambah Pengeluaran' ?></p>
      </div>
      <a href="financial_report.php" class="btn btn-custom-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali ke Laporan
      </a>
    </div>

    <!-- Form Section -->
    <form action="" method="POST">
      <div class="mb-3">
        <label for="tanggal" class="form-label fw-bold">Tanggal</label>
        <input type="date" class="form-control" id="tanggal" name="tanggal" required />
      </div>

      <?php if ($jenis == 'masuk'): ?>
        <div class="mb-3">
          <label for="jenis_hewan" class="form-label fw-bold">Jenis Hewan</label>
          <select class="form-control" id="jenis_hewan" name="jenis_hewan" required onchange="updateHargaBiayaAdmin()">
            <option value="">Pilih Jenis Hewan</option>
            <option value="sapi">Sapi</option>
            <option value="kambing">Kambing</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="jumlah_hewan" class="form-label fw-bold">Jumlah Hewan</label>
          <input type="number" class="form-control" id="jumlah_hewan" name="jumlah_hewan" required min="1" />
        </div>

        <div class="mb-3">
          <label for="harga_per_ekor" class="form-label fw-bold">Harga per Ekor (Rp)</label>
          <input type="number" class="form-control" id="harga_per_ekor" name="harga_per_ekor" required readonly />
        </div>

        <div class="mb-3">
          <label for="biaya_admin_per_ekor" class="form-label fw-bold">Biaya Admin per Ekor (Rp)</label>
          <input type="number" class="form-control" id="biaya_admin_per_ekor" name="biaya_admin_per_ekor" required readonly />
        </div>

        <div class="mb-3">
          <label for="sumber" class="form-label fw-bold">Sumber Dana</label>
          <select class="form-control" id="sumber" name="sumber" required>
            <option value="">Pilih Sumber Dana</option>
            <?php while ($row = mysqli_fetch_assoc($result_valid_sumber)): ?>
              <option value="<?= $row['nik'] ?>"><?= $row['nik'] ?> - <?= $row['name'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>

      <?php elseif ($jenis == 'keluar'): ?>
        <div class="mb-3">
          <label for="keperluan" class="form-label fw-bold">Keperluan</label>
          <input type="text" class="form-control" id="keperluan" name="keperluan" required />
        </div>

        <div class="mb-3">
          <label for="jumlah" class="form-label fw-bold">Jumlah</label>
          <input type="number" class="form-control" id="jumlah" name="jumlah" required min="1" />
        </div>

        <div class="mb-3">
          <label for="harga" class="form-label fw-bold">Harga Total (Rp)</label>
          <input type="number" class="form-control" id="harga" name="harga" required min="1" />
        </div>

      <?php endif; ?>

      <div class="mb-3">
        <label for="keterangan" class="form-label fw-bold">Keterangan</label>
        <textarea class="form-control" id="keterangan" name="keterangan"></textarea>
      </div>

      <div class="text-center mt-4">
        <button type="submit" class="btn btn-custom-success btn-lg px-5">
          <i class="fas fa-save me-2"></i>Simpan Transaksi
        </button>
      </div>
    </form>
  </div>

  <script>
    function updateHargaBiayaAdmin() {
        var jenisHewan = document.getElementById('jenis_hewan').value;
        var hargaPerEkor, biayaAdminPerEkor;

        // Tentukan harga dan biaya admin berdasarkan jenis hewan
        if (jenisHewan == 'sapi') {
            hargaPerEkor = 3000000; // Rp 3.000.000
            biayaAdminPerEkor = 100000; // Rp 100.000
        } else if (jenisHewan == 'kambing') {
            hargaPerEkor = 2700000; // Rp 2.700.000
            biayaAdminPerEkor = 50000; // Rp 50.000
        } else {
            hargaPerEkor = 0;
            biayaAdminPerEkor = 0;
        }

        // Set harga dan biaya admin ke input field
        document.getElementById('harga_per_ekor').value = hargaPerEkor;
        document.getElementById('biaya_admin_per_ekor').value = biayaAdminPerEkor;
    }

    // Auto set tanggal hari ini
    document.addEventListener('DOMContentLoaded', function() {
        var today = new Date().toISOString().split('T')[0];
        document.getElementById('tanggal').value = today;
    });
  </script>
</body>

</html>
