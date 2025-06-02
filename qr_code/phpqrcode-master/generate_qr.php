<?php
// Masukkan path ke library PHP QR Code
include("phpqrcode/qrlib.php");

// Data yang ingin dimasukkan ke dalam QR Code
$data = "https://example.com/ambil-daging?kode=qurban123";

// Path untuk menyimpan gambar QR Code
$file = "qrcode.png";

// Generate QR Code dan simpan sebagai gambar
QRcode::png($data, $file);

echo "QR Code berhasil dibuat dan disimpan di: " . $file;
?>
