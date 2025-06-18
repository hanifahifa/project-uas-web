<?php
session_start(); // Memulai sesi
include 'db.php'; // Pastikan file db.php sudah ada dan benar

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['submit'])) {
    $nik = $_POST['nik'];
    $password = $_POST['password'];

    try {
        // Query untuk mendapatkan data pengguna berdasarkan NIK
        $sql = "SELECT * FROM users WHERE nik = '$nik'"; // Tanpa prepared statement, langsung query
        $result = mysqli_query($conn, $sql); // Menggunakan $conn untuk koneksi database
        $user = mysqli_fetch_assoc($result);

        // Cek apakah pengguna ditemukan
        if ($user) {
            // Verifikasi password dengan password yang terenkripsi di database
            if ($password === $user['password']) {
                // Password benar, lakukan login
                // Set session data
                $_SESSION['nik'] = $user['nik']; // Menyimpan nik di sesi
                $_SESSION['role'] = $user['role']; // Menyimpan role di sesi

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
            background: linear-gradient(135deg, #f0f8f0, #e0f0e0);
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow: hidden;
        }
        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #28a745;
            text-decoration: none;
            font-size: 1.1em;
            font-weight: bold;
            transition: color 0.3s ease, transform 0.3s ease;
        }
        .back-btn:hover {
            color: #1f7a38;
            transform: translateX(-5px);
        }
        .login-container {
            background: white;
            max-width: 400px;
            width: 100%;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            margin: auto;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h1 {
            color: #28a745;
            font-size: 2.5em;
            margin-bottom: 15px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .subtitle {
            color: #666;
            font-size: 1.1em;
            margin-bottom: 25px;
            font-style: italic;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: left;
            border-left: 4px solid #721c24;
            animation: shake 0.5s ease-in-out;
        }
        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
            100% { transform: translateX(0); }
        }
        .form-label {
            color: #28a745;
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
            text-align: left;
        }
        .form-control {
            border: 2px solid #28a745;
            border-radius: 8px;
            padding: 10px;
            font-size: 1em;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control:focus {
            border-color: #1f7a38;
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.5);
            outline: none;
        }
        .btn-elegant {
            background: linear-gradient(90deg, #28a745, #1f7a38);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            width: 100%;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: transform 0.3s ease, background 0.3s ease;
        }
        .btn-elegant:hover {
            background: linear-gradient(90deg, #1f7a38, #145a2a);
            transform: translateY(-2px);
        }
        .btn-elegant:active {
            transform: translateY(0);
        }
        footer {
            text-align: center;
            padding: 10px;
            color: #666;
            margin-top: auto;
            font-size: 0.9em;
            width: 100%;
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
                <input type="password" id="password" name="password" class="form-control" required
                    autocomplete="current-password" />
            </div>
            <button type="submit" name="submit" class="btn btn-elegant"
                aria-label="Login ke sistem Qurbana">Masuk</button>
        </form>
    </div>

    <footer>
        © 2025 QURBANA: Sistem Qurban RT 001.
    </footer>
</body>

</html>