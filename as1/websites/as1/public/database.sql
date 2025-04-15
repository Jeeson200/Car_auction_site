-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Generation Time: Apr 14, 2025 at 03:03 PM
-- Server version: 11.6.2-MariaDB-ubu2404
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `assignment1`
--

-- --------------------------------------------------------

--
-- Table structure for table `auctions`
--

CREATE TABLE `auctions` (
  `id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text NOT NULL,
  `category_id` int(11) NOT NULL,
  `end_date` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `auctions`
--

INSERT INTO `auctions` (`id`, `title`, `description`, `category_id`, `end_date`, `user_id`, `image_path`) VALUES
(3, 'car', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 5, '2025-04-20 11:09:00', 6, 'public/images/auctions/67fc9c0f27fc3_2.jpg'),
(4, 'car', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum', 7, '2025-04-29 11:49:00', 1, 'public/images/auctions/67fca56587c80_3.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `bids`
--

CREATE TABLE `bids` (
  `id` int(11) NOT NULL,
  `auction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bid_amount` decimal(10,2) NOT NULL,
  `bid_time` datetime DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `bids`
--

INSERT INTO `bids` (`id`, `auction_id`, `user_id`, `bid_amount`, `bid_time`, `created_at`) VALUES
(2, 3, 6, 4000.00, '2025-04-14 05:25:28', '2025-04-14 05:25:28'),
(3, 4, 1, 5000.00, '2025-04-14 06:04:28', '2025-04-14 06:04:28'),
(4, 4, 1, 15000.00, '2025-04-14 07:48:39', '2025-04-14 07:48:39'),
(5, 4, 1, 30000.00, '2025-04-14 12:56:34', '2025-04-14 12:56:34'),
(6, 4, 1, 40000.00, '2025-04-14 14:26:43', '2025-04-14 14:26:43'),
(7, 4, 6, 50000.00, '2025-04-14 15:02:12', '2025-04-14 15:02:12');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent_id`, `sort_order`, `date_added`) VALUES
(1, 'Estate', NULL, 0, '2025-04-14 04:22:20'),
(3, 'Coupe', NULL, 0, '2025-04-14 04:22:20'),
(4, 'Saloon', NULL, 0, '2025-04-14 04:22:20'),
(5, '4x4', NULL, 0, '2025-04-14 04:22:20'),
(7, 'Hybrid', NULL, 0, '2025-04-14 04:22:20'),
(10, 'Sports', NULL, 0, '2025-04-14 08:16:37'),
(11, 'Electric', NULL, 0, '2025-04-14 08:17:25');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `review_text` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `auction_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `review_text`, `user_id`, `auction_id`, `created_at`) VALUES
(1, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum', 1, 4, '2025-04-14 07:15:11'),
(2, '111', 1, 4, '2025-04-14 13:08:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `name`, `isAdmin`) VALUES
(1, 'jeeson219@gmail.com', '$2y$12$GAqt26MgG1tVZdsY0NVbcuvvrWeLYOj8ufNXL0wUTw1RRexfiDglC', 'jeeson', 0),
(6, 'cascaadmin@gmail.com', '$2y$12$qDfl6dLZw2qrc2QrTbfWD.XhZSYFfj1TtmAXRshaV/IQXhD/kb/my', 'Casca', 1),
(8, 'sofie@gmail.com', '$2y$12$J4Wb8QXtlC/Ebyp5g35Biu1c29kx/rrC3GRTqNodUVQrlENaE5OeS', 'sofie', 0),
(9, 'sasa@gmailcom', '$2y$12$1fMpvDTf3k8rjrDND0I.N.1THvvXGruKvHwMtCZzFFO2tcPIRMsW2', 'sasa', 1),
(10, 'myra@gmail.com', '$2y$12$WKresIEs7A/yz2lQikF0b.dVJSFyey9sElXoZ2Fmz7o.lPUPhPPti', 'Myra', 1),
(13, 'hauruadmin@gmail.com', '$2y$12$GFw9VyNHJsSZ/e9gydyWF.6bcPwTZq58FRAvlV8cgd6K5sx8/BuJu', 'hauru', 1),
(14, 'kelvin@gmail.com', '$2y$12$ZigY1V7VEnDBQg6f1RX7X./G9oYhkNJbxX5kyX9K00t7O.UKAEQ6y', 'Kelvin', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auctions`
--
ALTER TABLE `auctions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_auction_category_id` (`category_id`),
  ADD KEY `fk_auction_user_id` (`user_id`);

--
-- Indexes for table `bids`
--
ALTER TABLE `bids`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auction_id` (`auction_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_review_auction_id` (`auction_id`),
  ADD KEY `fk_review_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auctions`
--
ALTER TABLE `auctions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bids`
--
ALTER TABLE `bids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auctions`
--
ALTER TABLE `auctions`
  ADD CONSTRAINT `fk_auction_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_auction_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `bids`
--
ALTER TABLE `bids`
  ADD CONSTRAINT `bids_ibfk_1` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`),
  ADD CONSTRAINT `bids_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_review_auction_id` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`),
  ADD CONSTRAINT `fk_review_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
