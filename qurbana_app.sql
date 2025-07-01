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
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

-- Dumping data for table qurbana_app.hewan_qurban: ~7 rows (approximately)
DELETE FROM `hewan_qurban`;
INSERT INTO `hewan_qurban` (`id`, `jenis`, `jumlah`, `harga_per_ekor`, `biaya_admin_per_ekor`, `sumber`, `created_at`, `keterangan`) VALUES
	(25, 'sapi', 1, 3000000, 100000, NULL, '2025-06-17 15:36:00', ''),
	(26, 'sapi', 1, 3000000, 100000, NULL, '2025-06-17 15:36:12', ''),
	(27, 'kambing', 1, 2700000, 50000, NULL, '2025-06-17 15:36:32', ''),
	(28, 'sapi', 1, 3000000, 100000, NULL, '2025-06-18 07:49:00', ''),
	(29, 'sapi', 1, 3000000, 100000, NULL, '2025-06-18 20:15:10', ''),
	(30, 'sapi', 1, 3000000, 100000, NULL, '2025-06-20 02:14:57', ''),
	(31, 'sapi', 1, 3000000, 100000, '10238943827965640617', '2025-06-20 04:02:46', '');

-- Dumping structure for table qurbana_app.keuangan_keluar
CREATE TABLE IF NOT EXISTS `keuangan_keluar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tanggal` timestamp NOT NULL,
  `keperluan` varchar(100) DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `harga` bigint NOT NULL,
  `keterangan` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

