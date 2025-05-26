<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
}

include 'db.php';

// Tampilkan data pengguna, hewan qurban, dll. berdasarkan role admin
?>
<a href="logout.php">Logout</a>

<!-- Daftar pengguna -->
<h3>Users</h3>
<?php
$sql = "SELECT * FROM users";
$stmt = $pdo->query($sql);
while ($row = $stmt->fetch()) {
    echo $row['nama'] . " - " . $row['role'] . "<br>";
}
?>
