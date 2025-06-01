<?php
include 'db.php';
session_start();

if (isset($_POST['submit'])) {
    $nik = $_POST['nik'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE nik = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nik]);
    $user = $stmt->fetch();

    if ($user) {
        if ($password === $user['password']) {
            $_SESSION['user_nik'] = $user['nik'];
            $_SESSION['role'] = $user['role'];

             switch ($user['role']) {
                case 'admin': header('Location: admin/dashboard_admin.php'); break;  // Redirect ke dashboard admin
                case 'warga': header('Location: warga/dashboard_warga.php'); break;  // Redirect ke dashboard warga
                case 'panitia': header('Location: panitia/dashboard_panitia.php'); break;  // Redirect ke dashboard panitia
                case 'berqurban': header('Location: berqurban/dashboard_berqurban.php'); break;  // Redirect ke dashboard berqurban
            }
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "ID tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Sistem Qurban</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(to bottom right, #BFD8B8, #F0F5EC);
      background-image: url('https://images.unsplash.com/photo-1704859597974-cb64079ef7e6?q=80&w=2080&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'); /* Gambar suasana Idul Adha */
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(5px);
    }

    .login-card {
      background: linear-gradient(to bottom right, rgba(255,255,255,0.95), rgba(245, 255, 245, 0.9));
      border-radius: 18px;
      padding: 40px;
      box-shadow: 0 10px 25px rgba(26, 77, 46, 0.3);
      max-width: 420px;
      width: 100%;
    }

    .login-card h3 {
      color: #1A4D2E;
      font-weight: 700;
      margin-bottom: 25px;
      text-align: center;
    }

    .btn-elegant {
      background: linear-gradient(to right, #1A4D2E, #3B7A57);
      color: white;
      font-weight: 600;
      border: none;
      transition: all 0.3s ease;
    }

    .btn-elegant:hover {
      background: linear-gradient(to right, #164A2F, #5AA469);
    }

    .form-control:focus {
      border-color: #D4AF37;
      box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
    }

    .error-message {
      background-color: #dc3545;
      color: white;
      padding: 12px;
      border-radius: 8px;
      text-align: center;
      margin-bottom: 15px;
    }

    .back-btn {
      display: inline-block;
      margin-bottom: 15px;
      color: #1A4D2E;
      text-decoration: none;
    }

    .back-btn:hover {
      text-decoration: underline;
    }

    @media (max-width: 576px) {
      .login-card {
        padding: 30px;
        margin: 15px;
      }
    }
  </style>
</head>
<body>

  <div class="login-card">
    <a href="index.html" class="back-btn">&larr; Back to Dashboard</a>
    <h3>QURBANA</h3>

    <?php if (isset($error)) : ?>
      <div class="error-message"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label for="nik" class="form-label">NIK</label>
        <input type="text" class="form-control" id="nik" name="nik" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>
      <button type="submit" name="submit" class="btn btn-elegant w-100">Login</button>
    </form>
  </div>

</body>
</html>
