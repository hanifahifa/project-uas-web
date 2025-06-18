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

// Tentukan laman dashboard berdasarkan peran
$dashboard_url = ($_SESSION['role'] == 'admin') ? '../admin/dashboard_admin.php' : '../panitia/dashboard_panitia.php';

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
    body {
      background-color: #f0f8f0;
      font-family: Arial, sans-serif;
    }

    .container {
      max-width: 800px;
      margin: 20px auto;
      padding: 20px;
    }

    .header-section {
      background-color: #28a745;
      color: white;
      padding: 15px;
      border-radius: 10px 10px 0 0;
      text-align: center;
      margin-bottom: 20px;
      position: relative;
      overflow: hidden; /* Menangani overflow tombol */
    }

    .back-button {
      color: white;
      text-decoration: none;
      font-size: 0.9em;
      position: absolute;
      left: 20px;
      top: 50%;
      transform: translateY(-50%);
      padding: 5px 10px;
      border: 2px solid white;
      border-radius: 5px;
      background: transparent;
      transition: all 0.3s ease;
    }

    .back-button:hover {
      color: #e0e0e0;
      border-color: #e0e0e0;
    }

    .header-section h1 {
      font-size: 1.5em;
      margin: 0;
    }

    .header-section p {
      font-size: 0.9em;
      margin: 5px 0 0;
    }

    .table-section {
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .table {
      margin-bottom: 0;
    }

    .table th {
      background-color: #28a745;
      color: white;
    }

    .table td {
      vertical-align: middle;
    }

    .btn-warning {
      background-color: #ffc107;
      border-color: #ffc107;
      color: #333;
      padding: 5px 10px;
      border-radius: 5px;
    }

    .btn-warning:hover {
      background-color: #e0a800;
      border-color: #e0a800;
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Header Section -->
    <div class="header-section">
      <a href="<?php echo $dashboard_url; ?>" class="back-button"><i class="fas fa-arrow-left"></i> Kembali</a>
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