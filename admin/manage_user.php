<?php
include('../db.php');
session_start();

// Hanya admin yang dapat mengakses
if (!isset($_SESSION['user_nik']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Membuat koneksi ke MySQL
$conn = mysqli_connect($host, $username, $password, $dbname);

// Mengecek apakah koneksi berhasil
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ambil semua data pengguna
$query = "SELECT * FROM users ORDER BY name";
$result = mysqli_query($conn, $query);

// Ambil pesan sukses/error dari URL
$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'user_berhasil_dihapus':
            $success_message = 'Pengguna berhasil dihapus dari sistem!';
            break;
    }
}

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'nik_kosong':
            $error_message = 'NIK pengguna tidak valid!';
            break;
        case 'user_tidak_ditemukan':
            $error_message = 'Pengguna tidak ditemukan!';
            break;
        case 'tidak_bisa_hapus_diri_sendiri':
            $error_message = 'Anda tidak bisa menghapus akun Anda sendiri!';
            break;
        case 'gagal_menghapus_user':
            $error_message = 'Gagal menghapus pengguna. Silakan coba lagi!';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna</title>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap" rel="stylesheet">
</head>
<body>

<div class="container">
    <h1>Manajemen Pengguna</h1>
    <p>Kelola data pengguna yang terdaftar dalam sistem qurban.</p>

    <!-- Alert Messages -->
    <?php if ($success_message): ?>
        <div class="alert alert-success">
            ✅ <?= $success_message ?>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-error">
            ❌ <?= $error_message ?>
        </div>
    <?php endif; ?>

    <div class="top-actions">
        <a href="register.php" class="btn">+ Tambah Pengguna</a>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="user-count">
            Total: <?= mysqli_num_rows($result) ?> pengguna terdaftar
        </div>

        <table>
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Jenis Kelamin</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nik']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                        <td>
                            <?php
                            // Ambil role pengguna
                            $stmt_roles = mysqli_prepare($conn, "SELECT role FROM user_roles WHERE nik = ?");
                            mysqli_stmt_bind_param($stmt_roles, 's', $row['nik']);
                            mysqli_stmt_execute($stmt_roles);
                            $roles_result = mysqli_stmt_get_result($stmt_roles);

                            // Gantilah MYSQLI_COLUMN dengan MYSQLI_ASSOC untuk hasil sebagai array asosiasi
                            $roles = mysqli_fetch_all($roles_result, MYSQLI_ASSOC); 

                            // Tampilkan role sebagai string
                            $roles = array_column($roles, 'role');
                            echo implode(", ", $roles); // Menampilkan semua role yang dimiliki pengguna
                            ?>
                        </td>
                        <td>
                            <a href="edit_user.php?nik=<?= $row['nik']?>" class="action-link">✏️ Edit</a> |
                            <?php if ($row['nik'] != $_SESSION['user_nik']): ?>
                                <a href="delete_user.php?nik=<?= $row['nik'] ?>" class="action-link delete">🗑️ Hapus</a>
                            <?php else: ?>
                                <span style="color: #ccc; font-style: italic;">Anda</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 40px;">
            <p style="font-size: 18px; color: #999; font-style: italic;">📋 Tidak ada pengguna yang terdaftar.</p>
        </div>
    <?php endif; ?>

    <div style="text-align: center;">
        <a href="dashboard_admin.php" class="btn">← Kembali ke Dashboard</a>
    </div>
</div>

</body>
</html>
