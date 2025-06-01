<?php
include 'db.php';
session_start();

// Proses login ketika form disubmit
if (isset($_POST['submit'])) {
    $nik = $_POST['nik'];  // Menggunakan NIK sebagai login
    $password = $_POST['password'];

    // Ambil data pengguna berdasarkan NIK
    $sql = "SELECT * FROM users WHERE nik = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nik]);
    $user = $stmt->fetch();

    // Pastikan user ditemukan dan password sesuai
    if ($user) {
        // Verifikasi password (plaintext)
        if ($password === $user['password']) {
            // Set session untuk NIK dan role
            $_SESSION['user_nik'] = $user['nik'];
            $_SESSION['role'] = $user['role'];

            // Redirect berdasarkan role
            if ($user['role'] == 'admin') {
                header('Location: dashboard_admin.php');
            } elseif ($user['role'] == 'warga') {
                header('Location: dashboard_warga.php');
            } elseif ($user['role'] == 'panitia') {
                header('Location: dashboard_panitia.php');
            } elseif ($user['role'] == 'berqurban') {
                header('Location: dashboard_berqurban.php');
            }
        } else {
            echo "<div class='error-message'>Password salah!</div>";
        }
    } else {
        echo "<div class='error-message'>ID tidak ditemukan!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Qurban</title>
    <style>
        /* Global Styling */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Container Styling untuk Login */
        .container-login {
            width: 100%;
            max-width: 400px;
            margin: 50px auto;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 25px;
            text-align: left;
        }

        /* Heading Styling */
        h2 {
            color: #007aff;
            font-size: 24px;
            margin-bottom: 30px;
            font-weight: 600;
        }

        /* Input Fields Styling */
        input[type="text"],
        input[type="password"],
        input[type="submit"] {
            width: 100%;
            padding: 14px;
            margin: 10px 0;
            border-radius: 10px;
            border: 1px solid #ddd;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s ease-in-out;
        }

        input[type="submit"] {
            background-color: #007aff;
            color: white;
            font-size: 16px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Error Message Styling */
        .error-message {
            background-color: #dc3545;
            color: white;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            text-align: center;
            font-weight: 600;
        }

        /* Back Button Styling */
        .back-btn {
            font-size: 16px;
            color: #007aff;
            text-decoration: none;
            margin-bottom: 20px;
            display: inline-block;
            transition: color 0.3s ease-in-out;
        }

        .back-btn:hover {
            color: #0056b3;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container-login {
                margin: 20px auto;
                padding: 20px;
                max-width: 90%;
            }
        }

        @media (max-width: 480px) {
            .container-login {
                padding: 15px;
                max-width: 95%;
            }
        }
    </style>
</head>

<body>

    <div class="container-login">
        <!-- Tombol Back -->
        <a href="index.html" class="back-btn">&lt; Back</a>

        <h2>Login - Sistem Qurban</h2>

        <form method="POST" action="">
            <div class="form-group">
                <label for="nik">NIK</label>
                <input type="text" id="nik" name="nik" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <input type="submit" name="submit" value="Login">
        </form>
    </div>

</body>

</html>
