<?php
include('../db.php');
session_start();

// Hanya admin yang bisa mengakses
// if (!isset($_SESSION['user_nik']) || $_SESSION['role'] != 'admin') {
//     header('Location: ../login.php');
//     exit;
// }

// Ambil data pengguna
$query = "SELECT * FROM users ORDER BY name";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f0f8f0;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            width: 100%;
        }
        .header {
            background-color: #28a745;
            color: white;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 2em;
            margin: 0;
        }
        p {
            color: #555;
            font-size: 1em;
            margin: 5px 0;
        }
        .top-actions {
            text-align: right;
            margin-bottom: 20px;
        }
        .btn {
            background-color: #28a745;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #1f7a38;
        }
        .user-count {
            color: #28a745;
            font-weight: bold;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #28a745;
            color: white;
        }
        td a {
            color: #ffc107;
            text-decoration: none;
            margin-right: 10px;
        }
        td a:hover {
            color: #e0a800;
        }
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .no-users {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
            font-size: 18px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Manajemen Pengguna</h1>
            <p>Kelola data pengguna yang terdaftar dalam sistem qurban.</p>
        </div>

        <!-- Pesan Sukses/Error -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                ✅ <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                ❌ <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <div class="top-actions">
            <a href="register.php" class="btn">+ Tambah Pengguna</a>
        </div>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="user-count">
                Total: <?= mysqli_num_rows($result) ?> pengguna terdaftar
            </div>

            <table>
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM users ORDER BY name";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)):
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nik']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                            <td>
                                <?php
                                $roles_query = "SELECT role FROM user_roles WHERE nik = '" . $row['nik'] . "'";
                                $roles_result = mysqli_query($conn, $roles_query);
                                $roles = [];
                                while ($role_row = mysqli_fetch_array($roles_result)) {
                                    $roles[] = $role_row['role'];
                                }
                                echo implode(", ", $roles);
                                ?>
                            </td>
                            <td>
                                <a href="edit_user.php?nik=<?= $row['nik'] ?>">Tambah role</a> |
                                <a href="delete_user.php?nik=<?= $row['nik'] ?>">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-users">
                📋 Tidak ada pengguna yang terdaftar.
            </div>
        <?php endif; ?>

        <div class="back-link">
            <a href="dashboard_admin.php" class="btn">← Kembali ke Dashboard</a>
        </div>
    </div>
</body>

</html>