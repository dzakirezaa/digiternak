-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 21, 2024 at 08:20 PM
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
-- Database: `digiternak_prod`
--

-- --------------------------------------------------------

--
-- Table structure for table `bcs`
--

CREATE TABLE `bcs` (
  `id` int(11) NOT NULL,
  `livestock_id` int(11) NOT NULL,
  `body_weight` double NOT NULL,
  `chest_size` double NOT NULL,
  `hips` double NOT NULL,
  `bcs_image` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bcs`
--

INSERT INTO `bcs` (`id`, `livestock_id`, `body_weight`, `chest_size`, `hips`, `bcs_image`, `created_at`, `updated_at`) VALUES
(1, 1, 2100, 100, 50, NULL, '2024-06-09 13:09:31', '2024-06-09 14:02:34'),
(2, 1, 500, 100, 50, NULL, '2024-06-09 13:12:17', '2024-06-09 13:12:17'),
(3, 1, 500, 100, 50, NULL, '2024-06-09 13:31:43', '2024-06-09 13:31:43'),
(4, 1, 500, 100, 50, NULL, '2024-06-09 13:36:32', '2024-06-09 13:36:32'),
(5, 1, 500, 100, 50, NULL, '2024-06-09 13:36:48', '2024-06-09 13:36:48'),
(6, 1, 500, 100, 50, NULL, '2024-06-09 13:39:54', '2024-06-09 13:39:54'),
(7, 1, 2100, 100, 50, NULL, '2024-06-09 13:55:25', '2024-06-09 13:55:25'),
(9, 3, 600, 100, 50, NULL, '2024-06-10 16:20:39', '2024-06-10 16:20:39'),
(10, 3, 600, 100, 50, NULL, '2024-06-10 16:25:01', '2024-06-10 16:25:01'),
(11, 3, 200, 300, 40, NULL, '2024-06-11 03:01:13', '2024-06-11 03:01:13');

-- --------------------------------------------------------

--
-- Table structure for table `bcs_images`
--

CREATE TABLE `bcs_images` (
  `id` int(11) NOT NULL,
  `bcs_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bcs_images`
--

INSERT INTO `bcs_images` (`id`, `bcs_id`, `image_path`) VALUES
(1, 1, 'uploads/bcs/8/1/1/KqSeAUZpovNE0.jpeg'),
(2, 1, 'uploads/bcs/8/1/1/2aHVfrPoMcIY0.jpeg'),
(3, 1, 'bcs/8/1/1/2OHiJT9WEtN10.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `cage`
--

CREATE TABLE `cage` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cage`
--

INSERT INTO `cage` (`id`, `user_id`, `name`, `location`, `description`, `created_at`, `updated_at`) VALUES
(1, 8, 'Kandang le', 'Cilebut', 'After edit', '2024-06-10 15:06:32', '2024-06-10 15:08:15'),
(3, 8, 'Kandang 2', 'Dramaga', 'this is example of cage description', '2024-06-10 15:06:32', '2024-06-10 15:08:15'),
(4, 8, 'Kandang sa', 'Dramaga', 'this is example of cage description', '2024-06-10 15:12:33', '2024-06-10 15:12:33'),
(5, 8, 'Kandang sa', 'Dramaga', 'this is example of cage description', '2024-06-10 15:14:32', '2024-06-10 15:14:32'),
(6, 8, 'Kandang saya kecil', 'Dramaga', 'this is example of cage description', '2024-06-10 15:19:55', '2024-06-10 15:19:55'),
(7, 8, 'Kandang saya uhuy', 'Dramaga', 'this is example of cage description', '2024-06-10 15:21:36', '2024-06-10 15:21:36'),
(9, 8, 'Kandang saya siapa', 'Dramaga', 'this is example of cage description', '2024-06-10 15:25:46', '2024-06-10 15:25:46'),
(10, 8, 'Kandang 11', 'Dramaga', 'this is example of cage description', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(11, 8, 'Kandang lele', 'Cilebut', 'After edit', '2024-06-10 15:31:41', '2024-06-10 15:35:54');

-- --------------------------------------------------------

--
-- Table structure for table `livestock`
--

CREATE TABLE `livestock` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `eid` bigint(11) DEFAULT NULL,
  `vid` varchar(10) NOT NULL,
  `cage_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `age` int(11) NOT NULL,
  `gender` enum('Jantan','Betina') NOT NULL,
  `type_of_livestock` enum('Kambing','Sapi') NOT NULL,
  `breed_of_livestock` enum('Madura','Bali','Limousin','Brahman') NOT NULL,
  `purpose` enum('Indukan','Penggemukan','Tabungan','Belum Tahu') NOT NULL,
  `maintenance` enum('Kandang','Gembala','Campuran') NOT NULL,
  `source` enum('Sejak Lahir','Bantuan Pemerintah','Beli','Beli dari Luar Kelompok','Beli dari Dalam Kelompok','Inseminasi Buatan','Kawin Alam','Tidak Tahu') NOT NULL,
  `ownership_status` enum('Sendiri','Kelompok','Titipan') NOT NULL,
  `reproduction` enum('Tidak Bunting','Bunting < 1 bulan','Bunting 1 bulan','Bunting 2 bulan','Bunting 3 bulan','Bunting 4 bulan','Bunting 5 bulan','Bunting 6 bulan','Bunting 7 bulan','Bunting 8 bulan','Bunting 9 bulan','Bunting 10 bulan','Bunting 11 bulan','Bunting > 11 bulan') NOT NULL,
  `chest_size` double NOT NULL,
  `body_weight` double NOT NULL,
  `health` enum('Sehat','Sakit') NOT NULL,
  `livestock_image` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `livestock`
--

INSERT INTO `livestock` (`id`, `user_id`, `eid`, `vid`, `cage_id`, `name`, `birthdate`, `age`, `gender`, `type_of_livestock`, `breed_of_livestock`, `purpose`, `maintenance`, `source`, `ownership_status`, `reproduction`, `chest_size`, `body_weight`, `health`, `livestock_image`, `created_at`, `updated_at`) VALUES
(1, 8, NULL, 'WPU4689', 1, 'Sapi Uhuy', '2024-06-09', 2, 'Jantan', 'Sapi', 'Bali', 'Penggemukan', 'Kandang', 'Sejak Lahir', 'Sendiri', 'Bunting < 1 bulan', 100, 2100, '', NULL, '2024-06-09 09:59:39', '2024-06-09 13:55:25'),
(3, 8, NULL, 'FKU2552', 3, 'Sapi Gokil', '2024-06-09', 2, 'Jantan', 'Sapi', 'Bali', 'Penggemukan', 'Kandang', 'Sejak Lahir', 'Sendiri', 'Bunting < 1 bulan', 300, 200, '', NULL, '2024-06-10 16:01:16', '2024-06-11 03:01:13'),
(4, 8, NULL, 'FAM7355', 3, 'Sapi Gendeng', '2024-06-09', 2, 'Jantan', 'Sapi', 'Bali', 'Penggemukan', 'Kandang', 'Sejak Lahir', 'Sendiri', 'Bunting < 1 bulan', 120, 300, 'Sehat', NULL, '2024-06-10 16:26:35', '2024-06-10 16:26:35');

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
(1, 1, 'livestock/8/1/HBIhOA3xgy0X0.jpeg'),
(2, 1, 'livestock/8/1/PuWZKewWUYEO0.jpeg');

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
  `location` varchar(255) NOT NULL,
  `livestock_feed` varchar(255) NOT NULL,
  `feed_weight` double NOT NULL,
  `vitamin` varchar(45) DEFAULT NULL,
  `costs` double NOT NULL,
  `details` text DEFAULT NULL,
  `documentation` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `note`
--

INSERT INTO `note` (`id`, `user_id`, `livestock_name`, `livestock_id`, `livestock_vid`, `livestock_cage`, `location`, `livestock_feed`, `feed_weight`, `vitamin`, `costs`, `details`, `documentation`, `created_at`, `updated_at`) VALUES
(1, 8, 'Sapi Uhuy', 1, 'WPU4689', 'Kandang le', 'Dramaga', 'Rumput lapangan', 1, 'BIOBIGMAX Probiotik', 50000, 'Sapi dalam kondisi sehat dengan berat badan stabil. Tidak ada tanda-tanda penyakit atau stres. ', NULL, '2024-06-09 15:03:09', '2024-06-09 15:03:09'),
(3, 8, 'Sapi Uhuy', 1, 'WPU4689', 'Kandang le', 'Cilebut', 'Rumput lapangan', 1, 'BIOBIGMAX Probiotik', 50000, 'Sapi dalam kondisi sehat dengan berat badan stabil. Tidak ada tanda-tanda penyakit atau stres.', NULL, '2024-06-10 16:15:02', '2024-06-10 16:15:02');

-- --------------------------------------------------------

--
-- Table structure for table `note_images`
--

CREATE TABLE `note_images` (
  `id` int(11) NOT NULL,
  `note_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `note_images`
--

INSERT INTO `note_images` (`id`, `note_id`, `image_path`) VALUES
(1, 1, 'notes/8/1/1/BRQpE05Xlt1M0.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
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
  `is_completed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `gender`, `nik`, `full_name`, `birthdate`, `phone_number`, `address`, `auth_key`, `password_hash`, `password_reset_token`, `verification_token`, `status`, `created_at`, `updated_at`, `is_completed`) VALUES
(1, 'konox', 'konox120@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$n84g2u61C/tPKoX6Yd.W3OE/oRfUAbkQv3mKS3MXElddtwliFQRte', NULL, NULL, 10, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(2, 'wowo', 'wowo@example.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$1LjOAdwwgOnuc2bgxVmypOuMHFiuT29XYasbUt6Aj4CbuB9Ypm5P6', NULL, NULL, 10, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(3, 'uhuy', 'uhuy123@example.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$nK.bFo4dT7EL.tOHHGW7sevJWitCMVVhWBX8XRZf1tH6I54JMhxrK', NULL, NULL, 10, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0),
(4, 'wokwok', 'wokwok@example.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$13$tqu9YnnAdyenuc4NTEqfOuBX4RUhM8u9yu8wN/0pGOSQHiYqf/6ia', NULL, 'c24zRWhXbXg0ZDJ1UUZBajcwUGRhbWRQMVB3YndNQ2s6MTcxNzkwNzYzMzoxNzE3OTA3NjM4', 10, '2024-06-09 04:33:53', '2024-06-09 04:33:53', 0),
(8, 'lele', 'hahay@example.com', 'Laki-laki', '4321567894523412', 'ale ula', '2024-06-09', '081234544444', 'Jalan Cendrawasih No. 25, RT 03/RW 05, Kelurahan Mulyorejo, Kecamatan Sukomanunggal, Kota Surabaya, Jawa Timur, 60112', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjgsInVzZXJuYW1lIjoibGVsZSIsImlhdCI6MTcyMTAzNTg4NiwiZXhwIjoxNzIxMDM5NDg2fQ.KrWyJIsJv7UevH5kaplEj-PrvPiVffUotXcatnT8uhs', '$2y$13$wg5NJBecr/0NgTeJGF.gHOY/5d5KbVpiVC2o78TsKcpb21xpZkKqe', NULL, NULL, 10, '2024-06-09 06:24:54', '2024-07-15 09:31:26', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bcs`
--
ALTER TABLE `bcs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `livestock_idx` (`livestock_id`);

--
-- Indexes for table `bcs_images`
--
ALTER TABLE `bcs_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bcs_idx` (`bcs_id`);

--
-- Indexes for table `cage`
--
ALTER TABLE `cage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_idx` (`user_id`);

--
-- Indexes for table `livestock`
--
ALTER TABLE `livestock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vid` (`vid`),
  ADD UNIQUE KEY `eid` (`eid`),
  ADD KEY `cage_idx` (`cage_id`),
  ADD KEY `user_idx` (`user_id`);

--
-- Indexes for table `livestock_images`
--
ALTER TABLE `livestock_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `livestock_idx` (`livestock_id`);

--
-- Indexes for table `note`
--
ALTER TABLE `note`
  ADD PRIMARY KEY (`id`),
  ADD KEY `livestock_idx` (`livestock_id`),
  ADD KEY `user_idx` (`user_id`),
  ADD KEY `livestock_vid_idx` (`livestock_vid`);

--
-- Indexes for table `note_images`
--
ALTER TABLE `note_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `note_idx` (`note_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bcs`
--
ALTER TABLE `bcs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `bcs_images`
--
ALTER TABLE `bcs_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cage`
--
ALTER TABLE `cage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `livestock`
--
ALTER TABLE `livestock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `livestock_images`
--
ALTER TABLE `livestock_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `note`
--
ALTER TABLE `note`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `note_images`
--
ALTER TABLE `note_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bcs`
--
ALTER TABLE `bcs`
  ADD CONSTRAINT `fk_livestock_bcs` FOREIGN KEY (`livestock_id`) REFERENCES `livestock` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `bcs_images`
--
ALTER TABLE `bcs_images`
  ADD CONSTRAINT `fk_bcs_images` FOREIGN KEY (`bcs_id`) REFERENCES `bcs` (`id`);

--
-- Constraints for table `cage`
--
ALTER TABLE `cage`
  ADD CONSTRAINT `fk_user_cage` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `livestock`
--
ALTER TABLE `livestock`
  ADD CONSTRAINT `fk_cage_livestock` FOREIGN KEY (`cage_id`) REFERENCES `cage` (`id`),
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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
