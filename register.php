<?php
include 'db.php';

if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $NIK = $_POST['NIK'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (nama, NIK, alamat, no_hp, password, role) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nama, $NIK, $alamat, $no_hp, $password, $role]);

    // Ambil ID yang baru saja diinsert
    $last_id = $pdo->lastInsertId();

    echo "Registrasi berhasil! ID Anda:  <br>" . $last_id. "<br>";
    echo "Silakan login dengan ID dan password Anda.";

}
?>

<form method="POST" action="">
    Nama: <input type="text" name="nama" required><br>
    NIK: <input type="text" name="NIK" required><br>
    Alamat: <input type="text" name="alamat" required><br>
    No. HP: <input type="text" name="no_hp" required><br>
    Password: <input type="password" name="password" required><br>
    Role:
    <select name="role" required>
        <option value="admin">Admin</option>
        <option value="warga">Warga</option>
        <option value="panitia">Panitia</option>
        <option value="berqurban">Berqurban</option>
    </select><br>
    <input type="submit" name="submit" value="Register">
    <!-- Tombol Back -->
    <a href="index.html">
        <button type="button">Back to dashboard</button>
    </a>
</form>


