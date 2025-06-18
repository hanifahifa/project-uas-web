<?php
include('../db.php');
session_start();

// Hanya admin yang bisa mengakses
// if (!isset($_SESSION['user_nik']) || $_SESSION['role'] != 'admin') {
//     header('Location: ../login.php');
//     exit;
// }

// Cek apakah parameter NIK ada melalui POST
if (!isset($_POST['nik']) || empty($_POST['nik'])) {
    header('Location: manage_user.php?error=nik_kosong');
    exit;
}

$nik = $_POST['nik']; // Dapatkan nik dari POST

// Koneksi ke database
$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Cek apakah user ada
$query_check = "SELECT nik FROM users WHERE nik = '$nik'";
$result_check = mysqli_query($conn, $query_check);
$user = mysqli_fetch_assoc($result_check);

if (!$user) {
    header('Location: manage_user.php?error=user_tidak_ditemukan');
    exit;
}

// Cegah admin menghapus dirinya sendiri
if ($nik == $_SESSION['user_nik']) {
    header('Location: manage_user.php?error=tidak_bisa_hapus_diri_sendiri');
    exit;
}

// Hapus data qurban terkait (jika ada)
$query_delete_qurban = "DELETE FROM hewan_qurban WHERE sumber = '$nik'";
mysqli_query($conn, $query_delete_qurban);

// Hapus user
$query_delete_user = "DELETE FROM users WHERE nik = '$nik'";
if (mysqli_query($conn, $query_delete_user)) {
    header('Location: manage_user.php?success=user_berhasil_dihapus');
    exit;
} else {
    header('Location: manage_user.php?error=gagal_hapus_user');
    exit;
}
?>
