<?php
session_start();  // Memulai sesi
include 'db.php';  // Pastikan file db.php sudah ada dan benar

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['submit'])) {
    $nik = $_POST['nik'];
    $password = $_POST['password'];

    try {
        // Query untuk mendapatkan data pengguna berdasarkan NIK
        $sql = "SELECT * FROM users WHERE nik = '$nik'";  // Tanpa prepared statement, langsung query
        $result = mysqli_query($conn, $sql);  // Menggunakan $conn untuk koneksi database
        $user = mysqli_fetch_assoc($result);

        // Cek apakah pengguna ditemukan
        if ($user) {
            // Verifikasi password dengan password yang terenkripsi di database
            if (password_verify($password, $user['password'])) {
                // Set session data
                $_SESSION['nik'] = $user['nik'];  // Menyimpan nik di sesi
                $_SESSION['role'] = $user['role'];  // Menyimpan role di sesi

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
    <style>
        body {
            background-color: #f0f8f0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        .back-btn {
            color: #28a745;
            text-decoration: none;
            font-size: 1em;
            margin: 20px;
            font-weight: bold;
        }
        .back-btn:hover {
            color: #1f7a38;
        }
        .login-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        h1 {
            color: #28a745;
            font-size: 2em;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #555;
            font-size: 1em;
            margin-bottom: 20px;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: left;
        }
        .form-label {
            color: #28a745;
            font-weight: bold;
        }
        .form-control {
            border: 1px solid #28a745;
            border-radius: 5px;
            padding: 8px;
        }
        .btn-elegant {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            width: 100%;
            font-weight: bold;
        }
        .btn-elegant:hover {
            background-color: #1f7a38;
        }
        footer {
            text-align: center;
            padding: 10px;
            color: #555;
            margin-top: auto;
        }
    </style>
</head>

<body>
    <a href="index.html" class="back-btn">← Kembali ke Beranda</a>

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
        © 2025 QURBANA: Sistem Qurban RT 001.
    </footer>
</body>

</html>