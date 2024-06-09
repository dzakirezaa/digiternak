-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 07, 2024 at 02:50 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `digiternak_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `breed_of_livestock`
--

CREATE TABLE `breed_of_livestock` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_deleted` tinyint(4) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `breed_of_livestock`
--

INSERT INTO `breed_of_livestock` (`id`, `name`, `is_deleted`, `updated_at`) VALUES
(1, 'Bali', 0, '2023-12-17 06:50:57'),
(2, 'Madura', 0, '2023-12-17 06:50:57');

-- --------------------------------------------------------

--
-- Table structure for table `cage`
--

CREATE TABLE `cage` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(10) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cage`
--

INSERT INTO `cage` (`id`, `user_id`, `name`, `location`, `description`) VALUES
(1, 1, 'Kandang 3', 'Cilebut', ''),
(2, 1, 'Kandang 2', 'Dramaga', ''),
(3, 1, 'Kandang 5', 'Bogor', 'this is example of cage description'),
(4, 1, 'Kandang Si', 'Dramaga', 'this is example of cage description'),
(5, 1, 'Kandang Si', 'Dramaga', 'this is example of cage description'),
(17, 1, 'Kandang 6', 'Dramaga', 'this is example of cage description');

-- --------------------------------------------------------

--
-- Table structure for table `gender`
--

CREATE TABLE `gender` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_deleted` tinyint(4) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gender`
--

INSERT INTO `gender` (`id`, `name`, `is_deleted`, `updated_at`) VALUES
(0, 'Prefer Not to Say', 0, '2024-02-17 23:35:30'),
(1, 'Laki-laki', 0, '2023-12-17 06:50:57'),
(2, 'Perempuan', 0, '2023-12-17 06:50:57');

-- --------------------------------------------------------

--
-- Table structure for table `livestock`
--

CREATE TABLE `livestock` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `eid` bigint(11) DEFAULT NULL,
  `vid` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `cage_id` int(11) NOT NULL,
  `type_of_livestock_id` int(11) DEFAULT NULL,
  `breed_of_livestock_id` int(11) DEFAULT NULL,
  `maintenance_id` int(11) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `ownership_status_id` int(11) DEFAULT NULL,
  `reproduction_id` int(11) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `age` varchar(255) DEFAULT NULL,
  `chest_size` decimal(18,1) DEFAULT NULL,
  `body_weight` decimal(18,1) DEFAULT NULL,
  `health` varchar(255) DEFAULT NULL,
  `livestock_image` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(4) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `purpose_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `livestock`
--

