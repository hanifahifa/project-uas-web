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
</head>

<body>

    <div class="container">
        <h1>Manajemen Pengguna</h1>
        <p>Kelola data pengguna yang terdaftar dalam sistem qurban.</p>

        <!-- Pesan Sukses/Error -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                ✅ <?= $_GET['success'] ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                ❌ <?= $_GET['error'] ?>
            </div>
        <?php endif; ?>

        <div class="top-actions">
            <a href="register.php" class="btn">+ Tambah Pengguna</a>
        </div>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="user-count">
                Total: <?= mysqli_num_rows($result) ?> pengguna terdaftar
            </div>

            <?php
            // Ambil data pengguna
            $query = "SELECT * FROM users ORDER BY name";
            $result = mysqli_query($conn, $query);
            ?>



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
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nik']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                            <td>
                                <?php
                                // Ambil role pengguna dari tabel user_roles
                                $roles_query = "SELECT role FROM user_roles WHERE nik = '" . $row['nik'] . "'";
                                $roles_result = mysqli_query($conn, $roles_query);
                                $roles = [];
                                while ($role_row = mysqli_fetch_array($roles_result)) {
                                    $roles[] = $role_row['role'];  // Simpan setiap role ke dalam array
                                }
                                echo implode(", ", $roles);  // Gabungkan role dengan koma
                                ?>
                            </td>
                            <td>
                                <a href="edit_user.php?nik=<?= $row['nik'] ?>">Edit</a> |
                                <a href="delete_user.php?nik=<?= $row['nik'] ?>">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>


        <?php else: ?>
            <div style="text-align: center; padding: 40px;">
                <p style="font-size: 18px; color: #999; font-style: italic;">📋 Tidak ada pengguna yang terdaftar.</p>
            </div>
        <?php endif; ?>

        <div style="text-align: center;">
            <a href="dashboard_admin.php" class="btn">← Kembali ke Dashboard</a>
        </div>
    </div>

</body>

</html>