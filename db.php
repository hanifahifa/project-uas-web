<?php
$host = 'localhost'; // alamat server
$dbname = 'qurban';  // nama database
$username = 'root';  // username mysql
$password = '';      // password mysql

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
