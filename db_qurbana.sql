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
CREATE DATABASE IF NOT EXISTS `qurbana_app` /*!40100 DEFAULT CHARACTER SET big5 COLLATE big5_bin */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `qurbana_app`;

-- Dumping structure for table qurbana_app.hewan_qurban
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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

-- Dumping data for table qurbana_app.hewan_qurban: ~3 rows (approximately)
DELETE FROM `hewan_qurban`;
INSERT INTO `hewan_qurban` (`id`, `jenis`, `jumlah`, `harga_per_ekor`, `biaya_admin_per_ekor`, `sumber`, `created_at`, `keterangan`) VALUES
	(25, 'sapi', 1, 3000000, 100000, NULL, '2025-06-17 15:36:00', ''),
	(26, 'sapi', 1, 3000000, 100000, NULL, '2025-06-17 15:36:12', ''),
	(27, 'kambing', 1, 2700000, 50000, NULL, '2025-06-17 15:36:32', ''),
	(28, 'sapi', 1, 3000000, 100000, NULL, '2025-06-18 07:49:00', ''),
	(29, 'sapi', 1, 3000000, 100000, '0000000016', '2025-06-18 20:15:10', '');

-- Dumping structure for table qurbana_app.keuangan_keluar
CREATE TABLE IF NOT EXISTS `keuangan_keluar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tanggal` timestamp NOT NULL,
  `keperluan` varchar(100) DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `harga` bigint NOT NULL,
  `keterangan` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

-- Dumping data for table qurbana_app.keuangan_keluar: ~0 rows (approximately)
DELETE FROM `keuangan_keluar`;
INSERT INTO `keuangan_keluar` (`id`, `tanggal`, `keperluan`, `jumlah`, `harga`, `keterangan`) VALUES
	(27, '2025-06-04 07:31:29', 'beli es', 4, 2000, 'haus'),
	(28, '2025-06-16 17:00:00', 'baso aci', 80, 90000, ''),
	(29, '2025-06-17 17:00:00', 'baso aci', 80, 90000, '');

-- Dumping structure for table qurbana_app.pembagian_daging
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
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table qurbana_app.pembagian_daging: ~4 rows (approximately)
DELETE FROM `pembagian_daging`;
INSERT INTO `pembagian_daging` (`id`, `nik`, `role_penerima`, `jumlah_kg`, `qr_code`, `status_pengambilan`) VALUES
	(70, '0000000014', 'warga', 1.00, 'default_qr_code.png', 'sudah'),
	(72, '0000000016', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(74, '0000000018', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(78, '111', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(80, '1', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(81, '1111', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(82, '2', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(83, '3', 'warga', 1.00, 'default_qr_code.png', 'belum');

-- Dumping structure for table qurbana_app.users
CREATE TABLE IF NOT EXISTS `users` (
  `nik` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `jenis_kelamin` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','warga') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'warga',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`nik`),
  UNIQUE KEY `nik` (`nik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table qurbana_app.users: ~8 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`nik`, `name`, `jenis_kelamin`, `alamat`, `password`, `role`, `created_at`) VALUES
	('0000000014', 'Diana Martin', 'P', 'Jl. Gatot Subroto No. 14', '1', 'warga', '2025-06-18 09:29:33'),
	('0000000016', 'Fiona Clark', 'P', 'Jl. Pahlawan No. 16', '1', 'warga', '2025-06-18 09:29:33'),
	('0000000018', 'Hannah Scott', 'P', 'Jl. Cendana No. 18', '0000000018', 'warga', '2025-06-18 09:29:33'),
	('1', '1', 'L', '1', '1', 'admin', '2025-06-18 19:45:29'),
	('111', '1', 'P', '1', '111', 'warga', '2025-06-18 19:38:40'),
	('1111', '1', 'L', '1', '1111', 'admin', '2025-06-18 20:14:43'),
	('2', '2', '2', '2', '2', 'warga', '2025-06-19 01:57:32'),
	('3', '3', '3', '3', '3', 'admin', '2025-06-19 01:58:53');

-- Dumping structure for table qurbana_app.user_roles
CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nik` varchar(20) NOT NULL,
  `role` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_roles_ibfk_1` (`nik`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`nik`) REFERENCES `users` (`nik`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=160 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table qurbana_app.user_roles: ~14 rows (approximately)
DELETE FROM `user_roles`;
INSERT INTO `user_roles` (`id`, `nik`, `role`) VALUES
	(127, '0000000018', 'warga'),
	(131, '0000000014', 'warga'),
	(132, '0000000014', 'panitia'),
	(144, '111', 'warga'),
	(145, '111', 'warga'),
	(151, '1', 'admin'),
	(152, '1', 'admin'),
	(153, '0000000016', 'warga'),
	(154, '0000000016', 'panitia'),
	(155, '0000000016', 'berqurban'),
	(156, '1111', 'admin'),
	(157, '1111', 'admin'),
	(158, '2', 'warga'),
	(159, '3', 'admin');

-- Dumping structure for trigger qurbana_app.after_user_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `after_user_insert` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    -- Masukkan data pembagian daging setelah pengguna baru ditambahkan
    INSERT INTO pembagian_daging (nik, role_penerima, jumlah_kg, status_pengambilan, qr_code)
    VALUES (NEW.nik, 'warga', 1.00, 'belum', 'default_qr_code.png');
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger qurbana_app.after_user_insert_v2
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `after_user_insert_v2` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    INSERT INTO user_roles (nik, role)
    VALUES (NEW.nik, NEW.role);
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
