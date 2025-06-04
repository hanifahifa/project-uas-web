<?php
include('../db.php');
session_start();

// Hanya admin yang dapat mengakses
if (!isset($_SESSION['user_nik']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Ambil semua data pengguna
$query = "SELECT * FROM users ORDER BY name";
$stmt = $pdo->prepare($query);
$stmt->execute();

// Ambil pesan sukses/error dari URL
$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    switch($_GET['success']) {
        case 'user_berhasil_dihapus':
            $success_message = 'Pengguna berhasil dihapus dari sistem!';
            break;
    }
}

if (isset($_GET['error'])) {
    switch($_GET['error']) {
        case 'nik_kosong':
            $error_message = 'NIK pengguna tidak valid!';
            break;
        case 'user_tidak_ditemukan':
            $error_message = 'Pengguna tidak ditemukan!';
            break;
        case 'tidak_bisa_hapus_diri_sendiri':
            $error_message = 'Anda tidak bisa menghapus akun Anda sendiri!';
            break;
        case 'gagal_menghapus_user':
            $error_message = 'Gagal menghapus pengguna. Silakan coba lagi!';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna</title>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
        }

        .container {
            max-width: 1000px;
            margin: 60px auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: rgb(9, 62, 36);
            font-weight: bold;
            margin-bottom: 10px;
        }

        p {
            text-align: center;
            margin-bottom: 30px;
            color: #555;
        }

        /* Alert Messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-weight: 600;
        }

        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: rgb(9, 62, 36);
            color: #ffffff;
        }

        tr:hover {
            background-color: #f0f9f0;
        }

        .btn {
            padding: 10px 16px;
            background-color: rgb(9, 62, 36);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            font-size: 15px;
            transition: background-color 0.2s ease-in-out;
        }

        .btn:hover {
            background-color: rgb(12, 97, 42);
        }

        .btn-danger {
            background-color: #c0392b;
        }

        .btn-danger:hover {
            background-color: #e74c3c;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .action-link {
            color: rgb(9, 62, 36);
            text-decoration: none;
            font-weight: 600;
            padding: 5px 8px;
            border-radius: 5px;
            transition: background-color 0.2s;
        }

        .action-link:hover {
            background-color: #f0f9f0;
            text-decoration: none;
        }

        .action-link.delete {
            color: #c0392b;
        }

        .action-link.delete:hover {
            background-color: #fdf2f2;
        }

        .top-actions {
            text-align: right;
            margin-bottom: 20px;
        }

        .user-count {
            text-align: center;
            color: #666;
            font-style: italic;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .btn {
                width: 100%;
                display: block;
                margin-bottom: 10px;
            }

            .top-actions {
                text-align: center;
            }

            table {
                font-size: 14px;
            }
            
            .action-link {
                display: block;
                margin: 2px 0;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Manajemen Pengguna</h1>
        <p>Kelola data pengguna yang terdaftar dalam sistem qurban.</p>

        <!-- Alert Messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                ✅ <?= $success_message ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                ❌ <?= $error_message ?>
            </div>
        <?php endif; ?>

        <div class="top-actions">
            <a href="register.php" class="btn">+ Tambah Pengguna</a>
        </div>

        <?php if ($stmt->rowCount() > 0): ?>
            <div class="user-count">
                Total: <?= $stmt->rowCount() ?> pengguna terdaftar
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
                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nik']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                            <td>
                                <span style="padding: 4px 8px; background-color: 
                                    <?php 
                                        switch($row['role']) {
                                            case 'admin': echo '#dc2626'; break;
                                            case 'panitia': echo '#2563eb'; break;
                                            case 'berqurban': echo '#16a34a'; break;
                                            default: echo '#6b7280';
                                        }
                                    ?>; 
                                    color: white; border-radius: 12px; font-size: 12px;">
                                    <?= ucfirst($row['role']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_user.php?nik=<?= $row['nik'] ?>" class="action-link">
                                    ✏️ Edit
                                </a>
                                |
                                <?php if ($row['nik'] != $_SESSION['user_nik']): ?>
                                    <a href="delete_user.php?nik=<?= $row['nik'] ?>" class="action-link delete">
                                        🗑️ Hapus
                                    </a>
                                <?php else: ?>
                                    <span style="color: #ccc; font-style: italic;">Anda</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px;">
                <p style="font-size: 18px; color: #999; font-style: italic;">
                    📋 Tidak ada pengguna yang terdaftar.
                </p>
            </div>
        <?php endif; ?>

        <div style="text-align: center;">
            <a href="dashboard_admin.php" class="btn">← Kembali ke Dashboard</a>
        </div>
    </div>

</body>
</html>
