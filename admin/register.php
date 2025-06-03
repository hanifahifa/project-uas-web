<?php
session_start();
include('../db.php'); // Menghubungkan ke database

// Cek jika form registrasi disubmit
if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $NIK = $_POST['NIK'];
    $alamat = $_POST['alamat'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Cek apakah NIK sudah ada di database
    $query_check_nik = "SELECT * FROM users WHERE nik = ?";
    $stmt_check_nik = $pdo->prepare($query_check_nik);
    $stmt_check_nik->execute([$NIK]);

    if ($stmt_check_nik->rowCount() > 0) {
        // Jika NIK sudah terdaftar, tampilkan peringatan
        echo "<script>
                alert('NIK sudah terdaftar. Silakan gunakan NIK lain.');
                window.history.back();
              </script>";
        exit();
    }

    // Masukkan data pengguna ke database
    $query = $pdo->prepare("
        INSERT INTO users 
            (nik, name, alamat, jenis_kelamin, password, role) 
        VALUES 
            (?, ?, ?, ?, ?, ?)
    ");
    $query->execute([ $NIK, $nama, $alamat, $jenis_kelamin, $password, $role]);

    // Simpan NIK pengguna yang baru ditambahkan ke session
    $_SESSION['last_nik'] = $NIK;

    // Generate QR Code setelah berhasil registrasi
    $qr_data = "NIK: " . $NIK . " | Nama: " . $nama; // Data untuk QR Code
    $qr_file = '../qrcodes/' . $NIK . '.png'; // Lokasi file QR Code disimpan di folder qrcodes/

    // Membuat QR Code menggunakan API QR Server
    $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qr_data);

    // Menyimpan QR Code ke file
    file_put_contents($qr_file, file_get_contents($qr_url));

    // Redirect ke halaman dashboard pengguna
    header("Location: manage_user.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Qurban</title>
</head>

<body>

    <div class="container-register">
        <!-- Tombol Back dengan simbol < -->
        <a href="manage_user.php" class="back-btn">&lt; Back</a>

        <h2>Registrasi - Sistem Qurban</h2>

        <!-- Formulir Registrasi -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="nama">Nama</label>
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
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
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
