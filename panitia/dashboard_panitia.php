<?php
session_start();
include '../db.php';  // Menghubungkan ke database

// Pastikan pengguna sudah login dan memiliki peran 'panitia'
if (!isset($_SESSION['user_nik']) || $_SESSION['role'] !== 'panitia') {
    header('Location: ../login.php');
    exit();
}

// Ambil data pengguna yang login
$user_nik = $_SESSION['user_nik'];
$query = $pdo->prepare("SELECT * FROM users WHERE nik = ?");
$query->execute([$user_nik]);
$user = $query->fetch();

// Ambil data tambahan untuk Panitia (misal, statistik qurban)
$ambil_data_qurban_sql = "SELECT * FROM pembagian_daging";
$ambil_data_qurban = $pdo->prepare($ambil_data_qurban_sql);
$ambil_data_qurban->execute();
$data_qurban = $ambil_data_qurban->fetchAll();

// Menghitung total berat daging yang dibagikan
$total_daging = 0;
$jumlah_daging_sudah_diambil = 0;
foreach ($data_qurban as $row) {
    $total_daging += $row['jumlah_kg'];
    if ($row['status_pengambilan'] == 'sudah') {
        $jumlah_daging_sudah_diambil += $row['jumlah_kg'];
    }
}

// Menghitung persentase pembagian daging
$persen_daging_terdistribusi = ($total_daging > 0) ? ($jumlah_daging_sudah_diambil / $total_daging) * 100 : 0;

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Panitia - Sistem Qurban</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .container-dashboard {
            max-width: 1200px;
            margin-top: 50px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color:rgb(13, 78, 23);
            color: white;
        }

        footer {
            margin-top: 50px;
            font-size: 14px;
            color: #777;
            text-align: center;
        }

        footer p {
            margin: 0;
        }

        .progress-bar {
            height: 30px;
        }
    </style>
</head>

<body>

    <div class="container-dashboard container">
        <!-- Tombol Kembali ke Dashboard -->
        <a href="../logout.php" class="btn btn-outline-primary mb-4">&lt; logout</a>

        <!-- Judul Dashboard -->
        <h2>Dashboard Panitia</h2>

        <!-- Statistik Pembagian Daging -->
        <div class="row mb-4">
            <!-- Total Daging yang Dibagikan -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Persentase Pembagian Daging</h5>
                    </div>
                    <div class="card-body">
                        <p><strong><?php echo number_format($persen_daging_terdistribusi, 2); ?>%</strong> dari total daging telah dibagikan.</p>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo $persen_daging_terdistribusi; ?>%" aria-valuenow="<?php echo $persen_daging_terdistribusi; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Qurban -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Statistik Qurban</h5>
                    </div>
                    <div class="card-body">
                        <h6>Total Berat Hewan Qurban yang Diterima tiap orang:</h6>
                        <p><?php echo $total_daging; ?> Kg</p>

                        <h6>Total Berat Daging yang Sudah Diterima oleh Warga:</h6>
                        <p><?php echo $jumlah_daging_sudah_diambil; ?> Kg</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Dashboard -->
        <div class="row mb-4">
            

            <!-- Card untuk Distribusi Daging -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Distribusi Daging</h5>
                    </div>
                    <div class="card-body">
                        <p>Melihat dan mengelola status pengambilan daging.</p>
                        <a href="../admin/meat_distribution.php" class="btn btn-primary">Kelola Pembagian Daging</a>
                    </div>
                </div>
            </div>

            <!-- Card untuk Laporan Keuangan -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Laporan Keuangan</h5>
                    </div>
                    <div class="card-body">
                        <p>Melihat dan menginput laporan keuangan qurban.</p>
                        <a href="../admin/financial_report.php" class="btn btn-primary">Lihat Laporan</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card untuk Inventaris -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Inventaris</h5>
                    </div>
                    <div class="card-body">
                        <p>Melihat dan mengelola barang-barang yang digunakan untuk qurban.</p>
                        <a href="manage_inventory.php" class="btn btn-primary">Kelola Inventaris</a>
                    </div>
                </div>
            </div>

            <!-- Card untuk Dokumentasi -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Dokumentasi Kegiatan</h5>
                    </div>
                    <div class="card-body">
                        <p>Upload foto atau video selama kegiatan qurban.</p>
                        <a href="upload_documentation.php" class="btn btn-primary">Upload Dokumentasi</a>
                    </div>
                </div>
            </div>

            <!-- Card untuk Scan QR Code -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Scan QR Code</h5>
                    </div>
                    <div class="card-body">
                        <p>Verifikasi pengambilan daging menggunakan QR Code.</p>
                        <a href="scan_qr.php" class="btn btn-primary">Scan QR Code</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <p>&copy; 2025 QURBANA: Sistem Qurban RT 001.</p>
        </footer>
    </div>

</body>

</html>
