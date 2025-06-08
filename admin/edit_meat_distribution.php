<?php
include '../db.php';
session_start();

// Pastikan hanya admin atau panitia yang dapat mengakses halaman ini
if (!isset($_SESSION['user_nik']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'panitia')) {
    header('Location: ../login.php');
    exit;
}

// Menentukan halaman dashboard yang tepat berdasarkan peran
$dashboard_url = ($_SESSION['role'] == 'admin') ? '../admin/dashboard_admin.php' : '../panitia/dashboard_panitia.php';

// Ambil ID dari parameter
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "ID tidak valid!";
    $_SESSION['message_type'] = "danger";
    header('Location: meat_distribution.php');
    exit;
}

$id = $_GET['id'];

// Ambil data pembagian daging berdasarkan ID
try {
    $query = "
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
        WHERE pembagian_daging.id = ?
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if (!$data) {
        $_SESSION['message'] = "Data tidak ditemukan!";
        $_SESSION['message_type'] = "danger";
        header('Location: meat_distribution.php');
        exit;
    }
} catch (Exception $e) {
    $_SESSION['message'] = "Error: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
    header('Location: meat_distribution.php');
    exit;
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status_pengambilan = $_POST['status_pengambilan'];
    $jumlah_kg = $_POST['jumlah_kg'];

    try {
        $update_query = "UPDATE pembagian_daging SET status_pengambilan = ?, jumlah_kg = ? WHERE id = ?";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->execute([$status_pengambilan, $jumlah_kg, $id]);

        $_SESSION['message'] = "Data berhasil diperbarui!";
        $_SESSION['message_type'] = "success";
        header('Location: meat_distribution.php');
        exit;

    } catch (Exception $e) {
        $_SESSION['message'] = "Gagal memperbarui data: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Distribusi Daging - Sistem Qurban</title>
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
            max-width: 800px;
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
            font-size: 2rem;
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
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.875rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-medium);
            box-shadow: 0 0 0 0.2rem rgba(143, 188, 143, 0.25);
        }

        .info-card {
            background: var(--primary-light);
            border: 1px solid var(--primary-medium);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-label {
            color: var(--text-dark);
            font-weight: 600;
        }

        .info-value {
            color: var(--primary-dark);
            font-weight: 500;
        }

        .role-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
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

        .btn-group {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        .btn-save {
            background: var(--primary-dark);
            color: var(--white);
            border: none;
            padding: 0.875rem 2rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-save:hover {
            background: var(--primary-medium);
            transform: translateY(-1px);
        }

        .btn-cancel {
            background: #6c757d;
            color: var(--white);
            border: none;
            padding: 0.875rem 2rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: #5a6268;
            color: var(--white);
            transform: translateY(-1px);
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

        @media (max-width: 768px) {
            .header-section {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .container {
                padding: 1rem;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn-save, .btn-cancel {
                width: 100%;
                justify-content: center;
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
                <i class="fas fa-edit"></i>
                Edit Distribusi Daging
            </h1>
            <p>Ubah status pengambilan dan jumlah distribusi daging</p>
        </div>
        <a href="meat_distribution.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Kembali
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

    <!-- Main Form -->
    <div class="main-card">
        <div class="card-header">
            <h4>
                <i class="fas fa-user"></i>
                Data Penerima
            </h4>
        </div>
        <div class="card-body">
            <!-- Info Penerima -->
            <div class="info-card">
                <div class="info-item">
                    <span class="info-label">NIK:</span>
                    <span class="info-value"><?= htmlspecialchars($data['nik']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Nama Penerima:</span>
                    <span class="info-value"><?= htmlspecialchars($data['nama_penerima']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Role:</span>
                    <span class="role-badge role-<?= $data['role_penerima'] ?>">
                        <?= ucfirst($data['role_penerima']) ?>
                    </span>
                </div>
            </div>

            <!-- Form Edit -->
            <form method="POST" id="editForm">
                <div class="form-group">
                    <label for="jumlah_kg" class="form-label">
                        <i class="fas fa-weight-hanging"></i>
                        Jumlah Daging (kg)
                    </label>
                    <input type="number" 
                           class="form-control" 
                           id="jumlah_kg" 
                           name="jumlah_kg" 
                           value="<?= $data['jumlah_kg'] ?>" 
                           min="0.1" 
                           step="0.1" 
                           required>
                </div>

                <div class="form-group">
                    <label for="status_pengambilan" class="form-label">
                        <i class="fas fa-check-circle"></i>
                        Status Pengambilan
                    </label>
                    <select class="form-select" id="status_pengambilan" name="status_pengambilan" required>
                        <option value="belum" <?= $data['status_pengambilan'] === 'belum' ? 'selected' : '' ?>>
                            Belum Diambil
                        </option>
                        <option value="sudah" <?= $data['status_pengambilan'] === 'sudah' ? 'selected' : '' ?>>
                            Sudah Diambil
                        </option>
                    </select>
                </div>

                <div class="btn-group">
                    <a href="meat_distribution.php" class="btn-cancel">
                        <i class="fas fa-times"></i>
                        Batal
                    </a>
                    <button type="submit" class="btn-save" onclick="return confirmUpdate()">
                        <i class="fas fa-save"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmUpdate() {
    const status = document.getElementById('status_pengambilan').value;
    const jumlah = document.getElementById('jumlah_kg').value;
    const nama = '<?= htmlspecialchars($data['nama_penerima']) ?>';
    
    const statusText = status === 'sudah' ? 'Sudah Diambil' : 'Belum Diambil';
    
    return confirm(`Apakah Anda yakin ingin mengubah data distribusi untuk ${nama}?\n\nStatus: ${statusText}\nJumlah: ${jumlah} kg`);
}
</script>

</body>
</html>