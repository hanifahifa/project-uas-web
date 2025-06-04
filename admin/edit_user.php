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
$stmt = $pdo->prepare("SELECT * FROM users WHERE nik = ?");
$stmt->execute([$nik]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Cek apakah pengguna ditemukan
if (!$user) {
    header('Location: manage_user.php?error=user_tidak_ditemukan');
    exit;
}

// Jika form disubmit, update data pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $alamat = $_POST['alamat'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validasi input
    if (empty($name) || empty($jenis_kelamin) || empty($alamat) || empty($password) || empty($role)) {
        $error_message = 'Semua kolom wajib diisi!';
    } else {
        // Enkripsi password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update data pengguna
        $stmt_update = $pdo->prepare("UPDATE users SET name = ?, jenis_kelamin = ?, alamat = ?, password = ?, role = ? WHERE nik = ?");
        $stmt_update->execute([$name, $jenis_kelamin, $alamat, $hashed_password, $role, $nik]);

        header('Location: manage_user.php?success=user_berhasil_diedit');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
        }

        .container {
            max-width: 500px;
            margin: 60px auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: rgb(9, 62, 36);
            font-weight: bold;
            margin-bottom: 10px;
        }

        p {
            text-align: center;
            margin-bottom: 30px;
            color: #555;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }

        .btn {
            padding: 10px 16px;
            background-color: rgb(9, 62, 36);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            font-size: 15px;
            transition: background-color 0.2s ease-in-out;
        }

        .btn:hover {
            background-color: rgb(12, 97, 42);
        }

        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-weight: 600;
        }

        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

    </style>
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
                <select id="role" name="role" required>
                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="warga" <?= $user['role'] == 'warga' ? 'selected' : '' ?>>Warga</option>
                    <option value="panitia" <?= $user['role'] == 'panitia' ? 'selected' : '' ?>>Panitia</option>
                    <option value="berqurban" <?= $user['role'] == 'berqurban' ? 'selected' : '' ?>>Berqurban</option>
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
