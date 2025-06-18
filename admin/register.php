<?php
include('../db.php');
session_start();

if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $NIK = $_POST['NIK'];
    $alamat = $_POST['alamat'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $password = $_POST['password']; // Mendapatkan password dari form
    $role = $_POST['role'];

    // Mengecek apakah NIK sudah terdaftar
    $query_check_nik = "SELECT * FROM users WHERE nik = '$NIK'";
    $result_check_nik = mysqli_query($conn, $query_check_nik);

    if (mysqli_num_rows($result_check_nik) > 0) {
        echo "<script>
                alert('NIK sudah terdaftar. Silakan gunakan NIK lain.');
                window.history.back();
              </script>";
        exit();
    }

    // Menggunakan password_hash untuk mengenkripsi password sebelum disimpan ke database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash password dengan bcrypt

    // Menyimpan data pengguna ke dalam database (users)
    $query = "
        INSERT INTO users (nik, name, alamat, jenis_kelamin, password, role)
        VALUES ('$NIK', '$nama', '$alamat', '$jenis_kelamin', '$hashed_password', '$role')
    ";
    if (mysqli_query($conn, $query)) {
        // Jika pengguna memilih "warga", simpan role lain ke dalam tabel user_roles
        if ($role == 'warga') {
            $query_role = "INSERT INTO user_roles (nik, role) VALUES ('$NIK', 'warga')";
            mysqli_query($conn, $query_role);
        } elseif ($role == 'admin') {
            $query_role = "INSERT INTO user_roles (nik, role) VALUES ('$NIK', 'admin')";
            mysqli_query($conn, $query_role);
        } elseif ($role == 'panitia') {
            $query_role = "INSERT INTO user_roles (nik, role) VALUES ('$NIK', 'panitia')";
            mysqli_query($conn, $query_role);
        }

        $_SESSION['last_nik'] = $NIK;

        // Membuat QR code
        $qr_data = "NIK: " . $NIK . " | Nama: " . $nama;
        $qr_file = '../qrcodes/' . $NIK . '.png';
        $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qr_data);
        file_put_contents($qr_file, file_get_contents($qr_url));

        // Redirect ke halaman manage_user.php
        mysqli_close($conn);
        header("Location: manage_user.php");
        exit();
    } else {
        mysqli_close($conn);
        echo "<script>
                alert('Gagal menyimpan data pengguna.');
                window.history.back();
              </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pengguna</title>
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
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            width: 100%;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #1f7a38;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            background-color: #28a745;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #1f7a38;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Registrasi Pengguna</h1>
        </div>
        <div class="form-container">
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
                        <option value="warga">Warga</option>
                        <option value="admin">Admin</option>
                        <option value="panitia">Panitia</option>
                    </select>
                </div>
                <input type="submit" name="submit" value="Register">
            </form>
            <div class="back-link">
                <a href="manage_user.php" class="btn">← Kembali ke Manajemen Pengguna</a>
            </div>
        </div>
    </div>
</body>

</html>