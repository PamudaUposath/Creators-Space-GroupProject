-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2025 at 11:56 PM
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
-- Database: `creators_space`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','instructor','admin') DEFAULT 'user',
  `is_active` tinyint(1) DEFAULT 1,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `profile_image` varchar(500) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `remove` int(5) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `username`, `password_hash`, `role`, `is_active`, `reset_token`, `reset_expires`, `profile_image`, `skills`, `bio`, `phone`, `date_of_birth`, `created_at`, `updated_at`, `remove`) VALUES
(1, 'Admin', 'User', 'admin@creatorsspace.local', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-25 08:11:01', '2025-09-25 08:11:01', 0),
(2, 'John', 'Instructor', 'instructor@creatorsspace.local', 'instructor', '$2y$10$uNK/Z/zvG.nV7sP1vlknsuFKozLn0gQXksoRUBo604mwBByZQRDze', 'instructor', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-25 08:11:01', '2025-09-27 19:22:09', 0),
(14, 'Test', 'User', 'test@gmail.com', '', '$2y$10$6DZQgqLeUYWcBgn0.gHOWe/4nsOUJJFxUszD7bCACzSqWvlKG4dPq', 'user', 1, NULL, NULL, './assets/images/profiles/profile_14_1758833534.png', '', '', '', '0000-00-00', '2025-09-25 08:16:03', '2025-09-25 20:52:14', 0),
(15, 'Test', 'User2', 'testuser@gmail.com', NULL, '$2y$10$4bXShBAF7x7YOIu4u7gprenfZk.x9KMlMaTcLQ..WSxSUs/yFqZu.', 'instructor', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-26 21:23:49', '2025-09-26 21:47:10', 0),
(16, 'Test', 'User', 'test@example.com', 'testuser123', '$2y$10$nVGWBIfhFWuld4FwW2pTiOYEUSh.9q.SDGJtDbyMt.Uo6412NEs62', 'user', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-26 22:26:09', '2025-10-02 18:32:20', 0),
(17, 'Test', 'User', 'testuser3@gmail.com', NULL, '$2y$10$9FAt44CcARqOJXOFLXfhDuGEUIJ3uf82h7hUPEYpcRuKvxgtdjssW', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-26 22:27:47', '2025-09-26 22:27:47', 0),
(18, 'Alice', 'Johnson', 'alice.johnson@example.com', 'alice.johnson', '$2y$10$SVI/9UTIr9NEiTBjiDA7fuEh6GbytLRk3wUkTa9o9deJ.t.HfzTg.', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-17 05:32:27', '2025-09-28 05:32:27', 0),
(19, 'Bob', 'Smith', 'bob.smith@example.com', 'bob.smith', '$2y$10$eQSJ/zhSCm1UmljzWS0KyeeETUAGMKYPR3VIYXjpbVEDL2ecohEDK', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-17 05:32:27', '2025-09-28 05:32:27', 0),
(20, 'Carol', 'Davis', 'carol.davis@example.com', 'carol.davis', '$2y$10$.6ZwM1/.SkFy/CWDVzoeweic2wc8j1Mz//UDbyO/kRInmwThR.HyO', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-05 05:32:28', '2025-09-28 05:32:28', 0),
(21, 'David', 'Wilson', 'david.wilson@example.com', 'david.wilson', '$2y$10$rLTmrC.Zz1Kql6sAd3sTFeQThiBeOG5VWAYoSF8jlL1LR9vTBQsXm', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-07 05:32:28', '2025-09-28 05:32:28', 0),
(22, 'Emma', 'Brown', 'emma.brown@example.com', 'emma.brown', '$2y$10$8qiBsqgM9eBTXnpuSlyi8eQZp0juOevIbezJIkYWKQu583Qbsi142', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-19 05:32:28', '2025-09-28 05:32:28', 0),
(23, 'Frank', 'Miller', 'frank.miller@example.com', 'frank.miller', '$2y$10$J8g9NrJ9.UaspNi4DLxNCOp68OjJ.BBTrP5iHHoh6eA3Mc6NYwqSu', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-15 05:32:28', '2025-09-28 05:32:28', 0),
(24, 'Grace', 'Taylor', 'grace.taylor@example.com', 'grace.taylor', '$2y$10$IfI.icuoB6Oin7R4ibaqr.QXZtMoO1TEqkjc6edaT5DoMDcBWTvR2', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-20 05:32:29', '2025-09-28 05:32:29', 0),
(25, 'Henry', 'Anderson', 'henry.anderson@example.com', 'henry.anderson', '$2y$10$WYuxnmydDnrsrBgtplxRouLaVgMChXX0CX0j1Wr7IclXb3zXGVFfS', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-28 05:32:29', '2025-09-28 05:32:29', 0),
(26, 'Pamuda', 'U de A Goonatilake', 'pamuda@mailinator.com', NULL, '$2y$10$YthNfYeDj2vqZUKZYswD8eQ6APUfNJkOMNcYayj0DwKSsO83u5ToG', 'user', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-29 05:18:43', '2025-10-02 21:40:04', 0),
(29, 'Yohan', 'Bandara', 'yohan@mailinator.com', 'yohan', '$2y$10$ohXDKtDmGiW0TiWW7sIFVeJZmBcHcvIEZNIhxUZ/rgzAmTbHJICWG', 'instructor', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-02 21:53:46', '2025-10-02 21:54:42', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_username` (`username`),
  ADD KEY `idx_users_role` (`role`),
  ADD KEY `idx_users_active` (`is_active`),
  ADD KEY `idx_users_skills` (`skills`(768));

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
