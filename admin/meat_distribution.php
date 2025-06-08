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

    try {
        // Mulai transaksi
        $pdo->beginTransaction();

        // Ambil nik dari pembagian_daging
        $query_nik = $pdo->prepare("SELECT nik FROM pembagian_daging WHERE id = ?");
        $query_nik->execute([$id]);
        $nik = $query_nik->fetchColumn();

        if ($nik) {
            // Hapus data dari pembagian_daging
            $delete_query = $pdo->prepare("DELETE FROM pembagian_daging WHERE id = ?");
            $delete_query->execute([$id]);

            // Hapus user jika data berhasil dihapus (hanya jika bukan admin, panitia, atau berqurban)
            $check_user = $pdo->prepare("SELECT role FROM users WHERE nik = ?");
            $check_user->execute([$nik]);
            $user_role = $check_user->fetchColumn();

            if ($user_role && $user_role === 'warga') {
                $delete_user = $pdo->prepare("DELETE FROM users WHERE nik = ?");
                $delete_user->execute([$nik]);
            }
        }

        // Commit transaksi
        $pdo->commit();
        $_SESSION['message'] = "Data berhasil dihapus!";
        $_SESSION['message_type'] = "success";

    } catch (Exception $e) {
        // Rollback jika terjadi kesalahan
        $pdo->rollback();
        $_SESSION['message'] = "Gagal menghapus data: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
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

// Hitung statistik
$total_daging = count($data_pembagian);
$sudah_diambil = count(array_filter($data_pembagian, function($item) {
    return $item['status_pengambilan'] === 'sudah';
}));
$belum_diambil = $total_daging - $sudah_diambil;
$persentase_diambil = $total_daging > 0 ? ($sudah_diambil / $total_daging) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distribusi Daging - Sistem Qurban</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            --border-radius: 16px;
            --shadow: 0 4px 20px rgba(26, 79, 46, 0.08);
            --shadow-hover: 0 8px 32px rgba(26, 79, 46, 0.12);
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

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: var(--white);
            padding: 1.5rem 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .header-title h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header-title p {
            color: var(--text-light);
            margin: 0.5rem 0 0 0;
            font-size: 0.95rem;
        }

        .back-btn {
            background: var(--primary-dark);
            color: var(--white);
            border: none;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: var(--primary-medium);
            color: var(--white);
            transform: translateY(-1px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            border: none;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-medium), var(--primary-dark));
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-light);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-dark);
            font-size: 1.2rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .main-card {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            position: relative;
        }

        .main-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-medium), var(--primary-dark));
        }

        .card-header {
            background: var(--white);
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem 2rem;
        }

        .card-header h4 {
            color: var(--primary-dark);
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-body {
            padding: 0;
        }

        .table-responsive {
            border-radius: 0 0 var(--border-radius) var(--border-radius);
        }

        .table {
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background: var(--primary-light);
            color: var(--primary-dark);
            font-weight: 600;
            border: none;
            padding: 1rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 1rem;
            border-top: 1px solid #e9ecef;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .status-belum {
            background: #fff3cd;
            color: #856404;
        }

        .status-sudah {
            background: #d1edff;
            color: #0c63e4;
        }

        .role-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .role-warga {
            background: var(--primary-light);
            color: var(--primary-dark);
        }

        .role-panitia {
            background: #e3f2fd;
            color: #1565c0;
        }

        .role-berqurban {
            background: #fff3e0;
            color: #ef6c00;
        }

        .btn-edit {
            background: var(--primary-medium);
            color: var(--white);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            transition: all 0.3s ease;
            margin-right: 0.5rem;
        }

        .btn-edit:hover {
            background: var(--primary-dark);
            color: var(--white);
            transform: translateY(-1px);
        }

        .btn-delete {
            background: #dc3545;
            color: var(--white);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            transition: all 0.3s ease;
        }

        .btn-delete:hover {
            background: #c82333;
            color: var(--white);
            transform: translateY(-1px);
        }

        .qr-code {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-light);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--primary-light);
            margin-bottom: 1rem;
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d1edff;
            color: #0c63e4;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .progress-container {
            margin-top: 0.5rem;
        }

        .progress {
            height: 6px;
            border-radius: 4px;
            background: var(--primary-light);
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--primary-medium), var(--primary-dark));
            border-radius: 4px;
            transition: width 0.6s ease;
        }

        @media (max-width: 768px) {
            .header-section {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .container {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .table-responsive {
                font-size: 0.85rem;
            }

            .btn-edit, .btn-delete {
                padding: 0.375rem 0.75rem;
                font-size: 0.8rem;
                margin-bottom: 0.25rem;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-title">
            <h1>
                <i class="fas fa-hand-holding-heart"></i>
                Distribusi Daging
            </h1>
            <p>Kelola dan pantau distribusi daging qurban kepada warga</p>
        </div>
        <a href="<?= $dashboard_url ?>" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Dashboard
        </a>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
            <i class="fas fa-<?= $_SESSION['message_type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
            <?= $_SESSION['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-boxes"></i>
                </div>
            </div>
            <div class="stat-value"><?= $total_daging ?></div>
            <div class="stat-label">Total Distribusi</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-value"><?= $sudah_diambil ?></div>
            <div class="stat-label">Sudah Diambil</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-value"><?= $belum_diambil ?></div>
            <div class="stat-label">Belum Diambil</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
            <div class="stat-value"><?= number_format($persentase_diambil, 1) ?>%</div>
            <div class="stat-label">Persentase Selesai</div>
            <div class="progress-container">
                <div class="progress">
                    <div class="progress-bar" style="width: <?= $persentase_diambil ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="main-card">
        <div class="card-header">
            <h4>
                <i class="fas fa-list"></i>
                Data Pembagian Daging
            </h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>NIK</th>
                            <th>Nama Penerima</th>
                            <th>Role</th>
                            <th>Jumlah (kg)</th>
                            <th>Status</th>
                            <th>QR Code</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($data_pembagian) > 0): ?>
                            <?php foreach ($data_pembagian as $row): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($row['nik']) ?></strong></td>
                                <td><?= htmlspecialchars($row['nama_penerima']) ?></td>
                                <td>
                                    <span class="role-badge role-<?= $row['role_penerima'] ?>">
                                        <?= ucfirst($row['role_penerima']) ?>
                                    </span>
                                </td>
                                <td><strong><?= $row['jumlah_kg'] ?> kg</strong></td>
                                <td>
                                    <span class="status-badge status-<?= $row['status_pengambilan'] ?>">
                                        <?= $row['status_pengambilan'] === 'sudah' ? 'Sudah Diambil' : 'Belum Diambil' ?>
                                    </span>
                                </td>
                                <td>
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode('NIK: ' . $row['nik']) ?>" 
                                         alt="QR Code" class="qr-code">
                                </td>
                                <td>
                                    <a href="edit_meat_distribution.php?id=<?= $row['id'] ?>" class="btn-edit">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </a>
                                    <a href="?delete=<?= $row['id'] ?>" class="btn-delete" 
                                       onclick="return confirm('Yakin ingin menghapus data distribusi untuk <?= htmlspecialchars($row['nama_penerima']) ?>?\n\nPerhatian: Jika penerima adalah warga biasa, akun user juga akan dihapus.')">
                                        <i class="fas fa-trash"></i>
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <h5>Belum Ada Data</h5>
                                    <p>Belum ada data pembagian daging yang tersedia.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>