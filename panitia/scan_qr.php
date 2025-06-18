<?php
session_start();
include '../db.php';

// Pastikan pengguna sudah login dan memiliki peran 'panitia'
if (!isset($_SESSION['user_nik']) || $_SESSION['role'] !== 'panitia') {
    header('Location: ../login.php');
    exit();
}

// Handle konfirmasi pengambilan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_pickup'])) {
    $id_pembagian = $_POST['id_pembagian'];

    try {
        $update_sql = "UPDATE pembagian_daging SET status_pengambilan = 'sudah', tanggal_pengambilan = NOW() WHERE id = ?";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute([$id_pembagian]);

        $success_message = "Konfirmasi pengambilan berhasil!";
    } catch (Exception $e) {
        $error_message = "Terjadi kesalahan: " . $e->getMessage();
    }
}

// Handle pencarian berdasarkan QR data
$qr_data = null;
$meat_info = null;

if (isset($_GET['qr']) && !empty($_GET['qr'])) {
    $qr_data = $_GET['qr'];

    // Cari data berdasarkan QR code (asumsi QR berisi ID pembagian)
    $search_sql = "SELECT pd.*, u.nama as nama_penerima, u.alamat 
                   FROM pembagian_daging pd 
                   LEFT JOIN users u ON pd.nik_penerima = u.nik 
                   WHERE pd.id = ? OR pd.nik_penerima = ?";
    $search_stmt = $pdo->prepare($search_sql);
    $search_stmt->execute([$qr_data, $qr_data]);
    $meat_info = $search_stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code - QURBANA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
    <style>
        :root {
            --primary-dark: #1a4f2e;
            --primary-medium: #8fbc8f;
            --primary-light: #e8f5e8;
            --accent: #f4d4a7;
            --text-dark: #2c3e50;
            --text-light: #6c757d;
            --white: #ffffff;
            --border-radius: 16px;
            --shadow: 0 4px 20px rgba(26, 79, 46, 0.08);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--primary-light) 0%, #f8fffe 100%);
            min-height: 100vh;
            color: var(--text-dark);
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .header-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 1.5rem 2rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .scanner-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .result-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            border-left: 4px solid var(--primary-medium);
        }

        .btn-primary-custom {
            background: var(--primary-dark);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            background: var(--primary-medium);
            transform: translateY(-1px);
        }

        .btn-success-custom {
            background: #28a745;
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            color: white;
            font-weight: 500;
        }

        .btn-danger-custom {
            background: #dc3545;
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            color: white;
            font-weight: 500;
        }

        #qr-reader {
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-belum {
            background: #fff3cd;
            color: #856404;
        }

        .status-sudah {
            background: #d4edda;
            color: #155724;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--primary-light);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .google-lens-btn {
            background: #4285f4;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .google-lens-btn:hover {
            background: #3367d6;
            color: white;
            transform: translateY(-1px);
        }

        .manual-input {
            background: var(--primary-light);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-qrcode me-2"></i>
                        Scan QR Code
                    </h2>
                    <p class="text-muted mb-0">Verifikasi pengambilan daging qurban</p>
                </div>
                <a href="dashboard_panitia.php" class="btn btn-primary-custom">
                    <i class="fas fa-arrow-left me-1"></i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Scanner Section -->
        <div class="scanner-card">
            <h4 class="mb-3">
                <i class="fas fa-camera me-2"></i>
                Scanner QR Code
            </h4>

            <div class="row">
                <div class="col-md-8">
                    <div id="qr-reader" style="width: 100%; height: 0px;"></div>
                    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
                    <div id="reader" style="width: 300px;"></div>

                    <script>
                        function onScanSuccess(decodedText, decodedResult) {
                            alert(`QR Code terdeteksi: ${decodedText}`);
                            html5QrcodeScanner.clear(); // Stop scanning
                        }

                        const html5QrcodeScanner = new Html5QrcodeScanner(
                            "reader",
                            { fps: 10, qrbox: 250 },
                            false
                        );
                        html5QrcodeScanner.render(onScanSuccess);
                    </script>

                    <div class="d-flex gap-2 mt-3">
                        <button id="start-scan" class="btn btn-success-custom">
                            <i class="fas fa-play me-1"></i>
                            Mulai Scan
                        </button>
                        <button id="stop-scan" class="btn btn-danger-custom" style="display: none;">
                            <i class="fas fa-stop me-1"></i>
                            Stop Scan
                        </button>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="manual-input">
                        <h6>Input Manual</h6>
                        <p class="small text-muted">Jika tidak bisa scan, masukkan kode secara manual:</p>
                        <form method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" name="qr" placeholder="Masukkan kode QR">
                                <button class="btn btn-primary-custom" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Result Section -->
        <?php if ($meat_info): ?>
            <div class="result-card">
                <h4 class="mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Informasi Pengambilan Daging
                </h4>

                <div class="info-row">
                    <span class="fw-semibold">Nama Penerima:</span>
                    <span><?php echo htmlspecialchars($meat_info['nama_penerima'] ?? 'Tidak diketahui'); ?></span>
                </div>

                <div class="info-row">
                    <span class="fw-semibold">NIK:</span>
                    <span><?php echo htmlspecialchars($meat_info['nik_penerima']); ?></span>
                </div>

                <div class="info-row">
                    <span class="fw-semibold">Alamat:</span>
                    <span><?php echo htmlspecialchars($meat_info['alamat'] ?? 'Tidak diketahui'); ?></span>
                </div>

                <div class="info-row">
                    <span class="fw-semibold">Jumlah Daging:</span>
                    <span><?php echo number_format($meat_info['jumlah_kg'], 1); ?> Kg</span>
                </div>

                <div class="info-row">
                    <span class="fw-semibold">Status:</span>
                    <span
                        class="status-badge <?php echo $meat_info['status_pengambilan'] == 'sudah' ? 'status-sudah' : 'status-belum'; ?>">
                        <?php echo $meat_info['status_pengambilan'] == 'sudah' ? 'Sudah Diambil' : 'Belum Diambil'; ?>
                    </span>
                </div>

                <?php if ($meat_info['status_pengambilan'] == 'sudah' && $meat_info['tanggal_pengambilan']): ?>
                    <div class="info-row">
                        <span class="fw-semibold">Tanggal Pengambilan:</span>
                        <span><?php echo date('d/m/Y H:i', strtotime($meat_info['tanggal_pengambilan'])); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Konfirmasi Button -->
                <?php if ($meat_info['status_pengambilan'] == 'belum'): ?>
                    <div class="mt-4 pt-3 border-top">
                        <h5 class="mb-3">Konfirmasi Pengambilan</h5>
                        <form method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin mengkonfirmasi pengambilan daging ini?')">
                            <input type="hidden" name="id_pembagian" value="<?php echo $meat_info['id']; ?>">
                            <button type="submit" name="confirm_pickup" class="btn btn-success-custom btn-lg">
                                <i class="fas fa-check me-2"></i>
                                Konfirmasi Pengambilan
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="mt-4 pt-3 border-top">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Daging sudah diambil pada
                            <?php echo date('d/m/Y H:i', strtotime($meat_info['tanggal_pengambilan'])); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif (isset($_GET['qr'])): ?>
            <div class="result-card">
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Data tidak ditemukan untuk kode: <strong><?php echo htmlspecialchars($_GET['qr']); ?></strong>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let html5QrCode;

        // Update Google Lens button to open camera
        document.getElementById('google-lens-btn').addEventListener('click', function (e) {
            e.preventDefault();

            // For mobile devices, try to open the default camera app
            if (/Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                // Try to open Google Lens if available
                window.open('google-lens://scan', '_blank');

                // Fallback: show instructions
                setTimeout(() => {
                    alert('Instruksi:\n1. Buka aplikasi Google Lens atau kamera\n2. Arahkan ke QR Code\n3. Salin kode yang muncul\n4. Masukkan di kolom Input Manual');
                }, 1000);
            } else {
                // For desktop, show instructions
                alert('Instruksi untuk menggunakan Google Lens:\n1. Buka Google Lens di ponsel Anda\n2. Scan QR Code\n3. Salin kode yang didapat\n4. Masukkan di kolom Input Manual di samping');
            }
        });

        function onScanSuccess(decodedText, decodedResult) {
            console.log(`Code matched = ${decodedText}`, decodedResult);

            // Stop scanning
            html5QrCode.stop().then((ignore) => {
                document.getElementById('start-scan').style.display = 'inline-block';
                document.getElementById('stop-scan').style.display = 'none';
            }).catch((err) => {
                console.log('Error stopping scan:', err);
            });

            // Redirect with QR data
            window.location.href = `scan_qr.php?qr=${encodeURIComponent(decodedText)}`;
        }

        function onScanFailure(error) {
            // Handle scan failure - usually just noise, don't log every failure
        }

        document.getElementById('start-scan').addEventListener('click', function () {
            html5QrCode = new Html5Qrcode("qr-reader");

            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    // Use back camera if available
                    let cameraId = devices[0].id;
                    if (devices.length > 1) {
                        // Try to find back camera
                        for (let device of devices) {
                            if (device.label.toLowerCase().includes('back') ||
                                device.label.toLowerCase().includes('rear') ||
                                device.label.toLowerCase().includes('environment')) {
                                cameraId = device.id;
                                break;
                            }
                        }
                    }

                    html5QrCode.start(
                        cameraId,
                        {
                            fps: 10,
                            qrbox: { width: 250, height: 250 }
                        },
                        onScanSuccess,
                        onScanFailure
                    ).then(() => {
                        document.getElementById('start-scan').style.display = 'none';
                        document.getElementById('stop-scan').style.display = 'inline-block';
                    }).catch((err) => {
                        console.log('Error starting scan:', err);
                        alert('Tidak dapat mengakses kamera. Pastikan Anda telah memberikan izin akses kamera.');
                    });
                }
            }).catch(err => {
                console.log('Error getting cameras:', err);
                alert('Tidak dapat mengakses kamera. Gunakan input manual sebagai alternatif.');
            });
        });

        document.getElementById('stop-scan').addEventListener('click', function () {
            if (html5QrCode) {
                html5QrCode.stop().then((ignore) => {
                    document.getElementById('start-scan').style.display = 'inline-block';
                    document.getElementById('stop-scan').style.display = 'none';
                }).catch((err) => {
                    console.log('Error stopping scan:', err);
                });
            }
        });
    </script>