-- Dumping data for table qurbana_app.keuangan_keluar: ~5 rows (approximately)
DELETE FROM `keuangan_keluar`;
INSERT INTO `keuangan_keluar` (`id`, `tanggal`, `keperluan`, `jumlah`, `harga`, `keterangan`) VALUES
	(27, '2025-06-04 07:31:29', 'beli es', 4, 2000, 'haus'),
	(28, '2025-06-16 17:00:00', 'baso aci', 80, 90000, ''),
	(29, '2025-06-17 17:00:00', 'baso aci', 80, 90000, ''),
	(30, '2025-06-19 17:00:00', 'baso aci', 80, 90000, ''),
	(31, '2025-06-19 17:00:00', 'baso aci', 80, 90000, '');

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
) ENGINE=InnoDB AUTO_INCREMENT=237 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table qurbana_app.pembagian_daging: ~61 rows (approximately)
DELETE FROM `pembagian_daging`;
INSERT INTO `pembagian_daging` (`id`, `nik`, `role_penerima`, `jumlah_kg`, `qr_code`, `status_pengambilan`) VALUES
	(80, '1', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(176, '72017166983768889723', 'warga', 1.00, 'default_qr_code.png', 'sudah'),
	(177, '91841674451348367627', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(178, '24018149696295173180', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(179, '97855334681210427947', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(180, '20916014602981702467', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(181, '13251061612145506782', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(182, '57648407147221701618', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(183, '63181826837219907394', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(184, '60979439969687207506', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(185, '81690721851411527244', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(186, '95202214813275983585', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(187, '67174502842650634019', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(188, '67054761207221923456', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(189, '73849952872351429176', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(190, '39021361234852615894', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(191, '44420549687232346755', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(192, '90205215861725034961', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(193, '58061625861204647962', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(194, '72623808793284527199', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(195, '11314131202984433018', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(196, '34784748282622254319', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(197, '55792714686242410982', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(198, '66094114403642479849', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(199, '23539592565505049192', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(200, '90749343898230857429', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(201, '57418329158722765849', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(202, '48123014922761934112', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(203, '92082374651792051871', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(204, '14325019893764117890', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(205, '37678414237766458792', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(206, '50789569882063549218', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(207, '56946203131917974935', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(208, '24358634475260379481', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(209, '51027837025454164576', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(210, '15018764482917419312', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(211, '31450667028982374190', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(212, '65792437732589519520', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(213, '26916245582133274409', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(214, '11590246828383747281', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(215, '92403421021743293445', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(216, '98465189301655982728', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(217, '88971756059311746899', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(218, '47213223576455719172', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(219, '50123984276113275897', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(220, '72819364652975876310', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(221, '15322767753159384506', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(222, '28943079835020915637', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(223, '62808367084590830953', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(224, '46502834656131974648', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(225, '27415083969554129520', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(226, '94460159651576367928', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(227, '32517174610823859729', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(229, '32734603282179508321', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(230, '10238943827965640617', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(231, '65303984406185421034', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(232, '16255726139585318927', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(233, '57910271205827619843', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(234, '63184618927406733453', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(235, '44230215710866329539', 'warga', 1.00, 'default_qr_code.png', 'belum'),
	(236, '23001762623407409672', 'warga', 1.00, 'default_qr_code.png', 'belum');

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

-- Dumping data for table qurbana_app.users: ~61 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`nik`, `name`, `jenis_kelamin`, `alamat`, `password`, `role`, `created_at`) VALUES
	('1', '1', 'L', '1', '1', 'admin', '2025-06-18 19:45:29'),
	('10238943827965640617', 'Ahmad Faisal', 'L', 'Jl. Subur No. 23, Yogyakarta', '1', 'warga', '2025-06-19 20:50:00'),
	('11314131202984433018', 'Erik Sanjaya', 'L', 'Jl. Surya No. 5, Bandung', '11314131202984433018', 'warga', '2025-06-19 20:50:00'),
	('11590246828383747281', 'Novianto', 'L', 'Jl. Mawar No. 6, Makassar', '11590246828383747281', 'warga', '2025-06-19 20:50:00'),
	('13251061612145506782', 'Rina Suryani', 'P', 'Jl. Taman Sari No. 18, Makassar', '13251061612145506782', 'warga', '2025-06-19 20:50:00'),
	('14325019893764117890', 'Herman Supriyadi', 'L', 'Jl. Karang No. 12, Jakarta', '14325019893764117890', 'warga', '2025-06-19 20:50:00'),
	('15018764482917419312', 'Marlina Karima', 'P', 'Jl. Bukit No. 11, Jakarta', '15018764482917419312', 'warga', '2025-06-19 20:50:00'),
	('15322767753159384506', 'Rita Juwita', 'P', 'Jl. Bintang No. 11, Semarang', '15322767753159384506', 'warga', '2025-06-19 20:50:00'),
	('16255726139585318927', 'Dimas Nugroho', 'L', 'Jl. Sumber Alam No. 10, Malang', '1', 'warga', '2025-06-19 20:50:00'),
	('20916014602981702467', 'Joko Widodo', 'L', 'Jl. Pemuda No. 5, Medan', '20916014602981702467', 'warga', '2025-06-19 20:50:00'),
	('23001762623407409672', 'Faris Zulkifli', 'L', 'Jl. Merdeka No. 13, Bandung', '23001762623407409672', 'warga', '2025-06-19 20:50:00'),
	('23539592565505049192', 'Nuryati Kartika', 'P', 'Jl. Raya Bekasi No. 7, Bekasi', '23539592565505049192', 'warga', '2025-06-19 20:50:00'),
	('24018149696295173180', 'Andi Pratama', 'L', 'Jl. Raya Semanggi No. 24, Bandung', '1', 'warga', '2025-06-19 20:50:00'),
	('24358634475260379481', 'Rina Rahayu', 'P', 'Jl. Melati No. 4, Semarang', '24358634475260379481', 'warga', '2025-06-19 20:50:00'),
	('26916245582133274409', 'Yulia Safitri', 'P', 'Jl. Gading No. 7, Surabaya', '26916245582133274409', 'warga', '2025-06-19 20:50:00'),
	('27415083969554129520', 'Siti Marlina', 'P', 'Jl. Pahlawan No. 19, Yogyakarta', '27415083969554129520', 'warga', '2025-06-19 20:50:00'),
	('28943079835020915637', 'Taufik Hidayat', 'L', 'Jl. Pelita No. 4, Malang', '28943079835020915637', 'warga', '2025-06-19 20:50:00'),
	('31450667028982374190', 'Eka Lestari', 'P', 'Jl. Pasar No. 13, Malang', '1', 'warga', '2025-06-19 20:50:00'),
	('32517174610823859729', 'Wulan Permata', 'P', 'Jl. Surya No. 16, Malang', '32517174610823859729', 'warga', '2025-06-19 20:50:00'),
	('32734603282179508321', 'Maya Ananda', 'P', 'Jl. Kenanga No. 2, Jakarta', '32734603282179508321', 'warga', '2025-06-19 20:50:00'),
	('34784748282622254319', 'Ferry Sumarno', 'L', 'Jl. Mandala No. 3, Medan', '34784748282622254319', 'warga', '2025-06-19 20:50:00'),
	('37678414237766458792', 'Lina Hapsari', 'P', 'Jl. Puncak No. 9, Bandung', '37678414237766458792', 'warga', '2025-06-19 20:50:00'),
	('39021361234852615894', 'Ria Marlina', 'P', 'Jl. Pahlawan No. 19, Bandung', '39021361234852615894', 'warga', '2025-06-19 20:50:00'),
	('44230215710866329539', 'Indah Kartika', 'P', 'Jl. Nusantara No. 22, Surabaya', '44230215710866329539', 'warga', '2025-06-19 20:50:00'),
	('44420549687232346755', 'Hendra Gunawan', 'L', 'Jl. Anggrek No. 33, Bali', '44420549687232346755', 'warga', '2025-06-19 20:50:00'),
	('46502834656131974648', 'Hendra Wijaya', 'L', 'Jl. Cempaka No. 7, Medan', '46502834656131974648', 'warga', '2025-06-19 20:50:00'),
	('47213223576455719172', 'Agung Prasetya', 'L', 'Jl. Siti No. 3, Jakarta', '1', 'warga', '2025-06-19 20:50:00'),
	('48123014922761934112', 'Wawan Kurniawan', 'L', 'Jl. Fajar No. 21, Malang', '48123014922761934112', 'warga', '2025-06-19 20:50:00'),
	('50123984276113275897', 'Dewi Pramesti', 'P', 'Jl. Merpati No. 25, Surabaya', '1', 'warga', '2025-06-19 20:50:00'),
	('50789569882063549218', 'Agus Santoso', 'L', 'Jl. Raya Semangka No. 14, Yogyakarta', '1', 'warga', '2025-06-19 20:50:00'),
	('51027837025454164576', 'Budi Wijayanto', 'L', 'Jl. Kemuning No. 9, Medan', '1', 'warga', '2025-06-19 20:50:00'),
	('55792714686242410982', 'Nina Anggraeni', 'P', 'Jl. Merdeka Raya No. 9, Yogyakarta', '55792714686242410982', 'warga', '2025-06-19 20:50:00'),
	('56946203131917974935', 'Dedi Setiawan', 'L', 'Jl. Balai No. 22, Surabaya', '1', 'warga', '2025-06-19 20:50:00'),
	('57418329158722765849', 'Yuliana Putri', 'P', 'Jl. Setia No. 5, Palembang', '57418329158722765849', 'warga', '2025-06-19 20:50:00'),
	('57648407147221701618', 'Yanto Nugroho', 'L', 'Jl. Alun-Alun No. 7, Malang', '57648407147221701618', 'warga', '2025-06-19 20:50:00'),
	('57910271205827619843', 'Lina Arifin', 'P', 'Jl. Raya No. 14, Palembang', '57910271205827619843', 'warga', '2025-06-19 20:50:00'),
	('58061625861204647962', 'Benyamin Tanuwijaya', 'L', 'Jl. Kuta No. 17, Bali', '1', 'warga', '2025-06-19 20:50:00'),
	('60979439969687207506', 'Slamet Raharjo', 'L', 'Jl. Raya Bogor No. 9, Depok', '60979439969687207506', 'warga', '2025-06-19 20:50:00'),
	('62808367084590830953', 'Yuliana Lestari', 'P', 'Jl. Pantai No. 8, Bali', '62808367084590830953', 'warga', '2025-06-19 20:50:00'),
	('63181826837219907394', 'Maya Pratiwi', 'P', 'Jl. Kebon Jeruk No. 4, Medan', '63181826837219907394', 'warga', '2025-06-19 20:50:00'),
	('63184618927406733453', 'Tono Supriyanto', 'L', 'Jl. Satria No. 18, Medan', '63184618927406733453', 'warga', '2025-06-19 20:50:00'),
	('65303984406185421034', 'Nina Hidayati', 'P', 'Jl. Kuningan No. 9, Jakarta', '65303984406185421034', 'warga', '2025-06-19 20:50:00'),
	('65792437732589519520', 'Iwan Darmawan', 'L', 'Jl. Raya Cengkareng No. 5, Jakarta', '65792437732589519520', 'warga', '2025-06-19 20:50:00'),
	('66094114403642479849', 'Purnomo Sutrisno', 'L', 'Jl. Cendrawasih No. 6, Makassar', '66094114403642479849', 'warga', '2025-06-19 20:50:00'),
	('67054761207221923456', 'Susi Susanto', 'P', 'Jl. Tanah Abang No. 6, Jakarta', '67054761207221923456', 'warga', '2025-06-19 20:50:00'),
	('67174502842650634019', 'Yuni Astuti', 'P', 'Jl. Raya Jatinegara No. 13, Jakarta', '67174502842650634019', 'warga', '2025-06-19 20:50:00'),
	('72017166983768889723', 'Budi Santoso', 'P', 'Jl. Merdeka No. 12, Jakarta', '1', 'warga', '2025-06-19 20:50:00'),
	('72623808793284527199', 'Diana Wijayanti', 'P', 'Jl. Gajah Mada No. 10, Jakarta', '1', 'warga', '2025-06-19 20:50:00'),
	('72819364652975876310', 'Ferry Adrianto', 'L', 'Jl. Suka No. 14, Bandung', '72819364652975876310', 'warga', '2025-06-19 20:50:00'),
	('73849952872351429176', 'Adi Saputra', 'L', 'Jl. Maju No. 8, Surabaya', '1', 'warga', '2025-06-19 20:50:00'),
	('81690721851411527244', 'Tika Ramadhani', 'P', 'Jl. Palem No. 22, Palembang', '81690721851411527244', 'warga', '2025-06-19 20:50:00'),
	('88971756059311746899', 'Tina Sari', 'P', 'Jl. Raya Kuta No. 10, Bali', '88971756059311746899', 'warga', '2025-06-19 20:50:00'),
	('90205215861725034961', 'Wati Kusuma', 'P', 'Jl. Pantai No. 8, Surabaya', '90205215861725034961', 'warga', '2025-06-19 20:50:00'),
	('90749343898230857429', 'Imam Setiawan', 'L', 'Jl. Puspa No. 16, Medan', '90749343898230857429', 'warga', '2025-06-19 20:50:00'),
	('91841674451348367627', 'Siti Aisyah', 'P', 'Jl. Pahlawan No. 15, Surabaya', '91841674451348367627', 'warga', '2025-06-19 20:50:00'),
	('92082374651792051871', 'Siti Nurhayati', 'P', 'Jl. Pahlawan No. 18, Surabaya', '92082374651792051871', 'warga', '2025-06-19 20:50:00'),
	('92403421021743293445', 'Irma Maulani', 'P', 'Jl. Karya No. 15, Yogyakarta', '92403421021743293445', 'warga', '2025-06-19 20:50:00'),
	('94460159651576367928', 'Eko Kurniawan', 'L', 'Jl. Purnama No. 12, Bandung', '1', 'warga', '2025-06-19 20:50:00'),
	('95202214813275983585', 'Rudi Satria', 'L', 'Jl. Kemuning No. 10, Semarang', '95202214813275983585', 'warga', '2025-06-19 20:50:00'),
	('97855334681210427947', 'Dewi Lestari', 'P', 'Jl. Sudirman No. 30, Yogyakarta', '1', 'warga', '2025-06-19 20:50:00'),
	('98465189301655982728', 'Toni Prasetyo', 'L', 'Jl. Damar No. 22, Medan', '98465189301655982728', 'warga', '2025-06-19 20:50:00');

-- Dumping structure for table qurbana_app.user_roles
CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nik` varchar(20) NOT NULL,
  `role` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_roles_ibfk_1` (`nik`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`nik`) REFERENCES `users` (`nik`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=380 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table qurbana_app.user_roles: ~86 rows (approximately)
DELETE FROM `user_roles`;
INSERT INTO `user_roles` (`id`, `nik`, `role`) VALUES
	(151, '1', 'admin'),
	(152, '1', 'admin'),
	(261, '91841674451348367627', 'warga'),
	(264, '20916014602981702467', 'warga'),
	(265, '13251061612145506782', 'warga'),
	(266, '57648407147221701618', 'warga'),
	(267, '63181826837219907394', 'warga'),
	(268, '60979439969687207506', 'warga'),
	(269, '81690721851411527244', 'warga'),
	(270, '95202214813275983585', 'warga'),
	(271, '67174502842650634019', 'warga'),
	(272, '67054761207221923456', 'warga'),
	(274, '39021361234852615894', 'warga'),
	(275, '44420549687232346755', 'warga'),
	(276, '90205215861725034961', 'warga'),
	(279, '11314131202984433018', 'warga'),
	(280, '34784748282622254319', 'warga'),
	(281, '55792714686242410982', 'warga'),
	(282, '66094114403642479849', 'warga'),
	(283, '23539592565505049192', 'warga'),
	(284, '90749343898230857429', 'warga'),
	(285, '57418329158722765849', 'warga'),
	(286, '48123014922761934112', 'warga'),
	(287, '92082374651792051871', 'warga'),
	(288, '14325019893764117890', 'warga'),
	(289, '37678414237766458792', 'warga'),
	(292, '24358634475260379481', 'warga'),
	(294, '15018764482917419312', 'warga'),
	(296, '65792437732589519520', 'warga'),
	(297, '26916245582133274409', 'warga'),
	(298, '11590246828383747281', 'warga'),
	(299, '92403421021743293445', 'warga'),
	(300, '98465189301655982728', 'warga'),
	(301, '88971756059311746899', 'warga'),
	(304, '72819364652975876310', 'warga'),
	(305, '15322767753159384506', 'warga'),
	(306, '28943079835020915637', 'warga'),
	(307, '62808367084590830953', 'warga'),
	(308, '46502834656131974648', 'warga'),
	(309, '27415083969554129520', 'warga'),
	(311, '32517174610823859729', 'warga'),
	(313, '32734603282179508321', 'warga'),
	(315, '65303984406185421034', 'warga'),
	(317, '57910271205827619843', 'warga'),
	(318, '63184618927406733453', 'warga'),
	(319, '44230215710866329539', 'warga'),
	(320, '23001762623407409672', 'warga'),
	(341, '97855334681210427947', 'warga'),
	(342, '97855334681210427947', 'panitia'),
	(343, '50123984276113275897', 'warga'),
	(344, '50123984276113275897', 'panitia'),
	(345, '72623808793284527199', 'warga'),
	(346, '72623808793284527199', 'panitia'),
	(347, '16255726139585318927', 'warga'),
	(348, '16255726139585318927', 'panitia'),
	(349, '31450667028982374190', 'warga'),
	(350, '31450667028982374190', 'panitia'),
	(351, '94460159651576367928', 'warga'),
	(352, '94460159651576367928', 'panitia'),
	(353, '73849952872351429176', 'warga'),
	(354, '73849952872351429176', 'panitia'),
	(355, '73849952872351429176', 'berqurban'),
	(356, '47213223576455719172', 'warga'),
	(357, '47213223576455719172', 'panitia'),
	(358, '47213223576455719172', 'berqurban'),
	(359, '50789569882063549218', 'warga'),
	(360, '50789569882063549218', 'panitia'),
	(361, '50789569882063549218', 'berqurban'),
	(362, '10238943827965640617', 'warga'),
	(363, '10238943827965640617', 'panitia'),
	(364, '10238943827965640617', 'berqurban'),
	(365, '24018149696295173180', 'warga'),
	(366, '24018149696295173180', 'panitia'),
	(367, '24018149696295173180', 'berqurban'),
	(368, '58061625861204647962', 'warga'),
	(369, '58061625861204647962', 'panitia'),
	(370, '58061625861204647962', 'berqurban'),
	(371, '72017166983768889723', 'warga'),
	(372, '72017166983768889723', 'panitia'),
	(373, '72017166983768889723', 'berqurban'),
	(374, '51027837025454164576', 'warga'),
	(375, '51027837025454164576', 'panitia'),
	(376, '51027837025454164576', 'berqurban'),
	(377, '56946203131917974935', 'warga'),
	(378, '56946203131917974935', 'panitia'),
	(379, '56946203131917974935', 'berqurban');

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
