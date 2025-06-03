<?php
include('../db.php');

// Ambil semua data pengguna
$query = "SELECT * FROM users";
$stmt = $pdo->prepare($query);
$stmt->execute();
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
        }

        .action-link:hover {
            text-decoration: underline;
        }

        .top-actions {
            text-align: right;
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
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Manajemen Pengguna</h1>
        <p>Kelola data pengguna yang terdaftar dalam sistem qurban.</p>

        <div class="top-actions">
            <a href="register.php" class="btn">+ Tambah Pengguna</a>
        </div>

        <?php if ($stmt->rowCount() > 0): ?>
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
                            <td><?= ucfirst($row['role']) ?></td>
                            <td class="btn-group">
                                <a href="edit_user.php?nik=<?= $row['nik'] ?>" class="action-link">Edit</a> |
                                <a href="delete_user.php?nik=<?= $row['nik'] ?>" class="action-link" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; font-style: italic;">Tidak ada pengguna yang terdaftar.</p>
        <?php endif; ?>

        <div style="text-align: center;">
            <a href="dashboard_admin.php" class="btn">Kembali ke Dashboard</a>
        </div>
    </div>

</body>
</html>
