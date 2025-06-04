<?php
include('../db.php');
session_start();

// Hanya admin yang boleh mengakses
if (!isset($_SESSION['user_nik']) || $_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

// Cek apakah parameter NIK ada
if (!isset($_GET['nik']) || empty($_GET['nik'])) {
    header('Location: manage_users.php?error=nik_kosong');
    exit;
}

$nik = $_GET['nik'];

// Cek apakah user ada
$stmt_check = $pdo->prepare("SELECT nik FROM users WHERE nik = ?");
$stmt_check->execute([$nik]);
$user = $stmt_check->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: manage_users.php?error=user_tidak_ditemukan');
    exit;
}

// Cegah admin menghapus dirinya sendiri
if ($nik == $_SESSION['user_nik']) {
    header('Location: manage_users.php?error=tidak_bisa_hapus_diri_sendiri');
    exit;
}

try {
    $pdo->beginTransaction();

    // Hapus data qurban terkait (jika ada)
    $stmt_delete_qurban = $pdo->prepare("DELETE FROM hewan_qurban WHERE sumber = ?");
    $stmt_delete_qurban->execute([$nik]);

    // Hapus user
    $stmt_delete_user = $pdo->prepare("DELETE FROM users WHERE nik = ?");
    $stmt_delete_user->execute([$nik]);

    $pdo->commit();

    header(header: 'Location: manage_user.php?success=user_berhasil_dihapus');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: manage_user.php?success=user_berhasil_dihapus');
    exit;
}
?>