</body>

<script>
    let html5QrCode;
    let isScanning = false;

    const qrReaderElement = document.getElementById("qr-reader");
    const startButton = document.getElementById("start-scan");
    const stopButton = document.getElementById("stop-scan");

    startButton.addEventListener("click", () => {
        if (isScanning) return;

        html5QrCode = new Html5Qrcode("qr-reader");

        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                const cameraId = devices[0].id;

                html5QrCode.start(
                    cameraId,
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 250 }
                    },
                    qrCodeMessage => {
                        // Redirect to the same page with QR data in GET
                        window.location.href = "?qr=" + encodeURIComponent(qrCodeMessage);
                    },
                    errorMessage => {
                        // Optional: log scanning errors
                        console.warn("Scanning error: ", errorMessage);
                    }
                ).then(() => {
                    isScanning = true;
                    startButton.style.display = "none";
                    stopButton.style.display = "inline-block";
                }).catch(err => {
                    alert("Gagal memulai kamera: " + err);
                });
            }
        }).catch(err => {
            alert("Tidak dapat mengakses kamera: " + err);
        });
    });

    stopButton.addEventListener("click", () => {
        if (html5QrCode && isScanning) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                isScanning = false;
                startButton.style.display = "inline-block";
                stopButton.style.display = "none";
            }).catch(err => {
                alert("Gagal menghentikan kamera: " + err);
            });
        }
    });
</script>

</html>