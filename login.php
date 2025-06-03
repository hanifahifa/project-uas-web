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
        case 'admin':
          header('Location: admin/dashboard_admin.php');
          break;  // Redirect ke dashboard admin
        case 'warga':
          header('Location: warga/dashboard_warga.php');
          break;  // Redirect ke dashboard warga
        case 'panitia':
          header('Location: panitia/dashboard_panitia.php');
          break;  // Redirect ke dashboard panitia
        case 'berqurban':
          header('Location: berqurban/dashboard_berqurban.php');
          break;  // Redirect ke dashboard berqurban
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
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Sistem Qurban</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

    body {
      font-family: 'Poppins', sans-serif;
      background: url('https://png.pngtree.com/png-clipart/20230601/ourmid/pngtree-animal-for-qurban-on-eid-al-adha-png-image_7108842.png') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 20px;
      backdrop-filter: brightness(0.65) blur(5px);
    }

    .login-container {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 25px;
      box-shadow: 0 12px 30px rgba(26, 77, 46, 0.35);
      max-width: 480px;
      width: 100%;
      padding: 50px 40px;
      text-align: center;
      transition: transform 0.3s ease;
    }

    .login-container:hover {
      transform: translateY(-5px);
    }

    .login-container h1 {
      font-weight: 700;
      color: #1A4D2E;
      margin-bottom: 8px;
      font-size: 2.8rem;
      letter-spacing: 2px;
    }

    .login-container p.subtitle {
      color: #3B7A57;
      font-weight: 500;
      font-size: 1.1rem;
      margin-bottom: 30px;
      letter-spacing: 0.5px;
    }

    .form-label {
      font-weight: 600;
      color: #1A4D2E;
    }

    .form-control {
      border-radius: 12px;
      border: 2px solid #A6C48A;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
      padding: 12px 16px;
      font-size: 1.1rem;
      color: #1A4D2E;
    }

    .form-control:focus {
      border-color: #D4AF37;
      box-shadow: 0 0 8px rgba(212, 175, 55, 0.7);
      outline: none;
    }

    .btn-elegant {
      background: linear-gradient(90deg, #1A4D2E 0%, #3B7A57 100%);
      border-radius: 12px;
      font-weight: 700;
      font-size: 1.2rem;
      padding: 14px;
      border: none;
      color: white;
      width: 100%;
      transition: background 0.4s ease;
      box-shadow: 0 6px 12px rgba(26, 77, 46, 0.3);
      cursor: pointer;
    }

    .btn-elegant:hover,
    .btn-elegant:focus {
      background: linear-gradient(90deg, #164A2F 0%, #5AA469 100%);
      box-shadow: 0 8px 18px rgba(26, 77, 46, 0.5);
      outline: none;
    }

    .error-message {
      background-color: #dc3545;
      color: white;
      padding: 12px;
      border-radius: 12px;
      text-align: center;
      margin-bottom: 20px;
      font-weight: 600;
      box-shadow: 0 4px 10px rgba(220, 53, 69, 0.6);
    }

    .back-btn {
      position: absolute;
      top: 25px;
      left: 25px;
      color: #1A4D2E;
      font-weight: 600;
      text-decoration: none;
      font-size: 1rem;
      padding: 8px 15px;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.8);
      box-shadow: 0 4px 8px rgba(26, 77, 46, 0.15);
      transition: background 0.3s ease;
    }

    .back-btn:hover {
      background: rgba(212, 175, 55, 0.8);
      color: white;
      text-decoration: none;
    }

    footer {
      margin-top: 40px;
      color:rgb(255, 255, 255);
      font-weight: 500;
      font-size: 0.9rem;
      text-align: center;
      opacity: 0.7;
      user-select: none;
    }

    @media (max-width: 576px) {
      .login-container {
        padding: 35px 25px;
        border-radius: 20px;
      }

      .login-container h1 {
        font-size: 2rem;
      }

      .btn-elegant {
        font-size: 1rem;
        padding: 12px;
      }
    }
  </style>
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
