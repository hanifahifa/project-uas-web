<?php
// Menghubungkan ke database
include '../db.php';
session_start();

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['user_nik']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Menangani form input keuangan
if (isset($_POST['submit'])) {
    $tanggal = $_POST['tanggal'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];

    if ($_GET['jenis'] == 'masuk') {
        $sumber = $_POST['sumber'];
        $sql = "INSERT INTO keuangan_masuk (tanggal, sumber, jumlah, keterangan) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tanggal, $sumber, $jumlah, $keterangan]);
    } elseif ($_GET['jenis'] == 'keluar') {
        $keperluan = $_POST['keperluan'];
        $sql = "INSERT INTO keuangan_keluar (tanggal, keperluan, jumlah, keterangan) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tanggal, $keperluan, $jumlah, $keterangan]);
    }

    // Redirect ke laporan keuangan dengan popup sukses
    echo "<script>
        alert('Data berhasil disimpan!');
        window.location.href = 'financial_report.php';  // Ganti dengan halaman yang sesuai
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Input Keuangan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h1>Input Keuangan</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
        </div>

        <?php if ($_GET['jenis'] == 'masuk') : ?>
            <div class="mb-3">
                <label for="sumber" class="form-label">Sumber</label>
                <input type="text" class="form-control" id="sumber" name="sumber" required>
            </div>
        <?php elseif ($_GET['jenis'] == 'keluar') : ?>
            <div class="mb-3">
                <label for="keperluan" class="form-label">Keperluan</label>
                <input type="text" class="form-control" id="keperluan" name="keperluan" required>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah</label>
            <input type="number" class="form-control" id="jumlah" name="jumlah" required>
        </div>

        <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan</label>
            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" required></textarea>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>

</body>
</html>
