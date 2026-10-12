-- Public import schema derived from dishub.sql supplied by the project owner.
-- Import into a NEW, empty database. No existing tables are dropped.
-- Original accounts, password hashes, personal records and operational data are omitted.

SET NAMES utf8mb4;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `numpang_uji` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `masa_berlaku` date DEFAULT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `nomor_uji` varchar(100) DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `foto_depan` varchar(255) DEFAULT NULL,
  `foto_kanan` varchar(255) DEFAULT NULL,
  `foto_kiri` varchar(255) DEFAULT NULL,
  `foto_belakang` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `numpang_uji`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `numpang_uji`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


