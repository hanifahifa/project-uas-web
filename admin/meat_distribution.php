<?php
// Menghubungkan ke database menggunakan MySQLi
include '../db.php';
session_start();

// Hanya admin yang dapat mengakses
// if (!isset($_SESSION['user_nik']) || $_SESSION['role'] !== 'admin') {
//     header('Location: ../login.php');
//     exit();
// }

// Membuat koneksi ke MySQL
$conn = mysqli_connect($host, $username, $password, $dbname);

// Mengecek apakah koneksi berhasil
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ambil data pengguna yang login
$nik = $_SESSION['nik'];
$query = "SELECT * FROM users WHERE nik = '$nik'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Ambil data distribusi daging dan urutkan berdasarkan id terkecil
$query_daging = "SELECT * FROM pembagian_daging ORDER BY id ASC";  // Ganti pengurutan menjadi ASC
$result_daging = mysqli_query($conn, $query_daging);

$daging_data = [];
if ($result_daging) {
    while ($row = mysqli_fetch_assoc($result_daging)) {
        $daging_data[] = $row;
    }
} else {
    echo "Query Pembagian Daging Error!";
}

// Menampilkan data
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Distribusi Daging - QURBANA</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    /* Styling goes here */
    /* Customize as needed */
  </style>
</head>

<body>
  <div class="container">
    <!-- Header Section -->
    <div class="header-section">
      <a href="../admin/dashboard_admin.php" class="header-title"> back<a>
        <h1>QURBANA</h1>
      <h1>Distribusi Daging</h1>
      <p>Kelola dan pantau distribusi daging qurban</p>
    </div>

    <!-- Data Pembagian Daging -->
    <div class="table-section">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>NIK Warga</th>
            <th>Status Pengambilan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($daging_data as $row): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['id']); ?></td>
              <td><?php echo htmlspecialchars($row['nik']); ?></td>
              <td><?php echo htmlspecialchars($row['status_pengambilan']); ?></td>
              <td>
                <a href="../admin/edit_meat_distribution.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
