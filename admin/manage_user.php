<?php
include('../db.php');
session_start();



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
            background: linear-gradient(135deg, #f0f8f0, #e0f0e0);
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            min-height: 100vh;
            overflow: auto;
        }

        .container {
            max-width: 900px;
            width: 100%;
        }

        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 25px;
            border-radius: 15px 15px 0 0;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            font-size: 2.2em;
            margin: 0;
            font-weight: 600;
        }

        p {
            color: #666;
            font-size: 1.1em;
            margin: 5px 0;
        }

        .top-actions {
            text-align: right;
            margin-bottom: 20px;
        }

        .btn {
            background: linear-gradient(90deg, #28a745, #1f7a38);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .btn:hover {
            background: linear-gradient(90deg, #1f7a38, #145a2a);
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .user-count {
            color: #28a745;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 1.1em;
        }

        table {
            width: 100%;
            border-collapse: separate;
            background-color: white;
            border-radius: 15px;
            overflow: auto;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            transition: background-color 0.3s ease;
        }

        th {
            background-color: #28a745;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        tr:hover td {
            background-color: #f8f9fa;
        }

        td a {
            color: #ffc107;
            text-decoration: none;
            margin-right: 15px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        td a:hover {
            color: #e0a800;
            transform: translateX(5px);
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid;
            animation: fadeIn 0.5s ease-in-out;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .no-users {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
            font-size: 1.2em;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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
                ✅ <?= htmlspecialchars(str_replace('_', ' ', $_GET['success'])) ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                ✅ <?= htmlspecialchars(str_replace('_', ' ', $_GET['error'])) ?>
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
                                // Mengambil role dengan GROUP_CONCAT agar hanya tampil satu role
                                $nik = mysqli_real_escape_string($conn, $row['nik']);  // Escape input untuk menghindari SQL Injection
                                $roles_query = "
                                    SELECT GROUP_CONCAT(DISTINCT role ORDER BY role) AS role
                                    FROM user_roles
                                    WHERE nik = '$nik'
                                    GROUP BY nik
                                ";
                                $roles_result = mysqli_query($conn, $roles_query);
                                $roles = mysqli_fetch_assoc($roles_result);
                                echo htmlspecialchars($roles['role']);
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