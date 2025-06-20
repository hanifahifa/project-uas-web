<?php
session_start();  // Make sure session_start() is called at the top

include '../db.php';  // Connecting to the database

// Connect to MySQL
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check if the connection is successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve the user's data based on their nik (from session)
$nik = $_SESSION['nik'];  // Use the session's nik directly
$query = mysqli_prepare($conn, "SELECT * FROM users WHERE nik = ?");
mysqli_stmt_bind_param($query, 's', $nik);  // Use the nik from the session
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);
$user = mysqli_fetch_assoc($result);

// Get additional data for Panitia (such as qurban statistics)
$ambil_data_qurban_sql = "SELECT * FROM pembagian_daging";
$ambil_data_qurban = mysqli_query($conn, $ambil_data_qurban_sql);
$data_qurban = mysqli_fetch_all($ambil_data_qurban, MYSQLI_ASSOC);

// Calculate the total weight of the meat distributed
$total_daging = 0;
$jumlah_daging_sudah_diambil = 0;
foreach ($data_qurban as $row) {
    $total_daging += $row['jumlah_kg'];
    if ($row['status_pengambilan'] == 'sudah') {
        $jumlah_daging_sudah_diambil += $row['jumlah_kg'];
    }
}

// Calculate the percentage of meat distributed
$persen_daging_terdistribusi = ($total_daging > 0) ? ($jumlah_daging_sudah_diambil / $total_daging) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Panitia - QURBANA</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f7f7f7;
        }

        .dashboard-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 20px 30px;
            background-color: #28a745;
            color: white;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .header-section h1 {
            font-size: 1.5em;
            margin: 0;
        }

        .welcome-text p {
            margin: 0;
            font-size: 0.9em;
            opacity: 0.9;
        }

        .header-buttons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .kembali-btn, .logout-btn {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9em;
        }

        .kembali-btn {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .kembali-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateY(-1px);
        }

        .logout-btn {
            background-color: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.4);
        }

        .logout-btn:hover {
            background-color: rgba(220, 53, 69, 0.3);
            color: white;
            transform: translateY(-1px);
        }

        .stats-grid {
            display: flex;
            justify-content: space-between;
            width: 100%;
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 12px;
            width: 48%;
        }

        .stat-card h3 {
            margin-bottom: 15px;
            font-size: 1.2em;
            display: flex;
            align-items: center;
        }

        .stat-card .icon {
            margin-right: 10px;
        }

        .stat-card .progress-container {
            margin-top: 10px;
        }

        .stat-card .progress-bar {
            height: 10px;
            border-radius: 5px;
        }

        .menu-grid {
            display: flex;
            justify-content: space-between;
            width: 100%;
            gap: 20px;
        }

        .menu-card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 32%;
            text-align: center;
        }

        .menu-btn {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            margin-top: 15px;
            text-decoration: none;
        }

        .menu-btn:hover {
            background: #1f7a38;
        }

        .card-modern {
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            text-align: center;
        }

        .qr-container {
            display: none;
            margin-top: 20px;
        }

        .qr-container img {
            border-radius: 8px;
            max-width: 200px;
        }

        .btn-qr {
            background: linear-gradient(90deg, #28a745, #1f7a38);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            display: block;
            margin: 10px auto;
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .btn-qr:hover {
            background: linear-gradient(90deg, #1f7a38, #145a2a);
            transform: translateY(-2px);
        }

        .btn-qr:active {
            transform: translateY(0);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .header-section {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .header-buttons {
                width: 100%;
                justify-content: center;
            }

            .stats-grid, .menu-grid {
                flex-direction: column;
            }

            .stat-card, .menu-card {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="welcome-text">
                <h1>Dashboard Panitia</h1>
                <p>Selamat datang, <?php echo htmlspecialchars($user['name'] ?? $user['nama'] ?? 'Panitia'); ?></p>
            </div>

            <div class="header-buttons">
                <!-- Kembali Button -->            
                <a href="../Dashboard_Utama/dashboard.php" class="kembali-btn">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                
                <!-- Logout Button -->
                <a href="../logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="stats-grid">
            <!-- Progress Card -->
            <div class="stat-card">
                <h3>
                    <div class="icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    Progress Distribusi
                </h3>
                <div class="percentage-text"><?php echo number_format($persen_daging_terdistribusi, 1); ?>%</div>
                <p class="stat-label">dari total daging telah terdistribusi</p>
                <div class="progress-container">
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $persen_daging_terdistribusi; ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Statistik Daging Qurban -->
            <div class="stat-card">
                <h3>
                    <div class="icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    Statistik Daging Qurban
                </h3>
                <div class="stat-detail">
                    <span class="stat-label">Total Daging Tersedia</span>
                    <span class="stat-value"><?php echo number_format($total_daging, 1); ?> Kg</span>
                </div>
                <div class="stat-detail">
                    <span class="stat-label">Sudah Terdistribusi</span>
                    <span class="stat-value"><?php echo number_format($jumlah_daging_sudah_diambil, 1); ?> Kg</span>
                </div>
                <div class="stat-detail">
                    <span class="stat-label">Belum Terdistribusi</span>
                    <span class="stat-value"><?php echo number_format($total_daging - $jumlah_daging_sudah_diambil, 1); ?> Kg</span>
                </div>
            </div>
        </div>

        <!-- QR Code Button -->
        <div class="card-modern">
            <h3>
                <div class="icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                QR Code Pengambilan
            </h3>
            <button id="showQrButton" class="btn-qr">
                Lihat QR
            </button>
            <div class="qr-container" id="qrContainer">
                <?php
                $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode("NIK: " . $user['nik'] . " - " . $user['name']);
                ?>
                <img src="<?php echo $qr_url; ?>" alt="QR Code">
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Tunjukkan QR Code ini saat pengambilan daging qurban</p>
            </div>
        </div>

        <!-- Menu Section -->
        <div class="menu-grid">
            <!-- Distribusi Daging -->
            <div class="menu-card">
                <h3>
                    <div class="icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    Distribusi Daging
                </h3>
                <p>Kelola status pengambilan daging qurban oleh warga dan pantau distribusi secara real-time.</p>
                <a href="../admin/meat_distribution.php" class="menu-btn">
                    <i class="fas fa-arrow-right"></i>
                    Kelola Distribusi
                </a>
            </div>

            <!-- Laporan Keuangan -->
            <div class="menu-card">
                <h3>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    Laporan Keuangan
                </h3>
                <p>Lihat dan kelola laporan keuangan kegiatan qurban, termasuk pemasukan dan pengeluaran.</p>
                <a href="../admin/financial_report.php" class="menu-btn">
                    <i class="fas fa-arrow-right"></i>
                    Lihat Laporan
                </a>
            </div>

            <!-- Scan QR Code -->
            <div class="menu-card">
                <h3>
                    <div class="icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    Scan QR Code
                </h3>
                <p>Verifikasi pengambilan daging menggunakan QR Code untuk proses yang lebih efisien dan akurat.</p>
                <a href="scan_qr.php" class="menu-btn">
                    <i class="fas fa-arrow-right"></i>
                    Mulai Scan
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2025 QURBANA - Sistem Manajemen Qurban RT 001</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle visibility of QR code container when button is clicked
        document.getElementById("showQrButton").addEventListener("click", function() {
            var qrContainer = document.getElementById("qrContainer");
            qrContainer.style.display = (qrContainer.style.display === "block") ? "none" : "block";
        });
    </script>
</body>

</html>