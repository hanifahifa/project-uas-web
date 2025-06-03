<!-- 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Qurban</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">Sistem Qurban</a>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="login.php">Login</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="register.php">Register</a>
            </li>
        </ul>
    </nav> -->

    <!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>QURBANA - Sistem Qurban</title>
  <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/flatly/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to bottom right, #BFD8B8, #F0F5EC);
    }

    .navbar {
      background-color: #1A4D2E !important;
    }

    .navbar-brand, .nav-link {
      color: white !important;
      font-weight: 600;
    }

    .nav-link:hover {
      text-decoration: underline;
    }

    .hero {
      text-align: center;
      padding: 80px 20px;
      color: #1A4D2E;
    }

    .hero h1 {
      font-size: 3rem;
      font-weight: 700;
    }

    .hero p {
      font-size: 1.2rem;
      margin-bottom: 30px;
    }

    .btn-main {
      background: linear-gradient(to right, #1A4D2E, #3B7A57);
      color: white;
      padding: 12px 30px;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      transition: 0.3s ease;
    }

    .btn-main:hover {
      background: linear-gradient(to right, #164A2F, #5AA469);
      color: #fff;
    }

    .footer {
      text-align: center;
      padding: 20px;
      color: #ffffff;
      background-color: #1A4D2E;
      margin-top: 80px;
    }
  </style>
</head>

<body>

<nav class="navbar navbar-expand-lg">
  <a class="navbar-brand" href="index.php">QURBANA</a>
  <div class="collapse navbar-collapse justify-content-end">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="login.php">Login</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="register.php">Register</a>
      </li>
    </ul>
  </div>
</nav>

<section class="hero">
  <h1>Selamat Datang di QURBANA</h1>
  <p>Sistem informasi pengelolaan qurban untuk warga, panitia, dan peserta yang berqurban. Transparan, praktis, dan religius.</p>
  <a href="login.php" class="btn btn-main">Masuk Sekarang</a>
</section>

<div class="footer">
  &copy; <?= date('Y'); ?> Qurbana - Sistem Qurban Digital
</div>

</body>
</html>
