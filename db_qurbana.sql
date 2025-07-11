-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for qurbana_app
DROP DATABASE IF EXISTS `qurbana_app`;
CREATE DATABASE IF NOT EXISTS `qurbana_app` /*!40100 DEFAULT CHARACTER SET big5 COLLATE big5_bin */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `qurbana_app`;

-- Dumping structure for table qurbana_app.hewan_qurban
DROP TABLE IF EXISTS `hewan_qurban`;
CREATE TABLE IF NOT EXISTS `hewan_qurban` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jenis` enum('kambing','sapi') NOT NULL,
  `jumlah` int NOT NULL,
  `harga_per_ekor` bigint NOT NULL,
  `biaya_admin_per_ekor` bigint NOT NULL,
  `sumber` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `keterangan` text,
  PRIMARY KEY (`id`),
  KEY `fk_hewan_qurban_sumber` (`sumber`),
  CONSTRAINT `fk_hewan_qurban_sumber` FOREIGN KEY (`sumber`) REFERENCES `users` (`nik`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

-- Dumping data for table qurbana_app.hewan_qurban: ~4 rows (approximately)
DELETE FROM `hewan_qurban`;
INSERT INTO `hewan_qurban` (`id`, `jenis`, `jumlah`, `harga_per_ekor`, `biaya_admin_per_ekor`, `sumber`, `created_at`, `keterangan`) VALUES
	(21, 'sapi', 1, 3000000, 100000, '6', '2025-06-04 14:29:05', 'patungan'),
	(22, 'sapi', 1, 3000000, 100000, '4', '2025-06-04 14:31:12', 'patungan'),
	(23, 'sapi', 1, 3000000, 100000, '4', '2025-06-08 08:53:45', ''),
	(24, 'sapi', 1, 3000000, 100000, '7777', '2025-06-13 04:34:52', 'ifa');

-- Dumping structure for table qurbana_app.keuangan_keluar
DROP TABLE IF EXISTS `keuangan_keluar`;
CREATE TABLE IF NOT EXISTS `keuangan_keluar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tanggal` timestamp NOT NULL,
  `keperluan` varchar(100) DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `harga` bigint NOT NULL,
  `keterangan` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

-- Dumping data for table qurbana_app.keuangan_keluar: ~1 rows (approximately)
DELETE FROM `keuangan_keluar`;
INSERT INTO `keuangan_keluar` (`id`, `tanggal`, `keperluan`, `jumlah`, `harga`, `keterangan`) VALUES
	(27, '2025-06-04 07:31:29', 'beli es', 4, 2000, 'haus');

-- Dumping structure for table qurbana_app.pembagian_daging
DROP TABLE IF EXISTS `pembagian_daging`;
CREATE TABLE IF NOT EXISTS `pembagian_daging` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nik` varchar(20) NOT NULL,
  `role_penerima` enum('warga','panitia','berqurban') NOT NULL,
  `jumlah_kg` decimal(5,2) NOT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `status_pengambilan` enum('belum','sudah') DEFAULT 'belum',
  PRIMARY KEY (`id`),
  KEY `nik` (`nik`),
  CONSTRAINT `pembagian_daging_ibfk_1` FOREIGN KEY (`nik`) REFERENCES `users` (`nik`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table qurbana_app.pembagian_daging: ~3 rows (approximately)
DELETE FROM `pembagian_daging`;
INSERT INTO `pembagian_daging` (`id`, `nik`, `role_penerima`, `jumlah_kg`, `qr_code`, `status_pengambilan`) VALUES
	(17, '6', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(18, '99', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(19, '7777', 'warga', 1.00, 'default_qr_code.png', 'belum');

-- Dumping structure for table qurbana_app.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `nik` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `jenis_kelamin` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','warga','panitia','berqurban') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'warga',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`nik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table qurbana_app.users: ~7 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`nik`, `name`, `jenis_kelamin`, `alamat`, `password`, `role`, `created_at`) VALUES
	('1', 'admin', 'p', 'a', '123', 'admin', '2025-06-01 08:04:40'),
	('2', 'panitia', 'p', 'a', '123', 'panitia', '2025-06-01 08:05:19'),
	('3', 'warga', 'p', 'a', '123', 'warga', '2025-06-01 08:05:44'),
	('4', 'berqurban', 'p', 'a', '123', 'berqurban', '2025-06-01 08:06:09'),
	('6', 'ifa', 'L', 's', '123', 'warga', '2025-06-03 21:06:51'),
	('7777', 'ifa', 'P', '1', '123', 'berqurban', '2025-06-13 04:34:31'),
	('99', 'p', 'P', 'p', '122', 'warga', '2025-06-08 09:59:54');

-- Dumping structure for trigger qurbana_app.after_user_insert
DROP TRIGGER IF EXISTS `after_user_insert`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `after_user_insert` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    -- Masukkan data pembagian daging setelah pengguna baru ditambahkan
    INSERT INTO pembagian_daging (nik, role_penerima, jumlah_kg, status_pengambilan, qr_code)
    VALUES (NEW.nik, 'warga', 1.00, 'belum', 'default_qr_code.png');
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
