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
  <title>Input Keuangan - Sistem Qurban</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
  <div class="container">
    <a href="financial_report.php" class="btn btn-secondary mb-4">&larr; Kembali ke Laporan</a>

    <h2 class="my-4">Input Transaksi Keuangan</h2>

    <form action="" method="POST">
      <div class="mb-3">
        <label for="tanggal" class="form-label">Tanggal</label>
        <input type="date" class="form-control" id="tanggal" name="tanggal" required />
      </div>

      <?php if ($jenis == 'masuk'): ?>
        <div class="mb-3">
          <label for="jenis_hewan" class="form-label">Jenis Hewan</label>
          <select class="form-control" id="jenis_hewan" name="jenis_hewan" required onchange="updateHargaBiayaAdmin()">
            <option value="">Pilih Jenis Hewan</option>
            <option value="sapi">Sapi</option>
            <option value="kambing">Kambing</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="jumlah_hewan" class="form-label">Jumlah Hewan</label>
          <input type="number" class="form-control" id="jumlah_hewan" name="jumlah_hewan" required min="1" />
        </div>

        <div class="mb-3">
          <label for="harga_per_ekor" class="form-label">Harga per Ekor (Rp)</label>
          <input type="number" class="form-control" id="harga_per_ekor" name="harga_per_ekor" required readonly />
        </div>

        <div class="mb-3">
          <label for="biaya_admin_per_ekor" class="form-label">Biaya Admin per Ekor (Rp)</label>
          <input type="number" class="form-control" id="biaya_admin_per_ekor" name="biaya_admin_per_ekor" required readonly />
        </div>

        <div class="mb-3">
          <label for="sumber" class="form-label">Sumber Dana</label>
          <select class="form-control" id="sumber" name="sumber" required>
            <option value="">Pilih Sumber Dana</option>
            <?php while ($row = mysqli_fetch_assoc($result_valid_sumber)): ?>
              <option value="<?= $row['nik'] ?>"><?= $row['nik'] ?> - <?= $row['name'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>

      <?php elseif ($jenis == 'keluar'): ?>
        <div class="mb-3">
          <label for="keperluan" class="form-label">Keperluan</label>
          <input type="text" class="form-control" id="keperluan" name="keperluan" required />
        </div>

        <div class="mb-3">
          <label for="jumlah" class="form-label">Jumlah</label>
          <input type="number" class="form-control" id="jumlah" name="jumlah" required min="1" />
        </div>

        <div class="mb-3">
          <label for="harga" class="form-label">Harga Total (Rp)</label>
          <input type="number" class="form-control" id="harga" name="harga" required min="1" />
        </div>

      <?php endif; ?>

      <div class="mb-3">
        <label for="keterangan" class="form-label">Keterangan</label>
        <textarea class="form-control" id="keterangan" name="keterangan"></textarea>
      </div>

      <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
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
