<?php
session_start(); // Memulai sesi

// Pastikan pengguna sudah login dan memiliki nik di sesi
if (!isset($_SESSION['nik'])) {
    header('Location: login.php'); // Arahkan ke halaman login jika nik tidak ada
    exit;
}

// Ambil nik pengguna dari sesi
$nik = $_SESSION['nik'];

// Koneksi ke database
include '../db.php';

// Ambil semua role pengguna berdasarkan NIK
$sql = "SELECT role FROM user_roles WHERE nik = '$nik'";
$result = mysqli_query($conn, $sql);

// Menyimpan role pengguna dalam array
$roles = [];
while ($row = mysqli_fetch_assoc($result)) {
    $roles[] = $row['role'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        body {
            background: linear-gradient(135deg, #f0f8f0, #e0f0e0);
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            overflow: hidden;
        }
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #28a745, #1f7a38);
            color: white;
            padding: 0;
            height: calc(100vh - 40px);
            position: fixed;
            top: 20px;
            left: 20px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3);
            transition: transform 0.3s ease;
        }
        .sidebar:hover {
            transform: translateX(-5px);
        }
        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar-header h4 {
            margin: 0;
            font-weight: 600;
            font-size: 1.2em;
            color: white;
        }
        .sidebar .list-group {
            background: transparent;
            border: none;
            padding: 20px 0;
        }
        .sidebar .list-group-item {
            background-color: transparent;
            border: none;
            color: white;
            padding: 0;
            margin: 5px 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .sidebar .list-group-item a {
            color: white;
            text-decoration: none;
            font-size: 0.95em;
            font-weight: 500;
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .sidebar .list-group-item a i {
            margin-right: 10px;
        }
        .sidebar .list-group-item:hover a {
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }
        .content {
            margin-left: 290px;
            padding: 0;
            flex: 1;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            min-height: calc(100vh - 40px);
            overflow: hidden;
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px;
            margin: 0;
            text-align: center;
            border-radius: 20px 20px 0 0;
            position: relative;
        }
        .logout-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            color: white;
            text-decoration: none;
            font-size: 0.9em;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: color 0.3s ease, transform 0.3s ease;
        }
        .logout-btn i {
            margin-right: 5px;
        }
        .logout-btn:hover {
            color: #ffeb3b;
            transform: translateY(-2px);
        }
        .header h1 {
            color: white;
            font-size: 2.2em;
            margin: 0;
            font-weight: 600;
        }
        .content-body {
            padding: 30px;
        }
        .content-body p {
            color: #333;
            font-size: 1.1em;
            line-height: 1.6;
            margin: 0;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            border-left: 4px solid #28a745;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .role-badge {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 500;
            margin: 2px;
            transition: transform 0.3s ease;
        }
        .role-badge:hover {
            transform: scale(1.1);
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4>Dashboard Menu</h4>
        </div>
        <ul class="list-group">
            <?php if (in_array('admin', $roles)): ?>
                <li class="list-group-item">
                    <a href="../admin/dashboard_admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard Admin</a>
                </li>
            <?php endif; ?>

            <?php if (in_array('panitia', $roles)): ?>
                <li class="list-group-item">
                    <a href="../panitia/dashboard_panitia.php"><i class="fas fa-users"></i> Dashboard Panitia</a>
                </li>
            <?php endif; ?>

            <?php if (in_array('berqurban', $roles)): ?>
                <li class="list-group-item">
                    <a href="../Berqurban/dashboard_berqurban.php"><i class="fas fa-cow"></i> Dashboard Berqurban</a>
                </li>
            <?php endif; ?>

            <?php if (in_array('warga', $roles)): ?>
                <li class="list-group-item">
                    <a href="../warga/dashboard_warga.php"><i class="fas fa-home"></i> Dashboard Warga</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Konten -->
    <div class="content">
        <div class="header">
            <a href="../logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
            <h1>Selamat Datang di Dashboard!</h1>
        </div>
        <div class="content-body">
            <p>
                Anda login sebagai: 
                <?php foreach ($roles as $role): ?>
                    <span class="role-badge"><?php echo ucfirst($role); ?></span>
                <?php endforeach; ?>
            </p>
        </div>
    </div>
</body>

</html>