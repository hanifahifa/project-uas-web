<?php
include('../db.php');
session_start();

// Hanya admin yang boleh mengakses
// if (!isset($_SESSION['user_nik']) || $_SESSION['role'] != 'admin') {
//     header('Location: ../login.php');
//     exit;
// }

// Cek apakah parameter NIK ada
if (!isset($_GET['nik']) || empty($_GET['nik'])) {
    header('Location: manage_user.php?error=nik_kosong');
    exit;
}

$nik = $_GET['nik'];

// Ambil data pengguna berdasarkan NIK
$query = "SELECT * FROM users WHERE nik = '$nik'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Cek apakah pengguna ditemukan
if (!$user) {
    header('Location: manage_user.php?error=user_tidak_ditemukan');
    exit;
}

// Ambil role pengguna yang sudah ada dari tabel user_roles
$query_roles = "SELECT role FROM user_roles WHERE nik = '$nik'";
$result_roles = mysqli_query($conn, $query_roles);
$existing_roles = mysqli_fetch_all($result_roles, MYSQLI_ASSOC);

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
            $query_update = "UPDATE users SET name = '$name', jenis_kelamin = '$jenis_kelamin', alamat = '$alamat', password = '$hashed_password' WHERE nik = '$nik'";
            mysqli_query($conn, $query_update);

            // Gabungkan role lama dengan role baru (tanpa menghapus role lama)
            $merged_roles = array_unique(array_merge(array_column($existing_roles, 'role'), $roles)); // Gabungkan dan hilangkan duplikasi
            $merged_roles_string = implode(',', $merged_roles); // Menggabungkan role menjadi string yang dipisahkan koma

            // Hapus semua role lama dari user
            $query_delete_roles = "DELETE FROM user_roles WHERE nik = '$nik'";
            mysqli_query($conn, $query_delete_roles);

            // Tambahkan role baru dan lama ke user
            foreach ($merged_roles as $role) {
                $query_insert_role = "INSERT INTO user_roles (nik, role) VALUES ('$nik', '$role')";
                mysqli_query($conn, $query_insert_role);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f0f8f0;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            max-width: 500px;
            width: 100%;
        }
        .header {
            background-color: #28a745;
            color: white;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 2em;
            margin: 0;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            color: #28a745;
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #28a745;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .form-group select[multiple] {
            height: 100px; /* Tinggi untuk multiple select */
        }
        .btn-group {
            margin-top: 20px;
            text-align: center;
        }
        .btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin: 0 10px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #1f7a38;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tambah Role Pengguna</h1>
        </div>
        <div class="form-container">
            <?php if (isset($error_message)): ?>
                <div class="alert-error">
                    ❌ <?= htmlspecialchars($error_message) ?>
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
                        <option value="admin" <?= in_array('admin', array_column($existing_roles, 'role')) ? 'selected' : '' ?>>Admin</option>
                        <option value="warga" <?= in_array('warga', array_column($existing_roles, 'role')) ? 'selected' : '' ?>>Warga</option>
                        <option value="panitia" <?= in_array('panitia', array_column($existing_roles, 'role')) ? 'selected' : '' ?>>Panitia</option>
                        <option value="berqurban" <?= in_array('berqurban', array_column($existing_roles, 'role')) ? 'selected' : '' ?>>Berqurban</option>
                    </select>
                </div>
                <div class="btn-group">
                    <button type="submit" class="btn">Simpan Perubahan</button>
                    <a href="manage_user.php" class="btn">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>