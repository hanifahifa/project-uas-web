<?php
session_start();
include '../db.php'; // koneksi ke database

$meat_info = null;
$error_message = "";

// --- Proses Konfirmasi Pengambilan ---
if (isset($_POST['confirm_pickup'])) {
    $id_pembagian = $_POST['id_pembagian'];
    $qr_code = $_POST['nik_qr'];
    $tanggal_pengambilan = date('Y-m-d H:i:s');

    $update = $conn->prepare("UPDATE pembagian_daging SET status_pengambilan = 'sudah', tanggal_pengambilan = ? WHERE id = ?");
    $update->bind_param("si", $tanggal_pengambilan, $id_pembagian);
    $update->execute();

    // Set ulang GET agar data tampil sesuai hasil update
    $_GET['qr'] = $qr_code;
}

// --- Ambil data berdasarkan QR ---
if (isset($_GET['qr'])) {
    $qr_code = $_GET['qr'];

    $stmt = $conn->prepare("
        SELECT pd.*, u.name AS nama_penerima, u.alamat, u.jenis_kelamin
        FROM pembagian_daging pd
        JOIN users u ON pd.nik = u.nik
        WHERE pd.nik = ?
    ");
    $stmt->bind_param("s", $qr_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $meat_info = $result->fetch_assoc();

    if (!$meat_info) {
        $error_message = "Data tidak ditemukan untuk kode: " . htmlspecialchars($qr_code);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Scan QR Code - Qurbana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>

<body style="background-color: #f0fdf5;">
    <div class="container py-5">
        <h2 class="mb-4"><i class="fa-solid fa-qrcode me-2"></i>Scan QR Code Qurbana</h2>
        <a href="dashboard_panitia.php" class="btn btn-dark mb-4">
            <i class="fa fa-arrow-left me-1"></i>Kembali
        </a>
        <p class="text-muted mb-4">Silakan scan QR code atau masukkan kode secara manual untuk mendapatkan informasi pengambilan daging qurban.</p>

        <!-- Scanner Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title"><i class="fa fa-camera me-2"></i>Scanner QR</h5>
                <div id="qr-reader" style="width: 100%; max-width: 500px; margin: auto;"></div>
                <div class="text-center mt-3">
                    <button id="start-scan" class="btn btn-primary">Start Scanning</button>
                    <button id="stop-scan" class="btn btn-danger" style="display: none;">Stop Scanning</button>
                </div>
                <hr>
                <h6>Input NIK disini untuk cek manual</h6>
                <form method="GET" class="row g-2">
                    <div class="col-10">
                        <input type="text" name="qr" class="form-control" placeholder="Masukkan kode QR" required>
                    </div>
                    <div class="col-2">
                        <button type="submit" class="btn btn-success w-100"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Result -->
        <?php if ($meat_info): ?>
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <i class="fa fa-check-circle me-2"></i>Informasi Pengambilan Daging
                </div>
                <div class="card-body">
                    <p><strong>Nama Penerima:</strong> <?= htmlspecialchars($meat_info['nama_penerima']) ?></p>
                    <p><strong>NIK:</strong> <?= htmlspecialchars($meat_info['nik']) ?></p>
                    <p><strong>Alamat:</strong> <?= htmlspecialchars($meat_info['alamat']) ?></p>
                    <p><strong>Jenis Kelamin:</strong> <?= htmlspecialchars($meat_info['jenis_kelamin']) ?></p>
                    <p><strong>Jumlah Daging:</strong> <?= number_format($meat_info['jumlah_kg'], 1) ?> Kg</p>
                    <p><strong>Status:</strong>
                        <span class="badge <?= $meat_info['status_pengambilan'] == 'sudah' ? 'bg-success' : 'bg-warning text-dark' ?>">
                            <?= $meat_info['status_pengambilan'] == 'sudah' ? 'Sudah Diambil' : 'Belum Diambil' ?>
                        </span>
                    </p>

                    <?php if ($meat_info['status_pengambilan'] == 'sudah' && $meat_info['tanggal_pengambilan']): ?>
                        <p><strong>Tanggal Pengambilan:</strong>
                            <?= date('d/m/Y H:i', strtotime($meat_info['tanggal_pengambilan'])) ?></p>
                    <?php endif; ?>

                    <?php if ($meat_info['status_pengambilan'] == 'belum'): ?>
                        <form method="POST" class="mt-3" onsubmit="return confirm('Yakin ingin mengkonfirmasi pengambilan?')">
                            <input type="hidden" name="id_pembagian" value="<?= $meat_info['id'] ?>">
                            <input type="hidden" name="nik_qr" value="<?= $meat_info['nik'] ?>">
                            <button type="submit" name="confirm_pickup" class="btn btn-success">
                                <i class="fas fa-check me-1"></i> Konfirmasi Pengambilan
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif ($error_message): ?>
            <div class="alert alert-warning mt-3">
                <i class="fa fa-exclamation-triangle me-2"></i><?= $error_message ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Scanner Script -->
    <script>
        const qrReader = new Html5Qrcode("qr-reader");
        const startBtn = document.getElementById("start-scan");
        const stopBtn = document.getElementById("stop-scan");

        startBtn.addEventListener("click", () => {
            Html5Qrcode.getCameras().then(devices => {
                if (devices.length > 0) {
                    qrReader.start(devices[0].id, {
                        fps: 10,
                        qrbox: 250
                    }, qr => {
                        window.location.href = "scan_qr.php?qr=" + encodeURIComponent(qr);
                    });
                    startBtn.style.display = "none";
                    stopBtn.style.display = "inline-block";
                }
            }).catch(console.error);
        });

        stopBtn.addEventListener("click", () => {
            qrReader.stop().then(() => {
                startBtn.style.display = "inline-block";
                stopBtn.style.display = "none";
            }).catch(console.error);
        });
    </script>
</body>
</html>
