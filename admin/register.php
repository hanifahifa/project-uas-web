<?php
include '../db.php';
session_start();

if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $NIK = $_POST['NIK'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Masukkan data pengguna ke database
    $sql = "INSERT INTO users (nama, NIK, alamat, no_hp, password, role) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nama, $NIK, $alamat, $no_hp, $password, $role]);

    // Ambil ID yang baru saja dimasukkan
    $last_id = $pdo->lastInsertId();

    // Set session untuk ID yang baru
    $_SESSION['last_id'] = $last_id;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Qurban</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <div class="container-register">
        <!-- Tombol Back dengan simbol < -->
        <a href="manage_user.php" class="back-btn">&lt; Back</a>

        <h2>Registrasi - Sistem Qurban</h2>

        <!-- Pesan berhasil registrasi muncul di bawah judul -->
        <?php if (isset($_SESSION['last_id'])): ?>
            <div class="success-message">
                Registrasi berhasil! ID Anda: <?php echo $_SESSION['last_id']; ?>. <br> Silakan login dengan ID dan password
                Anda.
                <a href="login.php" class="btn-login">Login Sekarang</a> <!-- Tombol untuk login -->

            </div>
            
            <?php unset($_SESSION['last_id']); ?> <!-- Menghapus session setelah ditampilkan -->
        <?php endif; ?>



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
                <label for="no_hp">No. HP</label>
                <input type="text" id="no_hp" name="no_hp" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select name="role" required>
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
