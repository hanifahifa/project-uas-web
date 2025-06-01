<?php
session_start();

// Cek apakah user sudah login dan apakah dia panitia
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'panitia') {
    header('Location: login.php');  // Redirect ke halaman login jika bukan panitia
    exit;
}

include 'header.php';
?>

<div class="container mt-4">
    <h1>Panitia Dashboard</h1>
    <!-- Konten panitia -->
</div>

<?php
include 'footer.php';
?>
