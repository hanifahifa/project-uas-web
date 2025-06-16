<?php
include('../db.php');
session_start();

// Hanya admin yang boleh mengakses
if (!isset($_SESSION['user_nik']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Cek apakah parameter NIK ada
if (!isset($_GET['nik']) || empty($_GET['nik'])) {
    header('Location: manage_user.php?error=nik_kosong');
    exit;
}

$nik = $_GET['nik'];

// Ambil data pengguna berdasarkan NIK
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE nik = ?");
mysqli_stmt_bind_param($stmt, 's', $nik);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Cek apakah pengguna ditemukan
if (!$user) {
    header('Location: manage_user.php?error=user_tidak_ditemukan');
    exit;
}

// Ambil role pengguna yang sudah ada dari tabel user_roles
$stmt_roles = mysqli_prepare($conn, "SELECT role FROM user_roles WHERE nik = ?");
mysqli_stmt_bind_param($stmt_roles, 's', $nik);
mysqli_stmt_execute($stmt_roles);
$existing_roles = mysqli_fetch_all(mysqli_stmt_get_result($stmt_roles), MYSQLI_ASSOC);

// Jika form disubmit, update data pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $alamat = $_POST['alamat'];
    $password = $_POST['password'];
    $roles = $_POST['role']; // Ambil array role yang dipilih

    // Validasi input
    if (empty($name) || empty($jenis_kelamin) || empty($alamat) || empty($password) || empty($roles)) {
        $error_message = 'Semua kolom wajib diisi!';
    } else {
        try {
            // Enkripsi password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Update data pengguna di tabel users
            $stmt_update = mysqli_prepare($conn, "UPDATE users SET name = ?, jenis_kelamin = ?, alamat = ?, password = ? WHERE nik = ?");
            mysqli_stmt_bind_param($stmt_update, 'sssss', $name, $jenis_kelamin, $alamat, $hashed_password, $nik);
            mysqli_stmt_execute($stmt_update);

            // Gabungkan role lama dengan role baru (tanpa menghapus role lama)
            $merged_roles = array_unique(array_merge($existing_roles, $roles)); // Gabungkan dan hilangkan duplikasi

            // Hapus semua role lama dari user
            $stmt_delete_roles = mysqli_prepare($conn, "DELETE FROM user_roles WHERE nik = ?");
            mysqli_stmt_bind_param($stmt_delete_roles, 's', $nik);
            mysqli_stmt_execute($stmt_delete_roles);

            // Tambahkan role baru dan lama ke user
            foreach ($merged_roles as $role) {
                $stmt_insert_role = mysqli_prepare($conn, "INSERT INTO user_roles (nik, role) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmt_insert_role, 'ss', $nik, $role);
                mysqli_stmt_execute($stmt_insert_role);
            }

            header('Location: manage_user.php?success=user_berhasil_diedit');
            exit;
        } catch (Exception $e) {
            $error_message = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
</head>
<body>

<div class="container">
    <h1>Edit Pengguna</h1>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-error">
            ❌ <?= $error_message ?>
        </div>
    <?php endif; ?>

    <form action="edit_user.php?nik=<?= $user['nik'] ?>" method="POST">
        <div class="form-group">
            <label for="name">Nama</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="jenis_kelamin">Jenis Kelamin</label>
            <select id="jenis_kelamin" name="jenis_kelamin" required>
                <option value="L" <?= $user['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="P" <?= $user['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
            </select>
        </div>
        <div class="form-group">
            <label for="alamat">Alamat</label>
            <input type="text" id="alamat" name="alamat" value="<?= htmlspecialchars($user['alamat']) ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Masukkan password baru" required>
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role[]" multiple required>
                <option value="admin" <?= in_array('admin', $existing_roles) ? 'selected' : '' ?>>Admin</option>
                <option value="warga" <?= in_array('warga', $existing_roles) ? 'selected' : '' ?>>Warga</option>
                <option value="panitia" <?= in_array('panitia', $existing_roles) ? 'selected' : '' ?>>Panitia</option>
                <option value="berqurban" <?= in_array('berqurban', $existing_roles) ? 'selected' : '' ?>>Berqurban</option>
            </select>
        </div>
        <div class="btn-group">
            <button type="submit" class="btn">Simpan Perubahan</button>
            <a href="manage_user.php" class="btn">Batal</a>
        </div>
    </form>
</div>

</body>
</html>
