<?php
include('../db.php');
session_start();



// Cek apakah ada parameter 'nik' yang dikirimkan
if (isset($_GET['nik'])) {
    $nik = $_GET['nik'];

    // Hapus data pengguna dari tabel 'users'
    $query = "DELETE FROM users WHERE nik = '$nik'";
    $result = mysqli_query($conn, $query);

    // Periksa apakah penghapusan berhasil
    if ($result) {
        // Redirect dengan pesan sukses
        header("Location: manage_user.php?success=Pengguna berhasil dihapus.");
    } else {
        // Redirect dengan pesan error
        header("Location: manage_user.php?error=Gagal menghapus pengguna.");
    }
} else {
    // Redirect jika tidak ada parameter 'nik'
    header("Location: manage_user.php?error=Pengguna tidak ditemukan.");
}
exit;
?>
