<<<<<<< HEAD
<?php
include 'db.php';
session_start();

if (isset($_POST['submit'])) {
    $nik = $_POST['nik'];  // Menggunakan NIK sebagai login
    $password = $_POST['password'];

    // Ambil data pengguna berdasarkan NIK
    $sql = "SELECT * FROM users WHERE nik = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nik]);
    $user = $stmt->fetch();

    // Pastikan user ditemukan
    if ($user) {
        // Verifikasi password (plaintext)
        if ($password === $user['password']) {
            // Set session untuk NIK dan role
            $_SESSION['user_nik'] = $user['nik'];
            $_SESSION['role'] = $user['role'];

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
            echo "Password salah!";
        }
    } else {
        echo "ID tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Qurban</title>
    <!-- CSS Styling langsung di head -->
    <style>
        /* Global Styling */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Container Styling untuk Registrasi */
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
            /* Biru iPhone */
            font-size: 24px;
            margin-bottom: 30px;
            font-weight: 600;
        }

        /* Input Fields Styling */
        input[type="text"],
        input[type="password"],
        select,
        input[type="submit"] {
            width: 100%;
            padding: 14px;
            margin: 10px 0;
            border-radius: 10px;
            border: 1px solid #ddd;
            box-sizing: border-box;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s ease-in-out;
        }

        /* Submit Button Styling */
        input[type="submit"] {
            background-color: #007aff;
            /* Biru iPhone */
            color: white;
            font-size: 16px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
            /* Darker blue on hover */
        }

        /* Focus Effect */
        input[type="text"]:focus,
        input[type="password"]:focus,
        select:focus {
            border-color: #007aff;
            /* Biru iPhone */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        /* Back Button Styling */
        .back-btn {
            font-size: 16px;
            color: #007aff;
            /* Biru iPhone */
            text-decoration: none;
            margin-bottom: 20px;
            display: inline-block;
            transition: color 0.3s ease-in-out;
            font-weight: normal;
            margin-top: 20px;
        }

        .back-btn:hover {
            color: #0056b3;
            /* Darker blue on hover */
        }

        /* Success Message Styling */
        .success-message {
            background-color: #28a745;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 16px;
            text-align: center;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 20px auto;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            .container-login {
                margin: 20px auto;
                padding: 20px;
                max-width: 90%;
            }

            h2 {
                font-size: 22px;
            }
        }

        @media (max-width: 480px) {
            .container-login {
                margin: 10px auto;
                padding: 15px;
                max-width: 95%;
            }

            input[type="text"],
            input[type="password"],
            select,
            input[type="submit"] {
                padding: 14px;
            }
        }
    </style>
</head>

<body>

    <div class="container-login">
        <!-- Tombol Back dengan simbol < -->
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
=======
<?php
include 'db.php';

session_start();

if (isset($_POST['submit'])) {
    $id = $_POST['id'];  // ID yang diinputkan oleh pengguna
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header('Location: dashboard.php');
    } else {
        echo "Invalid credentials!";
    }
}
?>

<form method="POST" action="">
    ID: <input type="text" name="id" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" name="submit" value="Login">
    <!-- Tombol Back -->
    <a href="index.html">
        <button type="button">Back to dashboard</button>
    </a>
</form>
>>>>>>> ab62da35eb63e7c2f4ea160ddd8babd3ec785d05
