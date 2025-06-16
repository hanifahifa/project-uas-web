<?php
session_start();
include 'db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['submit'])) {
    $nik = $_POST['nik'];
    $password = $_POST['password'];

    try {
        // Query untuk mendapatkan data pengguna berdasarkan NIK
        $sql = "SELECT * FROM users WHERE nik = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, 's', $nik); // Menggunakan 's' untuk parameter string
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        // Cek apakah pengguna ditemukan
        if ($user) {
            // Verifikasi password dengan password yang terenkripsi di database
            if (password_verify($password, $user['password'])) {
                // Set session data
                $_SESSION['user_nik'] = $user['nik'];
                $_SESSION['role'] = $user['role'];

                // Redirect ke dashboard utama
                header('Location: Dashboard_Utama/dashboard.php');
                exit;
            } else {
                // Password tidak cocok
                $error = "Password salah!";
            }
        } else {
            // NIK tidak ditemukan
            $error = "NIK tidak ditemukan!";
        }
    } catch (Exception $e) {
        // Menangani error query dan database
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Sistem Qurban</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <a href="index.html" class="back-btn">&larr; Kembali ke Beranda</a>

    <div class="login-container" role="main" aria-label="Form login sistem qurban">
        <h1>QURBANA</h1>
        <p class="subtitle">Masuk untuk mengelola sistem Qurban dengan mudah dan aman</p>

        <?php if (isset($error)): ?>
        <div class="error-message" role="alert" aria-live="assertive"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-4 text-start">
                <label for="nik" class="form-label">NIK</label>
                <input type="text" id="nik" name="nik" class="form-control" required autocomplete="username" />
            </div>
            <div class="mb-4 text-start">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password" />
            </div>
            <button type="submit" name="submit" class="btn btn-elegant" aria-label="Login ke sistem Qurbana">Masuk</button>
        </form>
    </div>

    <footer>
        &copy; 2025 QURBANA: Sistem Qurban RT 001.
    </footer>
</body>

</html>
