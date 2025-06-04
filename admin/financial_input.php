<?php
// Menghubungkan ke database
include '../db.php';
session_start();

// Hanya admin dan panitia yang dapat mengakses
if (!isset($_SESSION['user_nik']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'panitia')) {
    header('Location: ../login.php');
    exit;
}

// Ambil data users dengan role 'berqurban' untuk dropdown sumber
$stmt_users = $pdo->prepare("SELECT nik, name FROM users WHERE role = 'berqurban' ORDER BY name");
$stmt_users->execute();
$users = $stmt_users->fetchAll();

// Tentukan jenis input (masuk atau keluar)
$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : '';

// Proses saat form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pastikan data POST ada sebelum diproses
    $tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : ''; // Cek apakah tanggal ada
    $keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : ''; // Cek apakah keterangan ada

    if ($jenis == 'masuk') {
        // Data keuangan masuk (pemasukan) yang masuk ke hewan_qurban
        $jenis_hewan = isset($_POST['jenis_hewan']) ? $_POST['jenis_hewan'] : ''; // Cek jenis hewan
        $jumlah_hewan = isset($_POST['jumlah_hewan']) ? $_POST['jumlah_hewan'] : 0; // Cek jumlah hewan
        $harga_per_ekor = isset($_POST['harga_per_ekor']) ? $_POST['harga_per_ekor'] : 0; // Cek harga per ekor
        $biaya_admin_per_ekor = isset($_POST['biaya_admin_per_ekor']) ? $_POST['biaya_admin_per_ekor'] : 0; // Cek biaya admin
        $sumber = isset($_POST['sumber']) ? $_POST['sumber'] : ''; // Cek sumber

        // Query untuk memasukkan data ke tabel hewan_qurban
        $stmt = $pdo->prepare("INSERT INTO hewan_qurban (jenis, jumlah, harga_per_ekor, biaya_admin_per_ekor, sumber, created_at, keterangan) 
                               VALUES (?, ?, ?, ?, ?, NOW(), ?)");

        $stmt->execute([$jenis_hewan, $jumlah_hewan, $harga_per_ekor, $biaya_admin_per_ekor, $sumber, $keterangan]);

        // Redirect ke halaman laporan setelah data disimpan
        header('Location: financial_report.php');
        exit;
    } elseif ($jenis == 'keluar') {
        // Data keuangan keluar (pengeluaran) yang masuk ke keuangan_keluar
        $keperluan = isset($_POST['keperluan']) ? $_POST['keperluan'] : ''; // Cek keperluan
        $jumlah = isset($_POST['jumlah']) ? $_POST['jumlah'] : 0; // Cek jumlah
        $harga = isset($_POST['harga']) ? $_POST['harga'] : 0; // Cek harga

        // Query untuk memasukkan data ke tabel keuangan_keluar
        $stmt = $pdo->prepare("INSERT INTO keuangan_keluar (tanggal, keperluan, jumlah, harga, keterangan) 
                               VALUES (?, ?, ?, ?, ?)");

        $stmt->execute([$tanggal . ' ' . date('H:i:s'), $keperluan, $jumlah, $harga, $keterangan]);

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
    <style>
        /* Styling halaman */
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

        .btn-secondary {
            background-color: #7f8c8d;
            border: none;
        }

        .card {
            border: none;
            margin-top: 30px;
            border-radius: 16px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            border-radius: 16px 16px 0 0;
        }

        .table {
            margin-bottom: 0;
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Tombol Kembali -->
        <a href="financial_report.php" class="btn btn-secondary mb-4">&larr; Kembali ke Laporan</a>

        <!-- Judul -->
        <h1>Input Keuangan</h1>

        <!-- Form untuk Pemasukan atau Pengeluaran -->
        <form action="" method="POST">
            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
            </div>

            <?php if ($jenis == 'masuk'): ?>
                <!-- Form untuk Pemasukan -->
                <div class="mb-3">
                    <label for="jenis_hewan" class="form-label">Jenis Hewan:</label>
                    <select class="form-control" id="jenis_hewan" name="jenis_hewan" required onchange="updateHargaBiayaAdmin()">
                        <option value="">Pilih Jenis Hewan</option>
                        <option value="sapi">Sapi</option>
                        <option value="kambing">Kambing</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="jumlah_hewan" class="form-label">Jumlah Hewan:</label>
                    <input type="number" class="form-control" id="jumlah_hewan" name="jumlah_hewan" required min="1">
                </div>

                <div class="mb-3">
                    <label for="harga_per_ekor" class="form-label">Harga per Ekor (Rp):</label>
                    <input type="number" class="form-control" id="harga_per_ekor" name="harga_per_ekor" required readonly>
                </div>

                <div class="mb-3">
                    <label for="biaya_admin_per_ekor" class="form-label">Biaya Admin per Ekor (Rp):</label>
                    <input type="number" class="form-control" id="biaya_admin_per_ekor" name="biaya_admin_per_ekor" required readonly>
                </div>

                <div class="mb-3">
                    <label for="sumber" class="form-label">Sumber Dana:</label>
                    <select class="form-control" id="sumber" name="sumber" required>
                        <option value="">Pilih Sumber Dana</option>
                        <?php if (empty($users)): ?>
                            <option value="" disabled>Tidak ada user dengan role berqurban</option>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['nik'] ?>"><?= htmlspecialchars($user['nik'] . ' - ' . $user['name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

            <?php elseif ($jenis == 'keluar'): ?>
                <!-- Form untuk Pengeluaran -->
                <div class="mb-3">
                    <label for="keperluan" class="form-label">Keperluan:</label>
                    <input type="text" class="form-control" id="keperluan" name="keperluan" required>
                </div>

                <div class="mb-3">
                    <label for="jumlah" class="form-label">Jumlah/Qty:</label>
                    <input type="number" class="form-control" id="jumlah" name="jumlah" required min="1">
                </div>

                <div class="mb-3">
                    <label for="harga" class="form-label">Harga Total (Rp):</label>
                    <input type="number" class="form-control" id="harga" name="harga" required min="1">
                </div>

            <?php endif; ?>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Data</button>
        </form>
    </div>

    <script>
        function updateHargaBiayaAdmin() {
            var jenisHewan = document.getElementById('jenis_hewan').value;
            var hargaPerEkor, biayaAdminPerEkor;

            // Tentukan harga dan biaya admin berdasarkan jenis hewan
            if (jenisHewan == 'sapi') {
                hargaPerEkor = 3000000; // Rp 3.000.000
                biayaAdminPerEkor = 100000; // Rp 700.000
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