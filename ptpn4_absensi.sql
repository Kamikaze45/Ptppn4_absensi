-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2026 at 05:23 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ptpn4_absensi`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `tipe_absensi` enum('WFO','WFH','Dinas Luar') NOT NULL DEFAULT 'WFO',
  `check_in` time DEFAULT NULL,
  `check_in_lat` decimal(10,8) DEFAULT NULL,
  `check_in_lng` decimal(11,8) DEFAULT NULL,
  `check_in_alamat` varchar(255) DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `check_out_lat` decimal(10,8) DEFAULT NULL,
  `check_out_lng` decimal(11,8) DEFAULT NULL,
  `check_out_alamat` varchar(255) DEFAULT NULL,
  `status` enum('hadir','terlambat','alpha','izin','sakit') NOT NULL DEFAULT 'hadir',
  `status_masuk` varchar(255) DEFAULT NULL,
  `tipe_kerja` enum('wfo','wfh','dinas') DEFAULT 'wfo',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `user_id`, `tanggal`, `tipe_absensi`, `check_in`, `check_in_lat`, `check_in_lng`, `check_in_alamat`, `check_out`, `check_out_lat`, `check_out_lng`, `check_out_alamat`, `status`, `status_masuk`, `tipe_kerja`, `keterangan`, `created_at`, `updated_at`) VALUES
(5, 24, '2026-06-02', 'WFO', '09:24:27', -5.07297857, 105.13491257, 'Jalan Bangunrejo - Wates, Sinar Banten/bekri, Lampung Tengah, Lampung, Sumatra, Indonesia', NULL, NULL, NULL, NULL, 'terlambat', NULL, 'wfo', NULL, '2026-06-02 02:24:28', '2026-06-02 02:24:28'),
(6, 27, '2026-06-24', 'WFH', '16:31:59', -5.35599914, 105.23325465, NULL, '16:32:25', -5.35596676, 105.23323501, NULL, 'hadir', 'Terlambat', 'wfo', NULL, '2026-06-24 09:31:59', '2026-06-24 09:32:25'),
(7, 24, '2026-06-24', 'Dinas Luar', '16:36:16', -5.35595730, 105.23328283, NULL, '16:37:59', -5.35595499, 105.23329391, NULL, 'hadir', 'Terlambat', 'wfo', NULL, '2026-06-24 09:36:16', '2026-06-24 09:37:59'),
(8, 27, '2026-06-25', 'WFH', '08:26:18', -5.35593286, 105.23328405, NULL, NULL, NULL, NULL, NULL, 'hadir', 'Terlambat', 'wfo', NULL, '2026-06-25 01:26:18', '2026-06-25 01:26:18'),
(9, 27, '2026-06-26', 'Dinas Luar', '14:14:08', -5.35595709, 105.23327127, NULL, '16:39:39', -5.35597440, 105.23327218, NULL, 'hadir', 'Terlambat', 'wfo', NULL, '2026-06-26 07:14:08', '2026-06-26 09:39:39'),
(10, 27, '2026-06-28', 'Dinas Luar', '16:57:11', -5.35604175, 105.23324531, NULL, '16:58:29', -5.35607790, 105.23321548, NULL, 'hadir', 'Terlambat', 'wfo', NULL, '2026-06-28 09:57:11', '2026-06-28 09:58:29'),
(11, 27, '2026-06-30', 'WFH', '10:18:30', -5.35600711, 105.23319799, NULL, '10:19:10', -5.35601854, 105.23316519, NULL, 'hadir', 'Terlambat', 'wfo', NULL, '2026-06-30 03:18:30', '2026-06-30 03:19:10'),
(12, 24, '2026-06-30', 'Dinas Luar', '10:58:43', -5.35600563, 105.23319168, NULL, '10:58:54', -5.35611210, 105.23316141, NULL, 'hadir', 'Terlambat', 'wfo', NULL, '2026-06-30 03:58:43', '2026-06-30 03:58:54'),
(13, 27, '2026-07-01', 'Dinas Luar', '10:47:09', -5.35595907, 105.23331336, NULL, NULL, NULL, NULL, NULL, 'hadir', 'Terlambat', 'wfo', NULL, '2026-07-01 03:47:09', '2026-07-01 03:47:09'),
(14, 27, '2026-07-03', 'Dinas Luar', '19:34:52', -5.35613212, 105.23313345, NULL, NULL, NULL, NULL, NULL, 'hadir', 'Terlambat', 'wfo', NULL, '2026-07-03 12:34:52', '2026-07-03 12:34:52'),
(15, 27, '2026-07-04', 'WFH', '08:23:56', -5.35699600, 105.23246000, NULL, '18:55:50', -5.35596147, 105.23330168, 'Gedong Meneng, Lampung Selatan, Lampung, Sumatera, 35143, Indonesia', 'hadir', 'Terlambat', 'wfo', NULL, '2026-07-04 01:23:56', '2026-07-04 11:55:53'),
(16, 28, '2026-07-04', 'Dinas Luar', '10:16:11', -5.35699600, 105.23246000, 'Polinela, Gedong Meneng, Lampung Selatan, Lampung, Sumatera, 35144, Indonesia', '11:44:28', -5.35699600, 105.23246000, 'Polinela, Gedong Meneng, Lampung Selatan, Lampung, Sumatera, 35144, Indonesia', 'hadir', 'Terlambat', 'wfo', NULL, '2026-07-04 03:16:12', '2026-07-04 04:44:29'),
(17, 27, '2026-07-05', 'WFH', '18:21:36', -5.35699600, 105.23246000, 'Polinela, Gedong Meneng, Lampung Selatan, Lampung, Sumatera, 35144, Indonesia', '23:12:53', -5.35699600, 105.23246000, 'Polinela, Gedong Meneng, Lampung Selatan, Lampung, Sumatera, 35144, Indonesia', 'hadir', 'Terlambat', 'wfo', NULL, '2026-07-05 11:21:40', '2026-07-05 16:12:54'),
(18, 27, '2026-07-06', 'WFH', '08:16:31', -5.35699600, 105.23246000, 'Polinela, Gedong Meneng, Lampung Selatan, Lampung, Sumatera, 35144, Indonesia', NULL, NULL, NULL, NULL, 'hadir', 'Terlambat', 'wfo', NULL, '2026-07-06 01:16:33', '2026-07-06 01:16:33'),
(19, 28, '2026-07-06', 'Dinas Luar', '08:29:47', -5.35699600, 105.23246000, 'Polinela, Gedong Meneng, Lampung Selatan, Lampung, Sumatera, 35144, Indonesia', NULL, NULL, NULL, NULL, 'hadir', NULL, 'wfo', NULL, '2026-07-06 01:29:48', '2026-07-06 01:29:48'),
(20, 6, '2026-07-06', 'WFH', '08:42:53', -5.35699600, 105.23246000, 'Polinela, Gedong Meneng, Lampung Selatan, Lampung, Sumatera, 35144, Indonesia', NULL, NULL, NULL, NULL, 'hadir', NULL, 'wfo', NULL, '2026-07-06 01:42:54', '2026-07-06 01:42:54'),
(21, 3, '2026-07-06', 'Dinas Luar', '08:49:09', -5.35699600, 105.23246000, 'Polinela, Gedong Meneng, Lampung Selatan, Lampung, Sumatera, 35144, Indonesia', NULL, NULL, NULL, NULL, 'hadir', 'Hadir', 'wfo', NULL, '2026-07-06 01:49:10', '2026-07-06 01:49:10'),
(22, 4, '2026-07-06', 'Dinas Luar', '08:58:07', -5.35699600, 105.23246000, 'Polinela, Gedong Meneng, Lampung Selatan, Lampung, Sumatera, 35144, Indonesia', NULL, NULL, NULL, NULL, 'hadir', 'Terlambat', 'wfo', NULL, '2026-07-06 01:58:08', '2026-07-06 01:58:08');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_settings`
--

