-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 17, 2025 at 03:48 AM
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
-- Database: `elytra`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','MasterAdmin') NOT NULL,
  `created_at` datetime NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password`, `role`, `created_at`, `fname`, `lname`) VALUES
(1, 'Admin@gmail.com', 'Admin123!', 'Admin', '2025-07-14 15:50:42', '', ''),
(2, 'MasterAdmin@gmail.com', 'P@$$w0rd!', 'MasterAdmin', '2025-07-14 17:03:33', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE `deposits` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `wallet` varchar(50) NOT NULL,
  `network` enum('USDT','BTC','ETH') NOT NULL,
  `wallet_address` varchar(255) NOT NULL,
  `amount` decimal(18,8) NOT NULL,
  `receipt` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `agreed` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deposits`
--

INSERT INTO `deposits` (`id`, `user_id`, `wallet`, `network`, `wallet_address`, `amount`, `receipt`, `status`, `agreed`, `created_at`, `updated_at`) VALUES
(1, 5, 'OKX', 'USDT', 'TLZTcjWXdP57hx4NCZ1SdMuNvPE61gvLUo', 366.00000000, 'uploads/receipts/6877566ad06ab.jpg', 'pending', 1, '2025-07-16 15:36:10', '2025-07-16 15:36:10'),
(2, 5, 'Binance', 'BTC', '12Pkd8jvQipC1kKAzWcUeEaFUWoBRMsTPR', 100000.00000000, 'uploads/receipts/6877585c8c873.jpg', 'pending', 1, '2025-07-16 15:44:28', '2025-07-16 15:44:28'),
(3, 5, 'OKX', 'USDT', 'TLZTcjWXdP57hx4NCZ1SdMuNvPE61gvLUo', 1231231.00000000, 'uploads/receipts/6877597c7a9df.jpg', 'pending', 1, '2025-07-16 15:49:16', '2025-07-16 15:49:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` enum('free','premium') NOT NULL,
  `created_at` datetime NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `about_me` text DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `wallet_address` varchar(255) DEFAULT NULL,
  `kyc_verified` tinyint(1) DEFAULT 0,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `wallet_balance` decimal(12,2) DEFAULT 0.00,
  `btc_balance` decimal(18,8) DEFAULT 0.00000000,
  `eth_balance` decimal(18,8) DEFAULT 0.00000000,
  `usdt_balance` decimal(18,2) DEFAULT 0.00,
  `eltr_balance` decimal(18,2) DEFAULT 0.00,
  `last_activity` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `type`, `created_at`, `first_name`, `last_name`, `birthday`, `about_me`, `profile_photo`, `wallet_address`, `kyc_verified`, `updated_at`, `wallet_balance`, `btc_balance`, `eth_balance`, `usdt_balance`, `eltr_balance`, `last_activity`) VALUES
(4, 'Nikki', 'Nikkicusay@gmail.com', 'N!kk!15!', 'free', '2025-07-14 23:18:19', 'Nikki', 'Cusay', '2004-09-25', 'I LOVE GIFF SERRANO', 'uploads/user_4.png', '', 0, '2025-07-15 03:51:07', 0.00, 0.00000000, 0.00000000, 0.00, 0.00, '2025-07-15 03:42:49'),
(5, 'ahdflkhakl', 'kajfdlkja@gmail.com', 'justaopvcjkabIOUIO!', 'premium', '2025-07-16 15:33:56', NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-07-16 15:51:22', 1331597.00, 100000.00000000, 0.00000000, 1231098.00, 0.00, '2025-07-16 15:49:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `deposits`
--
ALTER TABLE `deposits`
  ADD CONSTRAINT `deposits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
