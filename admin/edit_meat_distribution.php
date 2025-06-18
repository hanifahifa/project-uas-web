<?php
include '../db.php';
session_start();

// Pastikan hanya admin atau panitia yang dapat mengakses halaman ini
// if (!isset($_SESSION['user_nik']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'panitia')) {
//     header('Location: ../login.php');
//     exit;
// }

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

// Membuat koneksi ke MySQL
$conn = mysqli_connect($host, $username, $password, $dbname);

// Mengecek apakah koneksi berhasil
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ambil data pembagian daging berdasarkan ID
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
    WHERE pembagian_daging.id = '$id'
";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    $_SESSION['message'] = "Data tidak ditemukan!";
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
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "ssi", $status_pengambilan, $jumlah_kg, $id);
        mysqli_stmt_execute($update_stmt);

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
        /* Styling goes here */
        /* Customize as needed */
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