CREATE TABLE `attendance_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance_settings`
--

INSERT INTO `attendance_settings` (`id`, `key`, `value`, `description`, `created_at`, `updated_at`) VALUES
(1, 'check_in_monday', '07:00', 'Jam masuk Senin', '2026-04-30 22:39:39', '2026-04-30 22:39:39'),
(2, 'check_in_tuesday', '08:00', 'Jam masuk Selasa', '2026-04-30 22:39:39', '2026-04-30 22:39:39'),
(3, 'check_in_wednesday', '08:00', 'Jam masuk Rabu', '2026-04-30 22:39:39', '2026-04-30 22:39:39'),
(4, 'check_in_thursday', '08:00', 'Jam masuk Kamis', '2026-04-30 22:39:39', '2026-04-30 22:39:39'),
(5, 'check_in_friday', '07:30', 'Jam masuk Jumat', '2026-04-30 22:39:39', '2026-04-30 22:39:39'),
(6, 'check_in_saturday', '07:30', 'Jam masuk Sabtu', '2026-04-30 22:39:39', '2026-04-30 22:39:39'),
(7, 'check_out_monday', '16:00', 'Jam pulang Senin', '2026-04-30 22:39:39', '2026-04-30 22:39:39'),
(8, 'check_out_tuesday', '16:00', 'Jam pulang Selasa', '2026-04-30 22:39:39', '2026-04-30 22:39:39'),
(9, 'check_out_wednesday', '16:00', 'Jam pulang Rabu', '2026-04-30 22:39:39', '2026-04-30 22:39:39'),
(10, 'check_out_thursday', '16:00', 'Jam pulang Kamis', '2026-04-30 22:39:39', '2026-04-30 22:39:39'),
(11, 'check_out_friday', '16:00', 'Jam pulang Jumat', '2026-04-30 22:39:39', '2026-04-30 22:39:39'),
(12, 'check_out_saturday', '13:00', 'Jam pulang Sabtu', '2026-04-30 22:39:39', '2026-04-30 22:39:39'),
(13, 'late_tolerance_minutes', '60', 'Batas absen setelah jam masuk (menit), lewat = terlambat', '2026-04-30 22:39:39', '2026-04-30 22:39:39'),
(14, 'reminder_before_minutes', '60', 'Notif WA dikirim X menit sebelum jam masuk', '2026-04-30 22:39:39', '2026-04-30 22:39:39'),
(15, 'office_lat', '-5.073259', 'Latitude kantor', '2026-04-30 22:39:39', '2026-04-30 22:39:39'),
(16, 'office_lng', '105.135010', 'Longitude kantor', '2026-04-30 22:39:39', '2026-04-30 22:39:39'),
(17, 'office_radius_meter', '100', 'Radius maksimal absensi (meter)', '2026-04-30 22:39:39', '2026-04-30 22:39:39');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_01_01_000001_create_users_table', 1),
(2, '2024_01_01_000002_create_absensi_table', 1),
(3, '2026_03_09_000001_create_pengajuan_izin_table', 1),
(4, '2026_05_01_022936_create_attendance_settings_table', 1),
(5, '2026_05_01_103535_create_jobs_table', 2),
(6, 'create_reminder_logs_table', 2),
(7, '2026_05_01_111550_create_failed_jobs_table', 3),
(8, '2026_05_23_172724_change_jabatan_column_to_varchar', 4),
(9, '2026_06_23_000001_add_manajer_to_users_role', 5),
(10, '2026_06_23_100000_add_tipe_absensi_to_absensi_table', 6),
(11, '2026_06_24_162817_add_status_masuk_to_absensis_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_izin`
--

CREATE TABLE `pengajuan_izin` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `tipe` enum('cuti','izin','sakit') NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `alasan` text NOT NULL,
  `file_lampiran` varchar(255) DEFAULT NULL,
  `status` enum('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `catatan_admin` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengajuan_izin`
--

INSERT INTO `pengajuan_izin` (`id`, `user_id`, `tipe`, `tanggal_mulai`, `tanggal_selesai`, `alasan`, `file_lampiran`, `status`, `catatan_admin`, `created_at`, `updated_at`) VALUES
(1, 27, 'cuti', '2026-06-26', '2026-06-29', 'liburan', 'lampiran-izin/sQpW61V8xFZleHjBaAKj0QWBVSqpBj7GfcaevEUm.png', 'disetujui', NULL, '2026-06-25 04:40:43', '2026-06-30 02:04:04'),
(2, 27, 'izin', '2026-06-26', '2026-07-03', 'izin kepentingan  keluarga', 'lampiran-izin/1MDhBgTJq13FoODNGej5CBXhHEPcNSW8gHWd3DyR.pdf', 'menunggu', NULL, '2026-06-25 04:58:53', '2026-06-25 04:58:53'),
(3, 27, 'sakit', '2026-06-26', '2026-06-27', 'sakit', 'lampiran-izin/FB5KDrJYddIGSEGrglvTwxqVh79KyohRe24P9LfP.png', 'disetujui', NULL, '2026-06-25 05:02:24', '2026-06-30 02:11:23'),
(8, 27, 'cuti', '2026-07-04', '2026-07-11', 'liburann', 'lampiran-izin/wFbWDCcbx7y0WUY5CXf9jj4faeFg5jzDuqcpJxgu.jpg', 'disetujui', NULL, '2026-06-30 02:16:51', '2026-07-05 11:56:37'),
(9, 28, 'cuti', '2026-07-25', '2026-08-01', 'libur', 'lampiran-izin/L8ww0t4KXJqCbopYRac81oPRAzBdn673vsQoyJjM.png', 'menunggu', NULL, '2026-07-04 04:44:11', '2026-07-04 04:44:11'),
(10, 27, 'izin', '2026-07-04', '2026-07-11', 'liburan', 'lampiran-izin/MLuJQIVgTcxWrfChGOypEgmnzYpFWQgc9yr0PXJa.png', 'ditolak', 'yy', '2026-07-04 07:40:01', '2026-07-05 18:14:52'),
(11, 27, 'izin', '2026-07-05', '2026-07-09', 'izijnnnn', 'lampiran-izin/lb0MYx2bRJ3db7KNCcLFU12RGHMm0N6aNynaGLeM.png', 'menunggu', NULL, '2026-07-04 11:21:40', '2026-07-04 11:21:40'),
(13, 27, 'sakit', '2026-07-18', '2026-07-19', '111', 'lampiran-izin/7nfWlqyH1oWEfkQ5k3szNAXnYaO9lEgWtPqTJEfz.png', 'disetujui', NULL, '2026-07-04 11:56:26', '2026-07-05 11:56:14'),
(14, 27, 'cuti', '2026-07-11', '2026-07-12', 'yyy', 'lampiran-izin/vXNaMPur0iTCfkvRQbXzo2tZC1BT7qB7W8Te7mKG.png', 'disetujui', NULL, '2026-07-05 18:13:04', '2026-07-05 18:14:23'),
(15, 28, 'cuti', '2026-07-07', '2026-07-09', '4444', 'lampiran-izin/TCigy0rKr2NAciLeChVjs0xoJ3fODl4BFTLk9YY4.png', 'disetujui', NULL, '2026-07-05 22:49:46', '2026-07-05 22:51:48');

-- --------------------------------------------------------

--
-- Table structure for table `reminder_logs`
--

CREATE TABLE `reminder_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('masuk','pulang') NOT NULL,
  `phone` varchar(20) NOT NULL,
  `status` enum('sent','failed') NOT NULL DEFAULT 'failed',
  `message_id` varchar(255) DEFAULT NULL,
  `error` text DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `nip` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manajer','user') NOT NULL DEFAULT 'user',
  `jabatan` varchar(100) DEFAULT NULL,
  `divisi` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `nip`, `email`, `password`, `role`, `jabatan`, `divisi`, `phone`, `photo`, `alamat`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'ADM001', 'admin@ptpn.ac.id', '$2y$12$zToSnPv4C.WpTaysh.cGbOJ8C.gsRS0R3jWIkCAI97G3RTxYWM2aa', 'admin', '', 'IT', '081100000011', 'uploads/photos/foto_6a4b1b14156824.58628385.jpg', 'Kecamatan Bekri, Lampung Tengah', 1, NULL, '2026-04-30 22:47:57', '2026-07-06 03:04:01'),
(3, 'BUDI INDRA IRAWAN', '7000124', 'budi@ptpn4.co.id', '$2y$12$0P61SX/AWLb0XwQ5ZrmJXOZlUdAUTcgA4OeH9feNspuzB.SH3MKRG', 'user', 'Staff Keuangan', 'Umum', '08127948193', NULL, 'Kecamatan Bekri, Lampung Tengah', 1, NULL, '2026-05-23 10:37:21', '2026-07-06 02:33:42'),
(4, 'DARIYAH', '7000125', 'dariyah@ptpn4.co.id', '$2y$12$FNW09zxeAXLg7q3U3MQb5ed2a.ck.hktEB6ME8PuqQL.NA2l2LC7.', 'user', 'Staff Keuangan', 'Umum', '08124437155', NULL, 'Kecamatan Bekri, Lampung Tengah', 1, NULL, '2026-05-23 10:37:22', '2026-07-06 02:34:38'),
(5, 'FEDRO ABELIO', '7000126', 'fedro@ptpn4.co.id', '$2y$12$bpPojBxH929H/mhnOYCnLumKulwxqlSvVjQLXMamV9CAGYkpKp/oq', 'user', 'Security', 'Umum', '08124027983', NULL, 'Kecamatan Bekri, Lampung Tengah', 1, NULL, '2026-05-23 10:37:22', '2026-07-06 02:49:51'),
(6, 'ATMINI MURHARYANTI', '7000127', 'atmini@ptpn4.co.id', '$2y$12$x9vcIsBhx8LPJo6cxY/SrOnW7CPr1k8e7XnfYPRH9svt0hOHDRyDW', 'user', 'Staff Administrasi', 'Umum', '08125909998', NULL, 'Kecamatan Bekri, Lampung Tengah', 1, NULL, '2026-05-23 10:37:22', '2026-07-06 02:32:13'),
(7, 'ANDRI PUJIARTO', '7000128', 'andri.p@company.com', '$2y$12$JXyRU40VymUzXaAic9WJYOZwS4LwFn0dkdePBcYEWbegvQ69isfG2', 'user', 'Staff Umum', 'Umum', '08129994816', NULL, 'Lampung', 1, NULL, '2026-05-23 10:37:23', '2026-05-23 10:37:23'),
(8, 'ANGGIAT N PANJAITAN', '7000129', 'anggiat@ptpn4.co.id', '$2y$12$BK4KA4Eaoyf09Lj6tzuD9uVpFyo8t6n5XYJVpNpYQpa22.4PaFWHS', 'user', 'Staff Administrasi', 'Umum', '08121700649', NULL, 'Kecamatan Bekri, Lampung Tengah', 0, NULL, '2026-05-23 10:37:23', '2026-07-14 00:35:09'),
(11, 'KHOLDUN KHOLIK', '7000132', 'Kholdun@ptpn4.co.id', '$2y$12$nIf2bfESjo3rEPYDTfsYTOrVIVeUfjGk3I6yXIsnoXL5YwgxaVsDu', 'user', 'Staff Administrasi', 'Umum', '08126853232', NULL, 'Kecamatan Bekri, Lampung Tengah', 1, NULL, '2026-05-23 10:37:24', '2026-07-06 02:52:11'),
(12, 'PARMIN', '7000133', 'parmin@ptpn4.co.id', '$2y$12$NPtqoDKEOR3I4egpAucjL.Ua18eZJIH5f2wQ5rJZsPedNYzvyVZ/W', 'user', 'Staff Operasional', 'Teknik', '08121665617', NULL, 'Kecamatan Goras Jaya, Lampung Tengah', 1, NULL, '2026-05-23 10:37:25', '2026-07-06 02:57:45'),
(13, 'NGADINO', '7000134', 'ngadino@ptpn4.co.id', '$2y$12$IzMt5fauV0i31ErphT0lRufBxxWYHUTYof7lpsasmQg5Lr2o6LH2u', 'user', 'Staff Operasional', 'Teknik', '08128517576', NULL, 'Kecamatan Bekri, Lampung Tengah', 1, NULL, '2026-05-23 10:37:25', '2026-07-06 02:53:54'),
(14, 'RINALDY PRATAMA P', '7000135', 'rinaldy@ptpn4.co.id', '$2y$12$iekP3/rURwE3AxKw/6hsUO8AMMpNvWA./Lnyx5XasFk3owYqwLI82', 'user', 'Staff Operasional', 'Teknik', '08121069350', NULL, 'Lampung Tengah', 1, NULL, '2026-05-23 10:37:26', '2026-07-06 02:58:40'),
(16, 'SRI DEWI NINGSIH', '7000137', 'sridewi@ptpn4.co.id', '$2y$12$GM53cKHIiZxu2d46MAlN9el.b7JdVcKBXBTnMoqNYHmOMibl18d9u', 'user', 'Staff Operasional', 'Pengolahan', '08129978826', NULL, 'Kecamatan Goras Jaya, Lampung Tengah', 1, NULL, '2026-05-23 10:37:26', '2026-07-06 02:59:50'),
(17, 'DESTRI I ASTUTI', '7000138', 'destri@ptpn4.co.id', '$2y$12$mxspfsDMd8.0bD6qzqfAuuQ4arIDAQ2Yyvxrj.djb4QU6ZKd/9Euu', 'user', 'Staff Operasional', 'Puskes', '08129107978', NULL, 'Kecamatan Bekri, Lampung Tengah', 1, NULL, '2026-05-23 10:37:27', '2026-07-06 02:48:59'),
(18, 'NUGRAHA DESWANTO', '7000139', 'nugraha@ptpn4.co.id', '$2y$12$gpxfXrdbXIYY.dueUn0RZ.FT56cOB3GZQIVD4ezodtdOI99635Ye.', 'user', 'Staff Operasional', 'Puskes', '08128299640', NULL, 'Kecamatan Bekri, Lampung Tengah', 1, NULL, '2026-05-23 10:37:27', '2026-07-06 02:54:55'),
(21, 'TRIHANDOKO', '7000136', 'trihandoko@ptpn4.co.id', '$2y$12$3a5p9tLMoH7Vd.Sd87FOAu8IFhyWxxpkLBN8S0o8PcCX2Fup8H6b6', 'user', 'Staff Operasional', 'Teknik', '08127539437', NULL, 'Lampung Tengah', 1, NULL, '2026-05-23 10:44:01', '2026-07-06 03:01:17'),
(22, 'MUKTI ALI', '7000199', 'ali@ptpn4.co.id', '$2y$12$t7rBmFpiD9nS3vh/a8ywZeVBe9pR0VwGWDEF2gjHeAtB2whA01j2K', 'user', 'Kepala Bagian', 'Umum', '082323232332', NULL, 'Lampung Tengah, Anak Tuha, Haji Pemanggilan', 1, NULL, '2026-06-02 01:58:47', '2026-07-06 02:52:52'),
(23, 'AMANDA PERTIWI', '7000198', 'tiwi@ptpn4.co.id', '$2y$12$Mz70nptdXhomW4vOfZmhKetHlKspkM.wIojMZM4R51uxThzGJUBxa', 'user', 'Staff Administrasi', 'Umum', '082323232332', NULL, 'Kecamatan Bekri, Lampung Tengah', 1, NULL, '2026-06-02 02:01:07', '2026-07-06 02:44:45'),
(24, 'ADI KURNIAWAN', '7000197', 'adi@ptpn4.co.id', '$2y$12$4usDPNonkTGqfPDpoNoAJO8wU61D/96kNAiGTsxFi2n3cY8MNz.Zm', 'user', 'Staff Keuangan', 'Umum', '082323232332', 'photos/evOcv3kv1iPawu0hvwUeyCquvg6EQfrNBxfCX7AN.jpg', 'Lampung Tengah, Anak Tuha, Haji Pemanggilan', 1, NULL, '2026-06-02 02:02:09', '2026-07-06 02:44:57'),
(25, 'Manajer', '001', 'manajer@ptpn4.com', '$2y$12$WwT5XDvrWvlF8BwOtRxWGuMCscwnPgIiiIebOgegcj4YgQ0Ef3ET6', 'manajer', 'Manajer', 'Operasional', '081134432321', 'uploads/photos/foto_6a48effed8baf4.68772711.jpg', 'Kecamatan Bekri, Lampung Tengah', 1, 'R0tGjGxjhuYs4lCkwStYwLxpnAnNMc0SgqDE84FiKNIa8pgQ461iUSzPTiYe', '2026-06-23 04:15:56', '2026-07-06 02:27:19'),
(26, 'Admin', '002', 'admin@ptpn4.com', '$2y$12$MMPjV2qKbWHPXNDL/j/DpuYaUrE9vAu2vwgGInvwsD2C5gByWktAO', 'admin', 'Staff IT', 'IT', '085367251426', 'uploads/photos/foto_6a4b1aa93d6c68.06416712.jpg', 'rajabasa, bandar lampung', 1, NULL, '2026-06-23 04:15:57', '2026-07-06 03:02:01'),
(27, 'HENDRA', '7000123', 'pegawai@ptpn4.co.id', '$2y$12$R4H60y.BhlTzWffdPtROae1gLt5jYqb8eG5Ujx8HWZsa8l8U7i3Je', 'user', 'Staff IT', 'Umum', '081298071243', 'uploads/photos/foto_6a48b89099cb77.81369038.jpg', 'Kecamatan Bekri, Lampung Tengah', 1, NULL, '2026-06-23 04:15:57', '2026-07-06 02:47:12'),
(28, 'AZHAR', '7000999', 'azhar@ptpn.co.id', '$2y$12$0FB3grRFZOSudoMdSa9TLuIGhwPm7MqCbjywSe.oVQ05bEovC6mhy', 'user', 'Staff IT', 'Umum', '085326547786', 'uploads/photos/foto_6a4adf53402de7.20853307.jpg', 'Sumatra Utara', 0, NULL, '2026-06-30 17:44:53', '2026-07-14 00:50:25'),
(29, 'INDRA', '7000111', 'indra@ptpn4.co.id', '$2y$12$dUwodLsJ74gK2O3djR3BROVSzmzyHKkyqTzTKGn6UKOxHvzs9QKnK', 'user', 'Staff IT', 'Umum', '89876654532', NULL, 'Aji Pemanggilan, Lampung Tengah', 1, NULL, '2026-06-30 18:20:16', '2026-07-06 02:47:36'),
(30, 'DWI', '7000222', 'dwi@ptpn4.co.id', '$2y$12$Qw.diiF0GbmKqrrupuai6uAhC2P2K8G7LxJRGinCD1EdevysgH.Di', 'user', 'Staff IT', 'Umum', '089876654532', NULL, 'Mesuji, Lampung', 1, NULL, '2026-06-30 18:20:17', '2026-07-06 02:50:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `absensi_user_id_tanggal_unique` (`user_id`,`tanggal`);

--
-- Indexes for table `attendance_settings`
--
ALTER TABLE `attendance_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_settings_key_unique` (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengajuan_izin`
--
ALTER TABLE `pengajuan_izin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_izin_user_id_foreign` (`user_id`);

--
-- Indexes for table `reminder_logs`
--
ALTER TABLE `reminder_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reminder_logs_user_id_type_sent_at_index` (`user_id`,`type`,`sent_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_nip_unique` (`nip`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `attendance_settings`
--
ALTER TABLE `attendance_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pengajuan_izin`
--
ALTER TABLE `pengajuan_izin`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `reminder_logs`
--
ALTER TABLE `reminder_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengajuan_izin`
--
ALTER TABLE `pengajuan_izin`
  ADD CONSTRAINT `pengajuan_izin_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reminder_logs`
--
ALTER TABLE `reminder_logs`
  ADD CONSTRAINT `reminder_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