INSERT INTO `livestock` (`id`, `user_id`, `eid`, `vid`, `name`, `birthdate`, `cage_id`, `type_of_livestock_id`, `breed_of_livestock_id`, `maintenance_id`, `source_id`, `ownership_status_id`, `reproduction_id`, `gender`, `age`, `chest_size`, `body_weight`, `health`, `livestock_image`, `created_at`, `is_deleted`, `updated_at`, `purpose_id`) VALUES
(3, 1, NULL, 'MUC2553', 'Sapi Jikri', '2024-04-10', 1, 1, 1, 1, 1, 1, 1, 'Jantan', '2 Tahun', 120.0, 300.0, 'Sehat', NULL, '2024-04-24 04:11:12', 0, '2024-04-24 04:11:12', NULL),
(4, 1, NULL, 'OWU3593', 'Sapi Pulung', '2024-04-10', 1, 1, 1, 1, 1, 1, 1, 'Jantan', '2 Tahun', 120.0, 300.0, 'Sehat', NULL, '2024-04-24 05:46:37', 0, '2024-04-24 05:46:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `livestock_images`
--

CREATE TABLE `livestock_images` (
  `id` int(11) NOT NULL,
  `livestock_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `livestock_images`
--

INSERT INTO `livestock_images` (`id`, `livestock_id`, `image_path`) VALUES
(11, 3, 'livestock/1/3/-iwH6NhYVF1J0.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

CREATE TABLE `maintenance` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_deleted` tinyint(4) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance`
--

INSERT INTO `maintenance` (`id`, `name`, `is_deleted`, `updated_at`) VALUES
(1, 'Kandang', 0, '2023-12-17 06:50:57'),
(2, 'Gembala', 0, '2023-12-17 06:50:57'),
(3, 'Campuran', 0, '2023-12-17 06:50:57');

-- --------------------------------------------------------

--
-- Table structure for table `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1702795813);

-- --------------------------------------------------------

--
-- Table structure for table `note`
--

CREATE TABLE `note` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `livestock_name` varchar(255) NOT NULL,
  `livestock_id` int(11) DEFAULT NULL,
  `livestock_vid` varchar(10) NOT NULL,
  `livestock_cage` varchar(10) NOT NULL,
  `date_recorded` datetime NOT NULL,
  `location` varchar(255) NOT NULL,
  `livestock_feed` varchar(255) NOT NULL,
  `costs` int(11) NOT NULL,
  `details` text NOT NULL,
  `documentation` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `note`
--

INSERT INTO `note` (`id`, `user_id`, `livestock_name`, `livestock_id`, `livestock_vid`, `livestock_cage`, `date_recorded`, `location`, `livestock_feed`, `costs`, `details`, `documentation`, `created_at`, `updated_at`) VALUES
(28, 1, 'Sapi Jikri', 3, 'MUC2553', 'Kandang 1', '2024-05-10 00:00:00', 'Dramaga', 'Jeruk', 200000, 'Ini contoh catatan ternak wokwok', NULL, '2024-05-10 15:29:58', '2024-05-10 15:29:58'),
(30, 1, 'Sapi Jikri', 3, 'MUC2553', 'Kandang 3', '2024-06-07 00:00:00', 'Cilebut', 'Konsentrat', 0, 'Ini contoh catatan ternak wokwok', NULL, '2024-06-07 01:19:37', '2024-06-07 01:19:37'),
(31, 1, 'Sapi Jikri', 3, 'MUC2553', 'Kandang 3', '2024-06-07 08:23:00', 'Cilebut', 'Konsentrat', 0, 'Ini contoh catatan ternak wokwok', NULL, '2024-06-07 01:23:00', '2024-06-07 01:23:00');

-- --------------------------------------------------------

--
-- Table structure for table `note_images`
--

CREATE TABLE `note_images` (
  `id` int(11) NOT NULL,
  `note_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ownership_status`
--

CREATE TABLE `ownership_status` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_deleted` tinyint(4) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ownership_status`
--

INSERT INTO `ownership_status` (`id`, `name`, `is_deleted`, `updated_at`) VALUES
(1, 'Milik Sendiri', 0, '2023-12-17 06:50:57'),
(2, 'Milik Kelompok', 0, '2023-12-17 06:50:57'),
(3, 'Titipan', 0, '2023-12-17 06:50:57');

-- --------------------------------------------------------

--
-- Table structure for table `purpose`
--

CREATE TABLE `purpose` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_deleted` tinyint(4) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purpose`
--

INSERT INTO `purpose` (`id`, `name`, `is_deleted`, `updated_at`) VALUES
(1, 'Indukan', 0, '2023-12-17 06:50:57'),
(2, 'Penggemukan', 0, '2023-12-17 06:50:57'),
(3, 'Tabungan', 0, '2023-12-17 06:50:57'),
(4, 'Belum Tau', 0, '2023-12-17 06:50:57');

-- --------------------------------------------------------

--
-- Table structure for table `reproduction`
--

CREATE TABLE `reproduction` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_deleted` tinyint(4) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reproduction`
--

INSERT INTO `reproduction` (`id`, `name`, `is_deleted`, `updated_at`) VALUES
(1, 'Tidak Bunting', 0, '2023-12-17 06:50:57'),
(2, 'Bunting < 1 Bulan', 0, '2023-12-17 06:50:57'),
(3, 'Bunting 1 Bulan', 0, '2023-12-17 06:50:57'),
(4, 'Bunting 2 Bulan', 0, '2023-12-17 06:50:57'),
(5, 'Bunting 3 Bulan', 0, '2023-12-17 06:50:57'),
(6, 'Bunting 4 Bulan', 0, '2023-12-17 06:50:57'),
(7, 'Bunting 5 Bulan', 0, '2023-12-17 06:50:57'),
(8, 'Bunting 6 Bulan', 0, '2023-12-17 06:50:57'),
(9, 'Bunting 7 Bulan', 0, '2023-12-17 06:50:57'),
(10, 'Bunting 8 Bulan', 0, '2023-12-17 06:50:57'),
(11, 'Bunting 9 Bulan', 0, '2023-12-17 06:50:57'),
(12, 'Bunting 10 Bulan', 0, '2023-12-17 06:50:57'),
(13, 'Bunting 11 Bulan', 0, '2023-12-17 06:50:57'),
(14, 'Bunting 12 Bulan', 0, '2023-12-17 06:50:57'),
(15, 'Bunting 13 Bulan', 0, '2023-12-17 06:50:57'),
(16, 'Bunting 14 Bulan', 0, '2023-12-17 06:50:57'),
(17, 'Bunting 15 Bulan', 0, '2023-12-17 06:50:57');

-- --------------------------------------------------------

--
-- Table structure for table `source`
--

CREATE TABLE `source` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_deleted` tinyint(4) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `source`
--

INSERT INTO `source` (`id`, `name`, `is_deleted`, `updated_at`) VALUES
(1, 'Sejak Lahir', 0, '2023-12-17 06:50:57'),
(2, 'Bantuan Pemerintah', 0, '2023-12-17 06:50:57'),
(3, 'Beli', 0, '2023-12-17 06:50:57'),
(4, 'Beli dari Dalam Kelompok', 0, '2023-12-17 06:50:57'),
(5, 'Beli dari Luar Kelompok', 0, '2023-12-17 06:50:57'),
(6, 'Inseminasi Buatan', 0, '2023-12-17 06:50:57'),
(7, 'Kawin Alam', 0, '2023-12-17 06:50:57'),
(8, 'Tidak Tau', 0, '2023-12-17 06:50:57');

-- --------------------------------------------------------

--
-- Table structure for table `type_of_livestock`
--

CREATE TABLE `type_of_livestock` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_deleted` tinyint(4) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `type_of_livestock`
--

INSERT INTO `type_of_livestock` (`id`, `name`, `is_deleted`, `updated_at`) VALUES
(1, 'Sapi', 0, '2023-12-17 06:50:57'),
(2, 'Kambing', 0, '2023-12-17 06:50:57');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `gender_id` int(11) DEFAULT NULL,
  `nik` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `phone_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `auth_key` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_completed` tinyint(1) DEFAULT 0,
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `role_id`, `gender_id`, `nik`, `full_name`, `birthdate`, `phone_number`, `address`, `auth_key`, `password_hash`, `password_reset_token`, `verification_token`, `status`, `created_at`, `updated_at`, `is_completed`, `is_verified`) VALUES
(1, 'lele', 'ulu123@example.com', 1, 1, '4321567894523421', 'ale ula', '2024-01-20', '081234544444', 'Jalan Cendrawasih No. 25, RT 03/RW 05, Kelurahan Mulyorejo, Kecamatan Sukomanunggal, Kota Surabaya, Jawa Timur, 60112', '4bfe0a2bd040a97e66a72a98eb76779d47d814c7769baa51396174c80b2b8f3e', '$2y$13$3z9TnUzBDn6fgNaU4DzzP.DDaYD6iLIexAQTkMFdmENiI8yE/MYJy', NULL, NULL, 10, '2024-04-23 15:33:43', '2024-06-07 03:05:47', 1, 1),
(2, 'lulu', 'lulu123@example.com', 1, NULL, '', '', NULL, NULL, '', NULL, '$2y$13$eQjTD3Ik/rptAYmuxcI84OB2xDH0TxyUdicINTaeNgBTf1zAWYDxu', NULL, NULL, 10, '2024-04-24 03:22:18', '2024-04-24 03:22:18', 0, 0),
(3, 'luluk', 'luluk123@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$MgUHnbwV8nMr4OsgCOTa/Obue8O2jTcdoO4.54oBis79gbGmhu.FS', NULL, NULL, 10, '2024-04-24 03:24:03', '2024-04-24 03:24:03', 0, 0),
(4, 'pelo', 'pelo123@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$eyTdGc0yq4gaJOY368gCMePdBBaitJOERaiT.3T0cLJJt0GFJ3VFW', NULL, NULL, 10, '2024-04-25 04:19:29', '2024-04-25 04:19:29', 0, 0),
(5, 'palo', 'palo123@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$mjYSrrmQwvFa5zSUn3N.s./fgKmTJf7PhmRdDOYmcFxBqfcmXe/7m', NULL, NULL, 10, '2024-04-25 04:35:06', '2024-04-25 04:35:06', 0, 0),
(6, 'pilo', 'pilo123@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$6C0hbSJJ3.CF9UTnpGEA2upAiuKS0e8dqORjJexXnKAXR63RVVFt2', NULL, NULL, 10, '2024-04-25 04:38:49', '2024-04-25 04:38:49', 0, 0),
(7, 'pulo', 'pulo123@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$/PiqUl85EZwhAZkF8NgwNO0EENq/7aHAwnJOeDjhH9r4slflBQY0a', NULL, NULL, 10, '2024-04-25 04:41:28', '2024-04-25 04:41:28', 0, 0),
(8, 'qlo', 'qlo123@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$ahc8wOLRcUAU1cHZCtumTuqoCewqrBDeqDvepHqPSZQBfuLhfG0Fe', NULL, NULL, 10, '2024-04-25 05:45:00', '2024-04-25 05:45:00', 0, 0),
(9, 'alo', 'alo123@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$A2VXGk7Y4NNtI4AW8MuADOfedM5IKJYEiJsyqxa5gDlOn79nTv5h2', NULL, NULL, 10, '2024-04-25 05:47:30', '2024-04-25 05:47:30', 0, 0),
(10, 'elo', 'elo123@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$fZxWOVVRNG0NylSrIOFi.uHRHtUzUmUO0Je2mnQ7C7I8xS8ihPe2S', NULL, NULL, 10, '2024-04-25 05:52:12', '2024-04-25 05:52:12', 0, 0),
(11, 'ilo', 'ilo123@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$cgoI71me75TlRSZ/9/7COevUIXD1plDs69A8RY4PfQPMtUHC91UdG', NULL, NULL, 10, '2024-04-25 06:08:23', '2024-04-25 06:08:23', 0, 0),
(12, 'lolo', 'olo123@example.com', 1, 1, '4321567894523423', 'ale ula', '2024-01-20', '081234544444', 'Jalan Cendrawasih No. 25, RT 03/RW 05, Kelurahan Mulyorejo, Kecamatan Sukomanunggal, Kota Surabaya, Jawa Timur, 60112', 'NpBTB-TJtT0g0xSZd4V0LgajPzg_-wEZ', '$2y$13$mPpPzPcnWHsRlWGWfYpLde4uBl0XvyrihvfEHnDMsjh5mRQmk2Qzy', NULL, 'G9i7-T5ZVuZ3kWWny-ERg-Tox7UroWPc', 10, '2024-04-25 06:19:24', '2024-04-25 07:50:11', 1, 0),
(13, 'yuyu', 'yuyu123@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$EAOFHE401Nv8TbdFzN/ruO81ztxxNuGNZtjj0mHLbyREUx.qTK46G', NULL, 'Eamoyoym-D9OAGyNM2YsHaDyErxsHHSr', 10, '2024-04-25 07:00:06', '2024-04-25 07:00:06', 0, 0),
(14, 'yuyo', 'yuyo123@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$QfaOiGMztcBtK7JqUFVIUOlfNOaPuGFmDKbggtKY4xHrhDP38sDae', NULL, 'e_Acqg_0usXNcR91_b-Il7HM_cvf04ZO', 10, '2024-04-25 07:14:49', '2024-04-25 07:14:49', 0, 0),
(15, 'testuser', 'testuser@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$TVLWHlTy7WirNNA40SfNDOp2pemoDdR7lxer.faZuToP7wjuDboie', NULL, 'H6Tlm6tppRXwUXON9NBM1iGRib5QpU4a', 10, '2024-04-25 08:36:27', '2024-04-25 08:36:27', 0, 0),
(16, 'testuse1', 'testuser1@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$IQmhbbnwkKrA7X8brcSIuuyM/Vh5A1Z.iIYWam7CGa4r2W5W3kLUC', NULL, 'BsUVXjyl2PafrrbdTFtrdp5fI5Cti8tI', 10, '2024-04-25 08:40:46', '2024-04-25 08:40:46', 0, 0),
(17, 'testuse2', 'testuser2@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$pkPIyjBwmYUSQ0IMs4k34eil2ooIM457jWDprW12sbEbtl5R.st5W', NULL, 'nV1yc26ANtpCNr8_pga9JrXKSGmql9N_', 10, '2024-04-25 08:45:13', '2024-04-25 08:45:13', 0, 0),
(18, 'testuser3', 'testuser3@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$PP5lMwAwAGCv4.way5zuQePvNaeypyJV28etZmfaAJXjlPQkyFaXW', NULL, '76m9Bt3JRl0TiHG4QQV9p6MOodf3wSe6', 10, '2024-04-25 08:52:05', '2024-04-25 08:52:05', 0, 0),
(19, 'testuser4', 'testuser4@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$zgfygmDSiiV1QeVxZB1IeOHwCyQTutUhUwt9w07J51dbnN.CLGlj6', NULL, '6dM10USThm59QckSgscXUtuxwZufhF_z', 10, '2024-04-25 08:52:35', '2024-05-16 16:42:47', 0, 1),
(20, 'testuser5', 'testuser5@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$Il.JjNhfxnIVur4xM2mA8ule5id5xQiRgyot1dwY.CnFPKJTjd/y6', NULL, 'rwmbMrDpTE0eoX7owCww456rRMzOF5s8', 10, '2024-04-25 08:53:35', '2024-05-11 13:32:00', 0, 1),
(33, 'astro', 'astrogo54@gmail.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$ZXGTiYFczbNDYkzuQoogJO.XCQBOd5cYy6D8NoBp5PRU43L810FDi', NULL, 'SGNzNWp1bXdCcEptUVRSVkpvRm0zYlNfRXJDVkVDTnA6MTcxNDY0MTc3OA==', 10, '2024-05-02 09:22:58', '2024-05-02 09:22:58', 0, 0),
(47, 'wowo', 'wowo@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, '2W1pnGnhz9M4VyuoMzYbAKf3bf28N_bJ', '$2y$13$lC.YV6ERs52GIUjzH7wNmOOq8EB4Qoo0nMdpJnMnaSL1d6cKeXgge', NULL, 'hQpYYHq-EtLLnJNQ8yPyrMXjiDSOHZkr', 10, '2024-05-10 04:42:00', '2024-05-11 04:05:03', 0, 1),
(52, 'hihi', 'hihi2@example.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'EMcx3SZ1kaKGxwG0vVom2hPvrGaVKb0Y', '$2y$13$7xCp9iNbXHvJWzJXz6gGuexb70m7sDuTy84edu/tDbzU7znbmeWei', NULL, NULL, 10, '2024-05-10 16:16:08', '2024-05-11 02:11:48', 0, 1),
(55, 'admin1', 'admin1@example.com', 2, NULL, NULL, NULL, NULL, NULL, NULL, '92BiCgpty3wCkhcwNloDxrxeoqXreDI2', '$2y$13$wGGhpgE/xLfAgY0/Mb6Y0.TG62/nwo7F5eXfROV7vG0omwZfqzzbK', NULL, NULL, 10, '2024-05-11 02:16:46', '2024-05-11 10:13:47', 0, 1),
(57, 'aotabu', 'aotabu12@gmail.com', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$.OdEw8vDwBFIi9jd1dOzfubD6PLKQCnlIS7GFATR5bwZX9QOc19nm', NULL, 'bDRFc2hsNG4wN1dmcDR1YzFfY3Z6TDJYT0NNOHlhR0w6MTcxNzQxNTMzNg==', 10, '2024-06-03 11:48:56', '2024-06-03 11:48:56', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`id`, `name`) VALUES
(1, 'Peternak'),
(2, 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `breed_of_livestock`
--
ALTER TABLE `breed_of_livestock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cage`
--
ALTER TABLE `cage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_cage` (`user_id`);

--
-- Indexes for table `gender`
--
ALTER TABLE `gender`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `livestock`
--
ALTER TABLE `livestock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vid` (`vid`),
  ADD UNIQUE KEY `eid` (`eid`),
  ADD KEY `fk_breed_of_livestock` (`breed_of_livestock_id`),
  ADD KEY `fk_maintenance` (`maintenance_id`),
  ADD KEY `fk_ownership_status` (`ownership_status_id`),
  ADD KEY `fk_reproduction` (`reproduction_id`),
  ADD KEY `fk_source` (`source_id`),
  ADD KEY `fk_type_of_livestock` (`type_of_livestock_id`),
  ADD KEY `fk_cage_livestock` (`cage_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_purpose_id` (`purpose_id`);

--
-- Indexes for table `livestock_images`
--
ALTER TABLE `livestock_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_livestock_images` (`livestock_id`);

--
-- Indexes for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `note`
--
ALTER TABLE `note`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_livestock_vid` (`livestock_vid`),
  ADD KEY `livestock_id` (`livestock_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `note_images`
--
ALTER TABLE `note_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_livestock_images` (`note_id`);

--
-- Indexes for table `ownership_status`
--
ALTER TABLE `ownership_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purpose`
--
ALTER TABLE `purpose`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reproduction`
--
ALTER TABLE `reproduction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `source`
--
ALTER TABLE `source`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `type_of_livestock`
--
ALTER TABLE `type_of_livestock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_role_user` (`role_id`),
  ADD KEY `fk_gender_user` (`gender_id`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cage`
--
ALTER TABLE `cage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `livestock`
--
ALTER TABLE `livestock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `livestock_images`
--
ALTER TABLE `livestock_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `note`
--
ALTER TABLE `note`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `note_images`
--
ALTER TABLE `note_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cage`
--
ALTER TABLE `cage`
  ADD CONSTRAINT `fk_user_cage` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `livestock`
--
ALTER TABLE `livestock`
  ADD CONSTRAINT `fk_breed_of_livestock` FOREIGN KEY (`breed_of_livestock_id`) REFERENCES `breed_of_livestock` (`id`),
  ADD CONSTRAINT `fk_cage_livestock` FOREIGN KEY (`cage_id`) REFERENCES `cage` (`id`),
  ADD CONSTRAINT `fk_maintenance` FOREIGN KEY (`maintenance_id`) REFERENCES `maintenance` (`id`),
  ADD CONSTRAINT `fk_ownership_status` FOREIGN KEY (`ownership_status_id`) REFERENCES `ownership_status` (`id`),
  ADD CONSTRAINT `fk_purpose` FOREIGN KEY (`purpose_id`) REFERENCES `purpose` (`id`),
  ADD CONSTRAINT `fk_reproduction` FOREIGN KEY (`reproduction_id`) REFERENCES `reproduction` (`id`),
  ADD CONSTRAINT `fk_source` FOREIGN KEY (`source_id`) REFERENCES `source` (`id`),
  ADD CONSTRAINT `fk_type_of_livestock` FOREIGN KEY (`type_of_livestock_id`) REFERENCES `type_of_livestock` (`id`),
  ADD CONSTRAINT `fk_user_livestock` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `livestock_images`
--
ALTER TABLE `livestock_images`
  ADD CONSTRAINT `fk_livestock_images` FOREIGN KEY (`livestock_id`) REFERENCES `livestock` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `note`
--
ALTER TABLE `note`
  ADD CONSTRAINT `fk_livestock_id` FOREIGN KEY (`livestock_id`) REFERENCES `livestock` (`id`),
  ADD CONSTRAINT `fk_livestock_vid` FOREIGN KEY (`livestock_vid`) REFERENCES `livestock` (`vid`),
  ADD CONSTRAINT `fk_user_note` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `note_images`
--
ALTER TABLE `note_images`
  ADD CONSTRAINT `fk_note_images` FOREIGN KEY (`note_id`) REFERENCES `note` (`id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_gender_user` FOREIGN KEY (`gender_id`) REFERENCES `gender` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_role_user` FOREIGN KEY (`role_id`) REFERENCES `user_role` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
