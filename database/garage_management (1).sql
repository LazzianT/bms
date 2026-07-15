-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jul 13, 2026 at 04:04 AM
-- Server version: 8.0.44
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `garage_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `client_id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tanggal_daftar` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`client_id`, `nama`, `alamat`, `telepon`, `email`, `tanggal_daftar`) VALUES
(1, 'Budi Santoso', 'Jl. Merdeka No 1, Jakarta', '081234567890', 'budi@email.com', '2026-05-22'),
(2, 'Siti Aminah', 'Jl. Sudirman No 2, Bandung', '081298765432', 'siti@email.com', '2026-05-22'),
(3, 'Agus Setiawan', 'Jl. Thamrin No 3, Surabaya', '081311223344', 'agus@email.com', '2026-05-22'),
(4, 'Dewi Lestari', 'Jl. Gatot Subroto No 4, Medan', '081344556677', 'dewi@email.com', '2026-05-22'),
(5, 'Reza Rahadian', 'Jl. Asia Afrika No 5, Makassar', '081455667788', 'reza@email.com', '2026-05-22'),
(6, 'Ayu Tingting', 'Jl. Pahlawan No 6, Semarang', '081566778899', 'ayu@email.com', '2026-05-22'),
(7, 'Raffi Ahmad', 'Jl. Diponegoro No 7, Yogyakarta', '081677889900', 'raffi@email.com', '2026-05-22'),
(8, 'Luna Maya', 'Jl. Hasanuddin No 8, Bali', '081788990011', 'luna@email.com', '2026-05-22'),
(9, 'Vino Bastian', 'Jl. Imam Bonjol No 9, Padang', '081899001122', 'vino@email.com', '2026-05-22'),
(10, 'Dian Sastro', 'Jl. Teuku Umar No 10, Palembang', '081297457759', 'dian@email.com', '2026-05-23'),
(11, 'Andi Pratama', 'Jl. Kebon Sirih No 11, Jakarta Pusat', '081201112234', 'andi.pratama@email.com', '2026-05-23'),
(12, 'Rina Wulandari', 'Jl. Cikini Raya No 15, Jakarta', '081302223344', 'rina.w@email.com', '2026-05-02'),
(13, 'Hendra Gunawan', 'Jl. Pemuda No 22, Surabaya', '081403334455', 'hendra.g@email.com', '2026-04-22'),
(14, 'Maya Sari', 'Jl. Veteran No 8, Malang', '081504445566', 'maya.sari@email.com', '2026-04-12'),
(15, 'Fajar Nugroho', 'Jl. Jend. Sudirman No 45, Semarang', '081605556677', 'fajar.n@email.com', '2026-04-02'),
(16, 'Putri Handayani', 'Jl. Ahmad Yani No 33, Bandung', '081706667788', 'putri.h@email.com', '2026-05-17'),
(17, 'Yoga Firmansyah', 'Jl. Gajah Mada No 19, Tangerang', '081807778899', 'yoga.f@email.com', '2026-05-07'),
(18, 'Sari Dewi Utami', 'Jl. Mangga Besar No 77, Jakarta', '081908889900', 'sari.du@email.com', '2026-04-27'),
(19, 'Rizky Ramadhan', 'Jl. Pasar Baru No 5, Bogor', '082009990011', 'rizky.r@email.com', '2026-04-17'),
(20, 'Nur Aini', 'Jl. Raya Serpong No 101, Tangerang Selatan', '082100112233', 'nur.aini@email.com', '2026-04-07'),
(21, 'Bayu Anggara', 'Jl. RE Martadinata No 55, Bandung', '082211223344', 'bayu.a@email.com', '2026-05-15'),
(22, 'Indah Permata', 'Jl. Siliwangi No 12, Cirebon', '082322334455', 'indah.p@email.com', '2026-05-08'),
(23, 'Doni Kusuma', 'Jl. Braga No 28, Bandung', '082433445566', 'doni.k@email.com', '2026-05-01'),
(24, 'Ratna Sari', 'Jl. Pasir Kaliki No 66, Bandung', '082544556677', 'ratna.s@email.com', '2026-04-24'),
(25, 'Ahmad Fauzi', 'Jl. Cipaganti No 88, Bandung', '082655667788', 'ahmad.f@email.com', '2026-05-19');

-- --------------------------------------------------------

--
-- Table structure for table `mekanik`
--

CREATE TABLE `mekanik` (
  `mekanik_id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `spesialis` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `mekanik`
--

INSERT INTO `mekanik` (`mekanik_id`, `nama`, `telepon`, `spesialis`) VALUES
(1, 'Joko', '082111223344', 'Mesin Motor Matic'),
(2, 'Andi', '082155667788', 'Kelistrikan'),
(3, 'Tono', '082233445566', 'Mesin Motor Sport'),
(4, 'Bambang', '082344556677', 'Kaki-kaki dan Rem'),
(5, 'Rudi', '082455667788', 'Injeksi dan ECU'),
(6, 'Eko', '082566778899', 'Cat dan Modifikasi'),
(7, 'Hadi', '082677889900', 'Mesin Motor Bebek'),
(8, 'Iwan', '082788990011', 'Overhaul Mesin'),
(9, 'Dani', '082899001122', 'Kelistrikan Lanjut'),
(10, 'Feri', '082900112233', 'Servis Ringan / Fast Track');

