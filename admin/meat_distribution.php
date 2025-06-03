<?php
include '../db.php';  // Menghubungkan ke database
session_start();

// Pastikan hanya admin atau panitia yang dapat mengakses halaman ini
if (!isset($_SESSION['user_nik']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'panitia')) {
    header('Location: ../login.php');
    exit;
}

// Menentukan halaman dashboard yang tepat berdasarkan peran
$dashboard_url = ($_SESSION['role'] == 'admin') ? '../admin/dashboard_admin.php' : '../panitia/dashboard_panitia.php';

// Cek jika ada parameter delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Ambil nik dari pembagian_daging untuk menghapus data pengguna
    $query_nik = $pdo->prepare("SELECT nik FROM pembagian_daging WHERE id = ?");
    $query_nik->execute([$id]);
    $nik = $query_nik->fetchColumn();  // Ambil NIK dari pembagian_daging
    
    // Hapus gambar QR Code terlebih dahulu
    $query_delete = $pdo->prepare("SELECT qr_code FROM pembagian_daging WHERE id = ?");
    $query_delete->execute([$id]);
    $row = $query_delete->fetch();
    
    if ($row && file_exists($row['qr_code'])) {
        unlink($row['qr_code']);  // Menghapus file QR Code
    }
    
    // Hapus data dari tabel pembagian_daging
    $delete_query = $pdo->prepare("DELETE FROM pembagian_daging WHERE id = ?");
    $delete_query->execute([$id]);

    // Jika data pembagian_daging berhasil dihapus, hapus data pengguna dari tabel users
    if ($delete_query->rowCount() > 0) {
        // Hapus data dari tabel users
        $delete_user = $pdo->prepare("DELETE FROM users WHERE nik = ?");
        $delete_user->execute([$nik]);
    }
    
    header('Location: meat_distribution.php');
    exit;
}




// Mengambil data pembagian daging dengan JOIN ke tabel users
$sql = "
    SELECT 
        pembagian_daging.id,
        pembagian_daging.nik,
        users.name AS nama_penerima,
        pembagian_daging.role_penerima,
        pembagian_daging.jumlah_kg,
        pembagian_daging.status_pengambilan,
        pembagian_daging.qr_code
    FROM pembagian_daging
    JOIN users ON pembagian_daging.nik = users.nik
    ORDER BY pembagian_daging.id DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$data_pembagian = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distribution of Meat - Sistem Qurban</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            padding: 20px;
        }

        .card {
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Tombol Kembali ke Dashboard Sesuai Role -->
        <a href="<?= $dashboard_url ?>" class="btn btn-secondary mb-4">&larr; Kembali ke Dashboard</a>

        <h1>Distribution of Meat</h1>
        <p>Lihat dan kelola distribusi daging qurban.</p>

        <!-- Tabel Pembagian Daging -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>Data Pembagian Daging</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>NIK</th>
                            <th>Nama Penerima</th>
                            <th>Role Penerima</th>
                            <th>Jumlah Daging (kg)</th>
                            <th>Status Pengambilan</th>
                            <th>QR Code</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data_pembagian as $row): ?>
                            <tr>
                                <td><?= $row['nik'] ?></td>
                                <td><?= $row['nama_penerima'] ?></td>
                                <td><?= ucfirst($row['role_penerima']) ?></td>
                                <td><?= $row['jumlah_kg'] ?> kg</td>
                                <td><?= ucfirst($row['status_pengambilan']) ?></td>
                                <td>
                                    <!-- Menampilkan QR Code -->
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode('NIK: ' . $row['nik']) ?>"
                                        alt="QR Code" width="50">
                                </td>
                                <td>
                                    <!-- Edit dan Hapus Pembagian -->
                                    <a href="edit_meat_distribution.php?id=<?= $row['id'] ?>"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus pembagian ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>