<?php
include '../db.php';
session_start();

// Cek login
// if (!isset($_SESSION['user_nik']) || $_SESSION['role'] !== 'admin') {
//     header('Location: ../login.php');
//     exit();
// }

// Koneksi ke DB
$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Ambil user yang login
$nik = $_SESSION['nik'];
$query_user = "SELECT * FROM users WHERE nik = '$nik'";
$result_user = mysqli_query($conn, $query_user);
$user = mysqli_fetch_assoc($result_user);

// Tentukan URL dashboard
$dashboard_url = ($_SESSION['role'] == 'admin') ? '../admin/dashboard_admin.php' : '../panitia/dashboard_panitia.php';

// Ambil data distribusi daging
$query_daging = "SELECT * FROM pembagian_daging ORDER BY id ASC";
$result_daging = mysqli_query($conn, $query_daging);

$daging_data = [];
if ($result_daging) {
  while ($row = mysqli_fetch_assoc($result_daging)) {
    $daging_data[] = $row;
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <title>Distribusi Daging - QURBANA</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Google Fonts: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
      margin: 20px auto;
      padding: 20px;
    }

    .header-section {
      background-color: #28a745;
      color: white;
      padding: 15px;
      border-radius: 12px 12px 0 0;
      text-align: center;
      margin-bottom: 20px;
      position: relative;
    }

    .back-button {
      position: absolute;
      left: 20px;
      top: 50%;
      transform: translateY(-50%);
      padding: 6px 14px;
      border: 2px solid white;
      border-radius: 6px;
      background-color: transparent;
      color: white;
      text-decoration: none;
      font-size: 0.9em;
      transition: all 0.3s ease-in-out;
    }

    .back-button:hover {
      background-color: white;
      color: #28a745;
    }

    .table-section {
      background-color: white;
      padding: 20px;
      border-radius: 0 0 12px 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .table th {
      background-color: #28a745;
      color: white;
      font-weight: 500;
      text-align: center;
    }

    .table td {
      vertical-align: middle;
      text-align: center;
    }

    .badge {
      font-size: 0.85em;
      padding: 6px 10px;
      border-radius: 10px;
    }

    .btn-warning {
      font-size: 0.85em;
      padding: 6px 12px;
      border-radius: 8px;
      font-weight: 500;
    }

    .btn-warning:hover {
      background-color: #e0a800;
      border-color: #d39e00;
    }

    .text-muted {
      font-style: Segoe UI, Arial, sans-serif;
    }
  </style>
</head>


<body>
  <div class="container">
    <!-- Header -->
    <div class="header-section">
      <a href="<?= $dashboard_url ?>" class="back-button"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
      <h1>Distribusi Daging</h1>
      <p>Kelola dan pantau distribusi daging qurban</p>
    </div>

    <!-- Tabel Distribusi -->
    <div class="table-section">
      <table class="table table-hover table-bordered">
        <thead>
          <tr>
            <th>No</th>
            <th>NIK Warga</th>
            <th>Status Pengambilan</th>
            <th>Tanggal Pengambilan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($daging_data) > 0): ?>
            <?php $no = 1;
            foreach ($daging_data as $row): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nik']) ?></td>
                <td>
                  <span class="badge <?= $row['status_pengambilan'] == 'sudah' ? 'bg-success' : 'bg-warning text-dark' ?>">
                    <?= ucfirst(htmlspecialchars($row['status_pengambilan'])) ?>
                  </span>
                </td>
                <td>
                  <?php
                  if (!empty($row['tanggal_pengambilan'])) {
                    echo date('d/m/Y H:i', strtotime($row['tanggal_pengambilan']));
                  } else {
                    echo '<span class="text-muted">-</span>';
                  }
                  ?>
                </td>
                <td>
                  <a href="../admin/edit_meat_distribution.php?id=<?= urlencode($row['id']) ?>" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i>Edit
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center text-muted">Belum ada data pembagian daging.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>