-- --------------------------------------------------------

--
-- Table structure for table `sparepart`
--

CREATE TABLE `sparepart` (
  `sparepart_id` int NOT NULL,
  `kode_sparepart` varchar(50) NOT NULL,
  `nama_sparepart` varchar(100) NOT NULL,
  `satuan` varchar(20) DEFAULT NULL,
  `stok` int DEFAULT '0',
  `harga_beli` double DEFAULT '0',
  `harga_jual` double DEFAULT '0',
  `supplier_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sparepart`
--

INSERT INTO `sparepart` (`sparepart_id`, `kode_sparepart`, `nama_sparepart`, `satuan`, `stok`, `harga_beli`, `harga_jual`, `supplier_id`) VALUES
(1, 'OLI-001', 'Oli Mesin Yamalube 0.8L', 'Botol', 50, 45000, 52000, 1),
(2, 'OLI-002', 'Oli Gardan Yamalube 100ml', 'Botol', 59, 15000, 18000, 1),
(3, 'BUSI-001', 'Busi NGK CPR9EA-9', 'Pcs', 3, 15000, 25000, 8),
(4, 'KAMPAS-001', 'Kampas Rem Depan Honda Vario', 'Set', 3, 40000, 55000, 2),
(5, 'KAMPAS-002', 'Kampas Rem Belakang NMAX', 'Set', -1, 55000, 75000, 2),
(6, 'BAN-001', 'Ban IRC 90/90-14 Tubeless', 'Pcs', 20, 180000, 210000, 6),
(7, 'BAN-002', 'Ban IRC 100/90-14 Tubeless', 'Pcs', 7, 230000, 265000, 6),
(8, 'AKI-001', 'Aki Yuasa YTZ5S', 'Pcs', 9, 180000, 220000, 4),
(9, 'FILT-001', 'Filter Udara Honda Beat FI', 'Pcs', 40, 45000, 60000, 1),
(10, 'VBLT-001', 'V-Belt Honda Vario 150', 'Pcs', 15, 120000, 150000, 1),
(11, 'OLI-003', 'Oli Mesin Castrol Power1 1L', 'Botol', 34, 68000, 85000, 1),
(12, 'OLI-004', 'Oli Mesin Shell Advance AX7 0.8L', 'Botol', 22, 55000, 70000, 1),
(13, 'BUSI-002', 'Busi Iridium NGK CR8EIX', 'Pcs', 12, 65000, 85000, 8),
(14, 'KAMPAS-003', 'Kampas Rem Depan Honda Beat', 'Set', 18, 35000, 50000, 2),
(15, 'KAMPAS-004', 'Kampas Kopling Honda Vario 125', 'Set', 5, 80000, 110000, 2),
(16, 'BAN-003', 'Ban Michelin Pilot Street 80/90-14', 'Pcs', 6, 250000, 310000, 6),
(17, 'BAN-004', 'Ban Dunlop D115 70/90-17', 'Pcs', 20, 195000, 240000, 6),
(18, 'AKI-002', 'Aki GS GTZ5S MF', 'Pcs', 7, 160000, 200000, 4),
(19, 'FILT-002', 'Filter Udara NMAX Original', 'Pcs', 15, 55000, 75000, 1),
(20, 'RANTAI-001', 'Rantai SSS 428 HSB 130L', 'Set', 5, 145000, 185000, 9),
(21, 'GEAR-001', 'Gear Set SSS Honda Supra X 125', 'Set', 4, 195000, 250000, 9),
(22, 'ROLLER-001', 'Roller Dr. Pulley Honda Vario 10g', 'Set', 20, 85000, 120000, 3),
(23, 'CDI-001', 'CDI Racing BRT Honda Beat FI', 'Pcs', 0, 350000, 450000, 9),
(24, 'LAMPU-001', 'Bohlam LED H6 Motor AC/DC 35W', 'Pcs', 24, 45000, 65000, 7),
(25, 'GASKET-001', 'Gasket Full Set Honda Beat', 'Set', 10, 75000, 100000, 3);

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `supplier_id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`supplier_id`, `nama`, `alamat`, `telepon`, `email`) VALUES
(1, 'PT Astra Otoparts', 'Gedung Astra, Sunter', '0215556661', 'sales@astra.co.id'),
(2, 'CV Maju Mundur', 'Ruko Blok M, Kebayoran', '0217778882', 'info@majumundur.com'),
(3, 'Toko Abadi Motor', 'Jl. Kebon Jeruk No. 5', '0218889993', 'cs@abadimotor.com'),
(4, 'PT Yuasa Battery', 'Kawasan Industri Cikarang', '0219990004', 'order@yuasa.co.id'),
(5, 'CV Gajah Mada', 'Jl. Gajah Mada No 12', '0212223335', 'gajahmada@part.com'),
(6, 'PT IRC Tire', 'Tangerang', '0213334446', 'sales@irc.co.id'),
(7, 'Toko Makmur Jaya', 'Pasar Senen Blok B', '0214445557', 'makmurjaya@gmail.com'),
(8, 'PT NGK Busi', 'Kawasan Industri Pulo Gadung', '0215551118', 'sales@ngk.co.id'),
(9, 'Bintang Racing Team (BRT)', 'Cibinong, Bogor', '0216662229', 'sales@brt.co.id'),
(10, 'Aneka Baut & Mur', 'Glodok Makmur', '0217773330', 'anekabaut@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_pembelian`
--

CREATE TABLE `transaksi_pembelian` (
  `purchase_id` int NOT NULL,
  `tanggal` datetime DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `sparepart_id` int DEFAULT NULL,
  `qty` int DEFAULT '1',
  `harga_beli` double DEFAULT '0',
  `total_harga` double DEFAULT '0',
  `keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi_pembelian`
--

INSERT INTO `transaksi_pembelian` (`purchase_id`, `tanggal`, `supplier_id`, `sparepart_id`, `qty`, `harga_beli`, `total_harga`, `keterangan`) VALUES
(1, '2026-04-22 15:21:02', 1, 1, 20, 45000, 900000, 'Restock oli mesin'),
(2, '2026-04-27 15:21:02', 8, 3, 10, 15000, 150000, 'Restock busi NGK'),
(3, '2026-05-02 15:21:02', 6, 6, 5, 180000, 900000, 'Restock ban IRC 90/90'),
(4, '2026-05-07 15:21:02', 4, 8, 3, 180000, 540000, 'Restock aki Yuasa'),
(5, '2026-05-12 15:21:02', 2, 4, 8, 40000, 320000, 'Restock kampas rem depan'),
(6, '2026-05-14 15:21:02', 1, 11, 10, 68000, 680000, 'Restock oli Castrol Power1'),
(7, '2026-05-15 15:21:02', 1, 12, 8, 55000, 440000, 'Restock oli Shell Advance'),
(8, '2026-05-16 15:21:02', 8, 13, 15, 65000, 975000, 'Restock busi Iridium NGK'),
(9, '2026-05-17 15:21:02', 2, 14, 20, 35000, 700000, 'Restock kampas rem Beat'),
(10, '2026-05-18 15:21:02', 6, 16, 4, 250000, 1000000, 'Restock ban Michelin'),
(11, '2026-05-19 15:21:02', 9, 20, 6, 145000, 870000, 'Restock rantai SSS'),
(12, '2026-05-20 15:21:02', 9, 21, 5, 195000, 975000, 'Restock gear set SSS'),
(13, '2026-05-21 15:21:02', 3, 22, 10, 85000, 850000, 'Restock roller Dr. Pulley'),
(14, '2026-05-22 15:21:02', 7, 24, 30, 45000, 1350000, 'Restock bohlam LED motor'),
(15, '2026-05-10 15:21:02', 3, 25, 8, 75000, 600000, 'Restock gasket set Beat'),
(16, '2026-05-22 15:24:01', 6, 7, 2, 230000, 460000, ''),
(17, '2026-05-23 10:10:35', 6, 17, 10, 200000, 2000000, 'ada kenaikan harga'),
(18, '2026-05-30 10:03:37', 6, 7, 10, 250000, 2500000, '');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_pendaftaran`
--

CREATE TABLE `transaksi_pendaftaran` (
  `registration_id` int NOT NULL,
  `vehicle_id` int NOT NULL,
  `client_id` int NOT NULL,
  `keluhan` text,
  `mekanik_id` int DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Registered',
  `tanggal_daftar` datetime NOT NULL,
  `tanggal_mulai` datetime DEFAULT NULL,
  `catatan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi_pendaftaran`
--

INSERT INTO `transaksi_pendaftaran` (`registration_id`, `vehicle_id`, `client_id`, `keluhan`, `mekanik_id`, `status`, `tanggal_daftar`, `tanggal_mulai`, `catatan`) VALUES
(1, 1, 1, 'Servis berkala 2.000 km dan cek rem', 1, 'Registered', '2026-05-22 15:21:02', NULL, 'Customer minta selesai hari ini'),
(2, 2, 2, 'Ganti oli, cek CVT, suara kasar saat akselerasi', 3, 'Registered', '2026-05-22 14:51:02', NULL, 'Tunggu approval sparepart'),
(3, 3, 3, 'Tarikan berat, minta cek roller dan v-belt', 5, 'InProgress', '2026-05-21 15:21:02', '2026-05-22 15:21:02', 'Estimasi selesai sore'),
(4, 4, 4, 'Overheat ringan dan flushing coolant', 2, 'Completed', '2026-05-19 15:21:02', '2026-05-20 15:21:02', 'Sudah diambil customer'),
(5, 11, 11, 'Servis berkala pertama 1000 km', 1, 'Registered', '2026-05-22 14:21:02', NULL, 'Motor baru'),
(6, 12, 12, 'Bunyi kasar di CVT saat jalan pelan', 3, 'InProgress', '2026-05-22 11:21:02', '2026-05-22 12:21:02', 'Perlu ganti roller'),
(7, 13, 13, 'Rantai kendor dan bunyi berisik', 4, 'InProgress', '2026-05-22 13:21:02', '2026-05-22 14:21:02', 'Ganti rantai set'),
(8, 14, 14, 'Servis tune-up mesin sport', 3, 'Registered', '2026-05-22 15:06:02', NULL, 'Customer tunggu'),
(9, 15, 15, 'Kopling berat dan selip', 5, 'Completed', '2026-05-20 15:21:02', '2026-05-21 15:21:02', 'Selesai ganti kampas kopling'),
(10, 17, 17, 'Mesin brebet saat idle', 7, 'Registered', '2026-05-22 14:36:02', NULL, 'Cek injektor'),
(11, 18, 18, 'Ganti oli dan tune up ringan', 10, 'Completed', '2026-05-17 15:21:02', '2026-05-18 15:21:02', 'Fast track'),
(12, 21, 21, 'Ban depan bocor halus, minta ganti baru', 4, 'InProgress', '2026-05-22 12:21:02', '2026-05-22 13:21:02', 'Stok ban tersedia'),
(13, 11, 11, 'Pembelian Sparepart', NULL, 'Completed', '2026-05-22 15:21:09', '2026-05-22 15:21:09', ''),
(14, 6, 6, 'Pembelian Sparepart', NULL, 'Completed', '2026-05-22 19:01:08', '2026-05-22 19:01:08', ''),
(15, 6, 6, 'gabisa jalan', 4, 'Registered', '2026-05-22 19:21:05', NULL, '-'),
(16, 22, 22, '-', 2, 'Completed', '2026-05-22 19:33:54', '2026-05-22 19:34:32', '-'),
(17, 25, 25, '-', 9, 'Completed', '2026-05-23 07:29:07', '2026-05-23 07:30:16', '-'),
(18, 27, 25, 'GANTI OLI', 6, 'Completed', '2026-05-23 07:57:56', '2026-05-23 08:01:12', ''),
(19, 27, 25, 'Pembelian Sparepart', NULL, 'Completed', '2026-05-23 07:59:06', '2026-05-23 07:59:06', ''),
(20, 3, 3, 'Sering mogok', 7, 'Completed', '2026-05-23 10:06:21', '2026-05-23 10:06:59', '-'),
(21, 27, 25, 'Pembelian Sparepart', NULL, 'Completed', '2026-05-23 10:11:16', '2026-05-23 10:11:16', 'beli oli doank'),
(22, 26, 3, 'Sering mogok', 9, 'Completed', '2026-05-30 09:58:43', '2026-05-30 09:59:24', 'Harus selesai hari ini'),
(23, 3, 3, 'Pembelian Sparepart', NULL, 'Completed', '2026-05-30 10:05:08', '2026-05-30 10:05:08', '-');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_servis`
--

CREATE TABLE `transaksi_servis` (
  `trans_id` int NOT NULL,
  `tanggal` datetime DEFAULT NULL,
  `client_id` int DEFAULT NULL,
  `vehicle_id` int DEFAULT NULL,
  `mekanik_id` int DEFAULT NULL,
  `registration_id` int DEFAULT NULL,
  `keluhan` text,
  `status_servis` varchar(50) DEFAULT NULL,
  `total_jasa` double DEFAULT '0',
  `total_sparepart` double DEFAULT '0',
  `grand_total` double DEFAULT '0',
  `bayar` double DEFAULT '0',
  `kembali` double DEFAULT '0',
  `metode_bayar` varchar(50) DEFAULT 'Cash',
  `user_kasir` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi_servis`
--

INSERT INTO `transaksi_servis` (`trans_id`, `tanggal`, `client_id`, `vehicle_id`, `mekanik_id`, `registration_id`, `keluhan`, `status_servis`, `total_jasa`, `total_sparepart`, `grand_total`, `bayar`, `kembali`, `metode_bayar`, `user_kasir`) VALUES
(1, '2026-05-22 15:21:02', 1, 1, 1, NULL, 'Ganti oli dan bongkar mesin', 'Selesai Lunas', 100000, 52000, 152000, 200000, 48000, 'Cash', 'kasir1'),
(2, '2026-05-22 15:21:02', 2, 2, 2, NULL, 'Lampu depan mati', 'Menunggu', 25000, 0, 25000, 0, 0, 'Cash', 'kasir2'),
(3, '2026-05-22 15:21:02', 3, 3, 3, NULL, 'Servis CVT tarikan berat', 'Dikerjakan', 60000, 150000, 210000, 0, 0, 'Cash', 'kasir1'),
(4, '2026-04-22 15:21:02', 4, 4, 1, NULL, 'Ganti ban depan', 'Selesai Lunas', 20000, 210000, 230000, 250000, 20000, 'Cash', 'kasir3'),
(5, '2026-03-22 15:21:02', 5, 5, 4, NULL, 'Rem blong dan ganti kanvas', 'Selesai Lunas', 30000, 55000, 85000, 100000, 15000, 'Cash', 'kasir2'),
(6, '2025-12-31 00:00:00', 6, 6, 5, NULL, 'Motor brebet susut gigi', 'Selesai Lunas', 75000, 25000, 100000, 100000, 0, 'Cash', 'kasir1'),
(7, '2026-03-18 15:21:02', 7, 7, 2, NULL, 'Kelistrikan rewel aki tekor', 'Selesai Lunas', 40000, 220000, 260000, 300000, 40000, 'Cash', 'kasir3'),
(8, '2026-02-11 15:21:02', 8, 8, 3, NULL, 'Ganti filter udara dan oli', 'Selesai Lunas', 45000, 112000, 157000, 200000, 43000, 'Cash', 'kasir2'),
(9, '2025-05-22 15:21:02', 9, 9, 10, NULL, 'Servis ringan saja', 'Selesai Lunas', 60000, 0, 60000, 60000, 0, 'Cash', 'kasir1'),
(10, '2024-05-22 15:21:02', 10, 10, 8, NULL, 'Turun mesin overheat', 'Selesai Lunas', 300000, 0, 300000, 500000, 200000, 'Cash', 'kasir3'),
(11, '2026-05-21 15:21:02', 11, 11, 1, NULL, 'Servis berkala pertama motor baru', 'Selesai Lunas', 50000, 52000, 102000, 150000, 48000, 'Cash', 'kasir1'),
(12, '2026-05-21 15:21:02', 12, 12, 3, NULL, 'Ganti roller dan per CVT', 'Dikerjakan', 80000, 120000, 200000, 0, 0, 'Cash', 'kasir2'),
(13, '2026-05-20 15:21:02', 13, 13, 4, NULL, 'Ganti rantai set lengkap', 'Selesai Lunas', 35000, 185000, 220000, 250000, 30000, 'Cash', 'kasir1'),
(14, '2026-05-20 15:21:02', 14, 14, 3, NULL, 'Tune-up R15 full check', 'Selesai Lunas', 120000, 85000, 205000, 210000, 5000, 'Cash', 'kasir3'),
(15, '2026-05-19 15:21:02', 15, 15, 5, NULL, 'Ganti kampas kopling set', 'Selesai Lunas', 75000, 110000, 185000, 200000, 15000, 'Cash', 'kasir2'),
(16, '2026-05-19 15:21:02', 16, 16, 2, NULL, 'Cek kelistrikan dan ganti busi', 'Selesai Lunas', 50000, 85000, 135000, 150000, 15000, 'Cash', 'kasir1'),
(17, '2026-05-18 15:21:02', 17, 17, 7, NULL, 'Servis injektor dan bersih throttle body', 'Selesai Lunas', 100000, 0, 100000, 100000, 0, 'Cash', 'kasir3'),
(18, '2026-05-17 15:21:02', 18, 18, 10, NULL, 'Ganti oli dan tune up ringan', 'Selesai Lunas', 35000, 70000, 105000, 110000, 5000, 'Cash', 'kasir2'),
(19, '2026-05-16 15:21:02', 19, 19, 7, NULL, 'Servis berkala dan ganti busi', 'Selesai Lunas', 40000, 50000, 90000, 100000, 10000, 'Cash', 'kasir1'),
(20, '2026-05-15 15:21:02', 20, 20, 3, NULL, 'Cek suara mesin kasar', 'Selesai Lunas', 80000, 0, 80000, 80000, 0, 'Cash', 'kasir3'),
(21, '2026-05-12 15:21:02', 21, 21, 4, NULL, 'Ganti ban depan tubeless', 'Selesai Lunas', 25000, 310000, 335000, 350000, 15000, 'Cash', 'kasir2'),
(22, '2026-05-10 15:21:02', 22, 22, 10, NULL, 'Servis ringan + cek aki', 'Selesai Lunas', 30000, 200000, 230000, 250000, 20000, 'Cash', 'kasir1'),
(23, '2026-05-08 15:21:02', 23, 23, 1, NULL, 'Ganti oli + filter udara', 'Selesai Lunas', 40000, 145000, 185000, 200000, 15000, 'Cash', 'kasir3'),
(24, '2026-05-02 15:21:02', 24, 24, 8, NULL, 'Servis besar CVT XMAX', 'Selesai Lunas', 200000, 150000, 350000, 400000, 50000, 'Cash', 'kasir2'),
(25, '2026-04-27 15:21:02', 25, 25, 4, NULL, 'Ganti kampas rem depan belakang', 'Selesai Lunas', 40000, 105000, 145000, 150000, 5000, 'Cash', 'kasir1'),
(26, '2026-05-22 15:21:09', 11, 11, NULL, 13, 'Pembelian Sparepart', 'Selesai Lunas', 0, 65000, 65000, 65000, 0, 'Cash', 'admin'),
(27, '2026-05-22 19:01:08', 6, 6, NULL, 14, 'Pembelian Sparepart', 'Selesai Lunas', 0, 110000, 110000, 200000, 90000, 'Cash', 'admin'),
(28, '2026-05-22 19:21:05', 6, 6, 4, 15, 'gabisa jalan', 'Menunggu', 20000, 900000, 920000, 0, 0, 'Cash', 'admin'),
(29, '2026-05-22 19:33:54', 22, 22, 2, 16, '-', 'Selesai Lunas', 100000, 605000, 705000, 800000, 95000, 'Cash', 'admin'),
(30, '2026-05-23 07:29:07', 25, 25, 9, 17, '-', 'Selesai Lunas', 102000, 450000, 552000, 2000000, 1448000, 'Cash', 'admin'),
(31, '2026-05-23 07:57:56', 25, 27, 6, 18, 'GANTI OLI', 'Selesai Lunas', 35000, 180000, 215000, 250000, 35000, 'Cash', 'admin'),
(32, '2026-05-23 07:59:06', 25, 27, NULL, 19, 'Pembelian Sparepart', 'Selesai Lunas', 0, 110000, 110000, 0, 0, 'Cash', 'admin'),
(33, '2026-05-23 10:06:21', 3, 3, 7, 20, 'Sering mogok', 'Selesai Lunas', 110000, 573000, 683000, 800000, 117000, 'Cash', 'admin'),
(34, '2026-05-23 10:11:16', 25, 27, NULL, 21, 'Pembelian Sparepart', 'Selesai Lunas', 0, 140000, 140000, 200000, 60000, 'Cash', 'admin'),
(35, '2026-05-30 09:58:43', 3, 26, 9, 22, 'Sering mogok', 'Selesai Lunas', 120000, 900000, 1020000, 2000000, 980000, 'Cash', 'admin'),
(36, '2026-05-30 10:05:08', 3, 3, NULL, 23, 'Pembelian Sparepart', 'Selesai Lunas', 0, 85000, 85000, 100000, 15000, 'Cash', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_servis_detail`
--

CREATE TABLE `transaksi_servis_detail` (
  `detail_id` int NOT NULL,
  `trans_id` int NOT NULL,
  `sparepart_id` int DEFAULT NULL,
  `qty` int DEFAULT '1',
  `harga` double DEFAULT '0',
  `subtotal` double DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi_servis_detail`
--

INSERT INTO `transaksi_servis_detail` (`detail_id`, `trans_id`, `sparepart_id`, `qty`, `harga`, `subtotal`) VALUES
(1, 1, 1, 1, 52000, 52000),
(2, 3, 10, 1, 150000, 150000),
(3, 4, 6, 1, 210000, 210000),
(4, 5, 4, 1, 55000, 55000),
(5, 6, 3, 1, 25000, 25000),
(6, 7, 8, 1, 220000, 220000),
(7, 8, 1, 1, 52000, 52000),
(8, 8, 9, 1, 60000, 60000),
(9, 11, 1, 1, 52000, 52000),
(10, 12, 22, 1, 120000, 120000),
(11, 13, 20, 1, 185000, 185000),
(12, 14, 13, 1, 85000, 85000),
(13, 15, 15, 1, 110000, 110000),
(14, 16, 13, 1, 85000, 85000),
(15, 18, 12, 1, 70000, 70000),
(16, 19, 14, 1, 50000, 50000),
(17, 21, 16, 1, 310000, 310000),
(18, 22, 18, 1, 200000, 200000),
(19, 23, 1, 1, 52000, 52000),
(20, 23, 9, 1, 60000, 60000),
(21, 23, 24, 1, 65000, 65000),
(22, 24, 10, 1, 150000, 150000),
(23, 25, 4, 1, 55000, 55000),
(24, 25, 14, 1, 50000, 50000),
(25, 26, 24, 1, 65000, 65000),
(26, 27, 15, 1, 110000, 110000),
(27, 28, 23, 2, 450000, 900000),
(28, 29, 7, 2, 265000, 530000),
(29, 29, 5, 1, 75000, 75000),
(30, 30, 23, 1, 450000, 450000),
(32, 32, 15, 1, 110000, 110000),
(33, 31, 12, 1, 70000, 70000),
(34, 31, 15, 1, 110000, 110000),
(37, 33, 7, 2, 265000, 530000),
(38, 33, 2, 1, 18000, 18000),
(39, 33, 3, 1, 25000, 25000),
(40, 34, 12, 2, 70000, 140000),
(43, 35, 7, 2, 265000, 530000),
(44, 35, 8, 1, 220000, 220000),
(45, 35, 5, 2, 75000, 150000),
(46, 36, 11, 1, 85000, 85000);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_servis_jasa`
--

CREATE TABLE `transaksi_servis_jasa` (
  `detail_id` int NOT NULL,
  `trans_id` int NOT NULL,
  `nama_jasa` varchar(100) DEFAULT NULL,
  `harga` double NOT NULL DEFAULT '0',
  `qty` int NOT NULL DEFAULT '1',
  `subtotal` double NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi_servis_jasa`
--

INSERT INTO `transaksi_servis_jasa` (`detail_id`, `trans_id`, `nama_jasa`, `harga`, `qty`, `subtotal`) VALUES
(1, 28, 'jasa pasang ban', 20000, 1, 20000),
(2, 29, 'Jasa servis', 80000, 1, 80000),
(3, 29, 'Jasa pasang ban', 20000, 1, 20000),
(4, 30, 'Jasa servis', 80000, 1, 80000),
(5, 30, 'Belah mesin', 20000, 1, 20000),
(6, 30, 'ganti ban', 2000, 1, 2000),
(8, 31, 'Jasa ganti oli', 20000, 1, 20000),
(9, 31, 'ganti kampas', 15000, 1, 15000),
(12, 33, 'Jasa Penggantian Ban', 30000, 1, 30000),
(13, 33, 'Jasa Servis', 80000, 1, 80000),
(17, 35, 'Jasa servis', 80000, 1, 80000),
(18, 35, 'Jasa penggantian Ban', 20000, 1, 20000),
(19, 35, 'Jasa penggantian Aki', 20000, 1, 20000);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'admin',
  `nama_lengkap` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `role`, `nama_lengkap`) VALUES
(1, 'admin', 'admin123', 'admin', 'Administrator Utama'),
(2, 'kasir1', 'kasir123', 'kasir', 'Kasir Bulan'),
(3, 'kasir2', 'kasir123', 'kasir', 'Kasir Bintang'),
(4, 'manager1', 'manager123', 'manager', 'Manager Surya'),
(5, 'kasir3', 'kasir123', 'kasir', 'Kasir Bumi'),
(6, 'kasir4', 'kasir123', 'kasir', 'Kasir Mars'),
(7, 'admin2', 'admin123', 'admin', 'Admin Kedua'),
(8, 'superadmin', 'super123', 'admin', 'Super Admin'),
(9, 'kasir5', 'kasir123', 'kasir', 'Kasir Jupiter'),
(10, 'kasir6', 'kasir123', 'kasir', 'Kasir Saturnus');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle`
--

CREATE TABLE `vehicle` (
  `vehicle_id` int NOT NULL,
  `client_id` int NOT NULL,
  `no_polisi` varchar(20) NOT NULL,
  `merk` varchar(50) DEFAULT NULL,
  `tipe` varchar(50) DEFAULT NULL,
  `cc` int DEFAULT NULL,
  `tipe_kendaraan` varchar(50) DEFAULT NULL,
  `tahun` int DEFAULT NULL,
  `no_rangka` varchar(50) DEFAULT NULL,
  `no_mesin` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vehicle`
--

INSERT INTO `vehicle` (`vehicle_id`, `client_id`, `no_polisi`, `merk`, `tipe`, `cc`, `tipe_kendaraan`, `tahun`, `no_rangka`, `no_mesin`) VALUES
(1, 1, 'B 1234 ABC', 'Honda', 'Vario 150', 150, 'Lebih dari Roda 2', 2020, 'MHK123456789A', 'JF123456A'),
(2, 2, 'D 5678 DEF', 'Yamaha', 'NMAX 155', 155, 'Lebih dari Roda 2', 2021, 'MHY987654321B', 'YG987654B'),
(3, 3, 'L 9012 GHI', 'Honda', 'PCX 160', 160, 'Lebih dari Roda 2', 2022, 'MHK112233445C', 'JF112233C'),
(4, 4, 'BK 3456 JKL', 'Yamaha', 'Aerox 155', 155, 'Lebih dari Roda 2', 2021, 'MHY223344556D', 'YG223344D'),
(5, 5, 'DD 7890 MNO', 'Honda', 'Beat Street', 110, 'Lebih dari Roda 2', 2019, 'MHK334455667E', 'JF334455E'),
(6, 6, 'H 1122 PQR', 'Kawasaki', 'KLX 150', 150, 'Roda 2', 2018, 'MHK445566778F', 'JF445566F'),
(7, 7, 'AB 3344 STU', 'Suzuki', 'GSX-R150', 150, 'Roda 2', 2020, 'MHS556677889G', 'SG556677G'),
(8, 8, 'DK 5566 VWX', 'Honda', 'CBR 150R', 150, 'Roda 2', 2021, 'MHK667788990H', 'KH667788H'),
(9, 9, 'BA 7788 YZA', 'Yamaha', 'MT-15', 150, 'Roda 2', 2022, 'MHY778899001I', 'YG778899I'),
(10, 10, 'BG 9900 BCD', 'Honda', 'Scoopy', 110, 'Lebih dari Roda 2', 2020, 'MHK889900112J', 'JF889900J'),
(11, 11, 'B 2345 KLM', 'Honda', 'ADV 160', 160, 'Lebih dari Roda 2', 2023, 'MHK990011223K', 'JF990011K'),
(12, 12, 'B 3456 NOP', 'Yamaha', 'Lexi 125', 125, 'Lebih dari Roda 2', 2022, 'MHY001122334L', 'YG001122L'),
(13, 13, 'L 4567 QRS', 'Honda', 'Supra X 125 FI', 125, 'Roda 2', 2020, 'MHK112233445M', 'JF112234M'),
(14, 14, 'N 5678 TUV', 'Yamaha', 'R15 V4', 155, 'Roda 2', 2023, 'MHY223344556N', 'YG223345N'),
(15, 15, 'AG 6789 WXY', 'Honda', 'CRF 150L', 150, 'Roda 2', 2021, 'MHK334455667O', 'JF334456O'),
(16, 16, 'B 7890 ZAB', 'Kawasaki', 'Ninja ZX-25R', 250, 'Roda 2', 2022, 'MHK445566778P', 'KH445567P'),
(17, 17, 'D 8901 CDE', 'Honda', 'Genio', 110, 'Lebih dari Roda 2', 2021, 'MHK556677889Q', 'JF556678Q'),
(18, 18, 'B 9012 FGH', 'Yamaha', 'Mio M3', 125, 'Lebih dari Roda 2', 2020, 'MHY667788990R', 'YG667789R'),
(19, 19, 'F 0123 IJK', 'Honda', 'Revo FI', 110, 'Roda 2', 2019, 'MHK778899001S', 'JF778890S'),
(20, 20, 'B 1235 LMN', 'Yamaha', 'XSR 155', 155, 'Roda 2', 2023, 'MHY889900112T', 'YG889901T'),
(21, 21, 'D 2346 OPQ', 'Honda', 'Vario 125', 125, 'Lebih dari Roda 2', 2022, 'MHK990011223U', 'JF990012U'),
(22, 22, 'B 3457 RST', 'Suzuki', 'Address FI', 110, 'Lebih dari Roda 2', 2021, 'MHS001122334V', 'SG001123V'),
(23, 23, 'D 4568 UVW', 'Honda', 'Beat FI', 110, 'Lebih dari Roda 2', 2020, 'MHK112233446W', 'JF112235W'),
(24, 24, 'B 5679 XYZ', 'Yamaha', 'XMAX 250', 250, 'Lebih dari Roda 2', 2022, 'MHY223344557X', 'YG223346X'),
(25, 25, 'B 6780 ABC', 'Honda', 'CB150 Verza', 150, 'Roda 2', 2021, 'MHK334455668Y', 'JF334457Y'),
(26, 3, 'D 1281 DF', 'Honda', 'CRV', 1500, 'Lebih dari Roda 2', 2024, '', ''),
(27, 25, 'T 8769 JK', 'HONDA', 'CB 150R', 150, 'Roda 2', 2024, '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `mekanik`
--
ALTER TABLE `mekanik`
  ADD PRIMARY KEY (`mekanik_id`);

--
-- Indexes for table `sparepart`
--
ALTER TABLE `sparepart`
  ADD PRIMARY KEY (`sparepart_id`),
  ADD UNIQUE KEY `kode_sparepart` (`kode_sparepart`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `transaksi_pembelian`
--
ALTER TABLE `transaksi_pembelian`
  ADD PRIMARY KEY (`purchase_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `sparepart_id` (`sparepart_id`);

--
-- Indexes for table `transaksi_pendaftaran`
--
ALTER TABLE `transaksi_pendaftaran`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `mekanik_id` (`mekanik_id`);

--
-- Indexes for table `transaksi_servis`
--
ALTER TABLE `transaksi_servis`
  ADD PRIMARY KEY (`trans_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `mekanik_id` (`mekanik_id`),
  ADD KEY `registration_id` (`registration_id`);

--
-- Indexes for table `transaksi_servis_detail`
--
ALTER TABLE `transaksi_servis_detail`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `trans_id` (`trans_id`),
  ADD KEY `sparepart_id` (`sparepart_id`);

--
-- Indexes for table `transaksi_servis_jasa`
--
ALTER TABLE `transaksi_servis_jasa`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `trans_id` (`trans_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD UNIQUE KEY `no_polisi` (`no_polisi`),
  ADD KEY `client_id` (`client_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `client_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `mekanik`
--
ALTER TABLE `mekanik`
  MODIFY `mekanik_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sparepart`
--
ALTER TABLE `sparepart`
  MODIFY `sparepart_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `supplier_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transaksi_pembelian`
--
ALTER TABLE `transaksi_pembelian`
  MODIFY `purchase_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `transaksi_pendaftaran`
--
ALTER TABLE `transaksi_pendaftaran`
  MODIFY `registration_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `transaksi_servis`
--
ALTER TABLE `transaksi_servis`
  MODIFY `trans_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `transaksi_servis_detail`
--
ALTER TABLE `transaksi_servis_detail`
  MODIFY `detail_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `transaksi_servis_jasa`
--
ALTER TABLE `transaksi_servis_jasa`
  MODIFY `detail_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `vehicle`
--
ALTER TABLE `vehicle`
  MODIFY `vehicle_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sparepart`
--
ALTER TABLE `sparepart`
  ADD CONSTRAINT `sparepart_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`) ON DELETE SET NULL;

--
-- Constraints for table `transaksi_pembelian`
--
ALTER TABLE `transaksi_pembelian`
  ADD CONSTRAINT `transaksi_pembelian_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`),
  ADD CONSTRAINT `transaksi_pembelian_ibfk_2` FOREIGN KEY (`sparepart_id`) REFERENCES `sparepart` (`sparepart_id`);

--
-- Constraints for table `transaksi_pendaftaran`
--
ALTER TABLE `transaksi_pendaftaran`
  ADD CONSTRAINT `transaksi_pendaftaran_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`vehicle_id`),
  ADD CONSTRAINT `transaksi_pendaftaran_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`),
  ADD CONSTRAINT `transaksi_pendaftaran_ibfk_3` FOREIGN KEY (`mekanik_id`) REFERENCES `mekanik` (`mekanik_id`);

--
-- Constraints for table `transaksi_servis`
--
ALTER TABLE `transaksi_servis`
  ADD CONSTRAINT `transaksi_servis_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`),
  ADD CONSTRAINT `transaksi_servis_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`vehicle_id`),
  ADD CONSTRAINT `transaksi_servis_ibfk_3` FOREIGN KEY (`mekanik_id`) REFERENCES `mekanik` (`mekanik_id`),
  ADD CONSTRAINT `transaksi_servis_ibfk_4` FOREIGN KEY (`registration_id`) REFERENCES `transaksi_pendaftaran` (`registration_id`) ON DELETE SET NULL;

--
-- Constraints for table `transaksi_servis_detail`
--
ALTER TABLE `transaksi_servis_detail`
  ADD CONSTRAINT `transaksi_servis_detail_ibfk_1` FOREIGN KEY (`trans_id`) REFERENCES `transaksi_servis` (`trans_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_servis_detail_ibfk_2` FOREIGN KEY (`sparepart_id`) REFERENCES `sparepart` (`sparepart_id`) ON DELETE SET NULL;

--
-- Constraints for table `transaksi_servis_jasa`
--
ALTER TABLE `transaksi_servis_jasa`
  ADD CONSTRAINT `transaksi_servis_jasa_ibfk_1` FOREIGN KEY (`trans_id`) REFERENCES `transaksi_servis` (`trans_id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD CONSTRAINT `vehicle_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
