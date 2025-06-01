<?php
include('../db.php'); // Menghubungkan ke file db.php

// Query untuk mengambil semua data pengguna
$query = "SELECT * FROM users";

// Menyiapkan dan mengeksekusi query menggunakan PDO
$stmt = $pdo->prepare($query);
$stmt->execute();

// Menampilkan header halaman
echo '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna</title>
    <link rel="stylesheet" href="styles.css"> <!-- Anda bisa menambahkan file CSS untuk styling -->
    
</head>
<body>

    <div class="container">
        <h1>Manajemen Pengguna</h1>
        <p>Kelola pengguna yang terdaftar dalam sistem.</p>
        <a href="register.php"><button>Tambah Pengguna</button></a>'; // Button untuk menambah pengguna

// Jika ada data pengguna
if ($stmt->rowCount() > 0) {
    echo '<table border="1">
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Jenis Kelamin</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>';
    
    // Menampilkan setiap pengguna
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>
                <td>' . $row['nik'] . '</td>
                <td>' . $row['name'] . '</td>
                <td>' . ($row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan') . '</td>
                <td>' . $row['email'] . '</td>
                <td>' . ucfirst($row['role']) . '</td>
                <td>
                    <a href="edit_user.php?nik=' . $row['nik'] . '">Edit</a> | 
                    <a href="delete_user.php?nik=' . $row['nik'] . '" onclick="return confirm(\'Apakah Anda yakin ingin menghapus pengguna ini?\')">Hapus</a>
                </td>
              </tr>';
    }
    
    echo '</tbody></table>';
} else {
    echo '<p>Tidak ada pengguna yang terdaftar.</p>';
}

// Tambahkan tombol "Back" yang mengarahkan ke dashboard_admin.php
echo '<a href="dashboard_admin.php"><button>Kembali ke Dashboard</button></a>';

echo '</div>
</body>
</html>';
?>
