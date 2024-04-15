-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 09, 2024 at 05:03 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `digiternak`
--
CREATE DATABASE IF NOT EXISTS `digiternak` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `digiternak`;

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
  `person_id` int(11) DEFAULT NULL,
  `name` varchar(10) NOT NULL,
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `person_id` int(11) DEFAULT NULL,
  `eid` bigint(11) NOT NULL,
  `vid` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `cage_id` int(11) DEFAULT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `livestock_images`
--

CREATE TABLE `livestock_images` (
  `id` int(11) NOT NULL,
  `livestock_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `note`
--

CREATE TABLE `note` (
  `id` int(11) NOT NULL,
  `livestock_vid` varchar(10) NOT NULL,
  `livestock_cage` varchar(10) NOT NULL,
  `date_recorded` date NOT NULL,
  `location` varchar(255) NOT NULL,
  `livestock_feed` varchar(255) NOT NULL,
  `costs` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `documentation` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `note_images`
--

CREATE TABLE `note_images` (
  `id` int(11) NOT NULL,
  `note_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `person`
--

CREATE TABLE `person` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nik` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `phone_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender_id` int(11) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `is_deleted` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `person_id` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `auth_key` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD KEY `fk_person_cage` (`person_id`);

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
  ADD UNIQUE KEY `eid` (`eid`),
  ADD UNIQUE KEY `vid` (`vid`),
  ADD KEY `fk_breed_of_livestock` (`breed_of_livestock_id`),
  ADD KEY `fk_maintenance` (`maintenance_id`),
  ADD KEY `fk_ownership_status` (`ownership_status_id`),
  ADD KEY `fk_person_livestock` (`person_id`),
  ADD KEY `fk_reproduction` (`reproduction_id`),
  ADD KEY `fk_source` (`source_id`),
  ADD KEY `fk_type_of_livestock` (`type_of_livestock_id`),
  ADD KEY `fk_cage_livestock` (`cage_id`);

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
  ADD KEY `fk_livestock_vid` (`livestock_vid`);

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
-- Indexes for table `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_person` (`user_id`),
  ADD KEY `nik` (`nik`) USING BTREE;

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
  ADD KEY `fk_person_user` (`person_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `livestock`
--
ALTER TABLE `livestock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `livestock_images`
--
ALTER TABLE `livestock_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `note`
--
ALTER TABLE `note`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `note_images`
--
ALTER TABLE `note_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `person`
--
ALTER TABLE `person`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cage`
--
ALTER TABLE `cage`
  ADD CONSTRAINT `fk_person_cage` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`);

--
-- Constraints for table `livestock`
--
ALTER TABLE `livestock`
  ADD CONSTRAINT `fk_breed_of_livestock` FOREIGN KEY (`breed_of_livestock_id`) REFERENCES `breed_of_livestock` (`id`),
  ADD CONSTRAINT `fk_cage_livestock` FOREIGN KEY (`cage_id`) REFERENCES `cage` (`id`),
  ADD CONSTRAINT `fk_maintenance` FOREIGN KEY (`maintenance_id`) REFERENCES `maintenance` (`id`),
  ADD CONSTRAINT `fk_ownership_status` FOREIGN KEY (`ownership_status_id`) REFERENCES `ownership_status` (`id`),
  ADD CONSTRAINT `fk_person_livestock` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `fk_reproduction` FOREIGN KEY (`reproduction_id`) REFERENCES `reproduction` (`id`),
  ADD CONSTRAINT `fk_source` FOREIGN KEY (`source_id`) REFERENCES `source` (`id`),
  ADD CONSTRAINT `fk_type_of_livestock` FOREIGN KEY (`type_of_livestock_id`) REFERENCES `type_of_livestock` (`id`);

--
-- Constraints for table `livestock_images`
--
ALTER TABLE `livestock_images`
  ADD CONSTRAINT `fk_livestock_images` FOREIGN KEY (`livestock_id`) REFERENCES `livestock` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `note`
--
ALTER TABLE `note`
  ADD CONSTRAINT `fk_livestock_vid` FOREIGN KEY (`livestock_vid`) REFERENCES `livestock` (`vid`);

--
-- Constraints for table `note_images`
--
ALTER TABLE `note_images`
  ADD CONSTRAINT `fk_note_images` FOREIGN KEY (`note_id`) REFERENCES `note` (`id`);

--
-- Constraints for table `person`
--
ALTER TABLE `person`
  ADD CONSTRAINT `fk_user_person` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_person_user` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`),
  ADD CONSTRAINT `fk_role_user` FOREIGN KEY (`role_id`) REFERENCES `user_role` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
