<?php
session_start();
include('../db.php');

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
        }elseif ($role == 'panitia') {
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
        </select>
    </div>
    <input type="submit" name="submit" value="Register">
</form>
