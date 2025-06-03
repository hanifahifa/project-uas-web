<?php
session_start();
include('../db.php');

if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $NIK = $_POST['NIK'];
    $alamat = $_POST['alamat'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $query_check_nik = "SELECT * FROM users WHERE nik = ?";
    $stmt_check_nik = $pdo->prepare($query_check_nik);
    $stmt_check_nik->execute([$NIK]);

    if ($stmt_check_nik->rowCount() > 0) {
        echo "<script>
                alert('NIK sudah terdaftar. Silakan gunakan NIK lain.');
                window.history.back();
              </script>";
        exit();
    }

    $query = $pdo->prepare("
        INSERT INTO users (nik, name, alamat, jenis_kelamin, password, role)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $query->execute([$NIK, $nama, $alamat, $jenis_kelamin, $password, $role]);

    $_SESSION['last_nik'] = $NIK;

    $qr_data = "NIK: " . $NIK . " | Nama: " . $nama;
    $qr_file = '../qrcodes/' . $NIK . '.png';
    $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qr_data);
    file_put_contents($qr_file, file_get_contents($qr_url));

    header("Location: manage_user.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pengguna - Sistem Qurban</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container-register {
            max-width: 600px;
            margin: 60px auto;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: rgb(9, 62, 36);
            font-weight: bold;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px 15px;
            margin-top: 5px;
            border-radius: 10px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            background-color: rgb(9, 62, 36);
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 10px;
            width: 100%;
            margin-top: 20px;
        }

        input[type="submit"]:hover {
            background-color: rgb(12, 97, 42);
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: rgb(9, 62, 36);
            text-decoration: none;
            font-weight: 600;
        }

        .back-btn:hover {
            text-decoration: underline;
        }

        @media screen and (max-width: 576px) {
            .container-register {
                padding: 25px;
                margin: 40px 15px;
            }
        }
    </style>
</head>
<body>

    <div class="container-register">
        <a href="manage_user.php" class="back-btn">&lt; Kembali</a>
        <h2>Form Registrasi Pengguna</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" required>
            </div>
            <div class="form-group">
                <label for="NIK">NIK</label>
                <input type="text" id="NIK" name="NIK" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <input type="text" id="alamat" name="alamat" required>
            </div>
            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <select id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="">-- Pilih --</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Peran</label>
                <select id="role" name="role" required>
                    <option value="">-- Pilih --</option>
                    <option value="admin">Admin</option>
                    <option value="warga">Warga</option>
                    <option value="panitia">Panitia</option>
                    <option value="berqurban">Berqurban</option>
                </select>
            </div>
            <input type="submit" name="submit" value="Register">
        </form>
    </div>

</body>
</html>
