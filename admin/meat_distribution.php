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

    // Ambil nik dari pembagian_daging
    $query_nik = $pdo->prepare("SELECT nik FROM pembagian_daging WHERE id = ?");
    $query_nik->execute([$id]);
    $nik = $query_nik->fetchColumn();

    // Hapus gambar QR Code
    $query_delete = $pdo->prepare("SELECT qr_code FROM pembagian_daging WHERE id = ?");
    $query_delete->execute([$id]);
    $row = $query_delete->fetch();

    if ($row && file_exists($row['qr_code'])) {
        unlink($row['qr_code']);
    }

    // Hapus data dari pembagian_daging
    $delete_query = $pdo->prepare("DELETE FROM pembagian_daging WHERE id = ?");
    $delete_query->execute([$id]);

    // Hapus user jika data berhasil dihapus
    if ($delete_query->rowCount() > 0) {
        $delete_user = $pdo->prepare("DELETE FROM users WHERE nik = ?");
        $delete_user->execute([$nik]);
    }

    header('Location: meat_distribution.php');
    exit;
}

// Ambil data pembagian daging
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
    <title>Distribusi Daging - Sistem Qurban</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            padding: 20px;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            margin-top: 20px;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0, 100, 0, 0.1);
        }
        .card-header {
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }
        .btn-outline-success, .btn-outline-danger {
            border-radius: 12px;
        }
        h1 {
            color: #2e7d32;
            font-weight: bold;
        }
        .btn-success {
            background-color: #2e7d32;
            border-color: #2e7d32;
        }
        .btn-success:hover {
            background-color: #256429;
            border-color: #256429;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Tombol Kembali -->
    <a href="<?= $dashboard_url ?>" class="btn btn-success mb-4">
        <i class="bi bi-arrow-left-circle"></i> Kembali ke Dashboard
    </a>

    <h1>Distribusi Daging</h1>
    <p>Lihat dan kelola distribusi daging qurban.</p>

    <!-- Tabel -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4>Data Pembagian Daging</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-success">
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
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode('NIK: ' . $row['nik']) ?>" alt="QR Code" width="50">
                            </td>
                            <td>
                                <a href="edit_meat_distribution.php?id=<?= $row['id'] ?>" class="btn btn-outline-success btn-sm mb-1">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                    <i class="bi bi-trash3-fill"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (count($data_pembagian) === 0): ?>
                        <tr>
                            <td colspan="7">Belum ada data pembagian daging.</td>
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
