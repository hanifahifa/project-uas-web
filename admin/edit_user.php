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
            $password_to_store = $password;

            // Update data pengguna di tabel users
            $query_update = "UPDATE users SET name = '$name', jenis_kelamin = '$jenis_kelamin', alamat = '$alamat', password = '$password_to_store' WHERE nik = '$nik'";
            mysqli_query($conn, $query_update);

            // Gabungkan role lama dengan role baru (tanpa menghapus role lama)
            $merged_roles = array_unique(array_merge(array_column($existing_roles, 'role'), $roles)); // Gabungkan dan hilangkan duplikasi

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
            background: linear-gradient(135deg, #f0f8f0, #e0f0e0);
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
        }
        .container {
            max-width: 500px;
            width: 100%;
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 25px;
            border-radius: 15px 15px 0 0;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h1 {
            font-size: 2.2em;
            margin: 0;
            font-weight: 600;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            color: #28a745;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #28a745;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1em;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-group input:focus,
        .form-group select:focus {
            border-color: #1f7a38;
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.5);
            outline: none;
        }
        .role-options {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .role-option {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }
        .role-option:hover {
            border-color: #28a745;
        }
        .role-option input[type="checkbox"] {
            margin: 0 12px 0 0;
            transform: scale(1.2);
            accent-color: #28a745;
        }
        .role-option label {
            margin: 0;
            font-weight: 500;
            color: #495057;
            cursor: pointer;
            flex: 1;
        }
        .btn-group {
            margin-top: 30px;
            text-align: center;
            display: flex;
            justify-content: center;
            gap: 20px; /* Jarak antar tombol */
        }
        .btn {
            background: linear-gradient(90deg, #28a745, #1f7a38);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.3s ease, background 0.3s ease;
        }
        .btn:hover {
            background: linear-gradient(90deg, #1f7a38, #145a2a);
            transform: translateY(-2px);
        }
        .btn:active {
            transform: translateY(0);
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #721c24;
            animation: fadeIn 0.5s ease-in-out;
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
                    <div class="role-options">
                        <div class="role-option">
                            <input type="checkbox" id="role_panitia" name="role[]" value="panitia" <?= in_array('panitia', array_column($existing_roles, 'role')) ? 'checked' : '' ?>>
                            <label for="role_panitia">Panitia</label>
                        </div>
                        <div class="role-option">
                            <input type="checkbox" id="role_berqurban" name="role[]" value="berqurban" <?= in_array('berqurban', array_column($existing_roles, 'role')) ? 'checked' : '' ?>>
                            <label for="role_berqurban">Berqurban</label>
                        </div>
                    </div>
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