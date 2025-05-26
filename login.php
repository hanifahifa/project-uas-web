<?php
include 'db.php';

session_start();

if (isset($_POST['submit'])) {
    $id = $_POST['id'];  // ID yang diinputkan oleh pengguna
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header('Location: dashboard.php');
    } else {
        echo "Invalid credentials!";
    }
}
?>

<form method="POST" action="">
    ID: <input type="text" name="id" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" name="submit" value="Login">
    <!-- Tombol Back -->
    <a href="index.html">
        <button type="button">Back to dashboard</button>
    </a>
</form>