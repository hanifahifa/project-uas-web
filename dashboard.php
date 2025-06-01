<<<<<<< HEAD
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
}

include 'db.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch();

echo "Welcome, " . $user['nama'] . "!";
echo "Role: " . $user['role'];
?>
<a href="logout.php">Logout</a>
=======
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
}

include 'db.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch();

echo "Welcome, " . $user['nama'] . "!";
echo "Role: " . $user['role'];
?>
<a href="logout.php">Logout</a>
>>>>>>> ab62da35eb63e7c2f4ea160ddd8babd3ec785d05
