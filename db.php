<?php
$host = 'localhost'; 
$dbname = 'qurbana_app'; 
$username = 'root'; 
$password = '';      
$koneksi = mysqli_connect($host, $username, $password, $dbname);
if (!$koneksi) {
    die("Connection failed: " . mysqli_connect_error());
}

// echo "Connected successfully";
?>
