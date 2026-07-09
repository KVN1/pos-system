-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 01, 2025 at 05:35 AM
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
-- Database: `infinite_pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `parent_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Fruits & Vegetables', NULL, 'active', '2025-03-27 09:43:38', '2025-03-27 09:43:38'),
(2, 'Dairy & Eggs', NULL, 'active', '2025-03-27 09:43:38', '2025-03-27 09:43:38'),
(3, 'Meat & Seafood', NULL, 'active', '2025-03-27 09:43:38', '2025-03-27 09:43:38'),
(4, 'Beverages', NULL, 'active', '2025-03-27 09:43:38', '2025-03-27 09:43:38'),
(5, 'Snacks', NULL, 'active', '2025-03-27 09:43:38', '2025-03-28 13:50:55'),
(7, 'Leafy Vegetables', 1, 'active', '2025-03-27 09:43:46', '2025-03-27 09:43:46'),
(8, 'Root Crops', 1, 'active', '2025-03-27 09:43:46', '2025-03-27 09:43:46'),
(9, 'Milk', 2, 'active', '2025-03-27 09:43:46', '2025-03-27 09:43:46'),
(10, 'Cheese', 2, 'active', '2025-03-27 09:43:46', '2025-03-27 09:43:46'),
(11, 'Yogurt', 2, 'active', '2025-03-27 09:43:46', '2025-03-27 09:43:46'),
(12, 'Chicken', 3, 'active', '2025-03-27 09:43:46', '2025-03-27 09:43:46'),
(13, 'Beef', 3, 'active', '2025-03-27 09:43:46', '2025-03-27 09:43:46'),
(14, 'Fish', 3, 'active', '2025-03-27 09:43:46', '2025-03-27 09:43:46'),
(15, 'Shrimp', 3, 'active', '2025-03-27 09:43:46', '2025-03-27 09:43:46'),
(16, 'Soft Drinks', 4, 'active', '2025-03-27 09:43:46', '2025-03-27 09:43:46'),
(17, 'Juices', 4, 'active', '2025-03-27 09:43:46', '2025-03-27 09:43:46'),
(18, 'Coffee', 4, 'active', '2025-03-27 09:43:46', '2025-03-27 09:43:46'),
(19, 'Tea', 4, 'active', '2025-03-27 09:43:46', '2025-03-27 09:43:46'),
(20, 'Chips', 5, 'active', '2025-03-27 09:43:46', '2025-03-27 09:43:46'),
(21, 'Biscuits', 5, 'active', '2025-03-27 09:43:46', '2025-03-27 09:43:46'),
(22, 'Nuts', 5, 'active', '2025-03-27 09:43:46', '2025-03-27 09:43:46'),
(23, 'Spices', NULL, 'active', '2025-03-27 09:49:18', '2025-03-27 09:49:18'),
(24, 'Household Supplies', NULL, 'active', '2025-03-27 12:09:35', '2025-03-27 12:09:35'),
(25, 'test', NULL, 'active', '2025-03-27 13:27:57', '2025-03-27 13:27:57'),
(32, 'asdasdasdasdasd', NULL, 'active', '2025-03-28 12:23:33', '2025-03-28 12:23:33'),
(33, 'yehey', NULL, 'active', '2025-03-28 12:27:46', '2025-03-28 12:27:46'),
(34, 'tey', NULL, 'active', '2025-03-28 12:28:57', '2025-03-28 12:28:57'),
(35, 'tesssssssst', NULL, 'active', '2025-03-28 12:30:20', '2025-03-28 12:30:20'),
(36, 'hehe', NULL, 'active', '2025-03-28 13:51:00', '2025-03-28 13:51:00'),
(37, 'Medical', NULL, 'active', '2025-03-29 13:19:41', '2025-03-29 13:19:41'),
(38, 'ss', NULL, 'active', '2025-03-30 05:35:40', '2025-03-30 05:35:40'),
(39, 'aa', NULL, 'active', '2025-03-30 09:23:12', '2025-03-30 09:23:12'),
(40, 'mm', NULL, 'active', '2025-03-30 11:24:07', '2025-03-30 11:24:07');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(50) NOT NULL,
  `stock` int(11) NOT NULL,
  `buy_price` decimal(10,2) NOT NULL,
  `sell_price` decimal(10,2) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiry` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `code`, `description`, `category`, `stock`, `buy_price`, `sell_price`, `date_added`, `expiry`) VALUES
(1, '850040427066', 'Prime Ice Pop 500ml', 'Beverages', 50, 200.00, 220.00, '2025-03-27 02:48:11', ''),
(2, '4800361331500', 'BearBrand Sterelized 200ml', 'Beverages', 100, 25.00, 30.00, '2025-03-27 02:51:42', ''),
(3, '4800067130063', 'Philusa Ethyl Alcohol 70% 350ml', 'Household Supplies', 50, 80.00, 90.00, '2025-03-27 04:09:46', ''),
(4, '4809010508898', 'Milcu underarm&foot 40g', 'Household Supplies', 50, 50.00, 60.00, '2025-03-27 04:45:57', ''),
(6, '4806524039887', 'Luxe Organix Aloe Vera 100ml', 'Fruits & Vegetables', 20, 180.00, 195.00, '2025-03-28 05:39:40', '2025-03-15'),
(10, '6974944461538', 'Telesin Gopro battery HERO8/7/6', 'Household Supplies', 14, 90.00, 100.00, '2025-03-29 19:38:40', '2025-09-30'),
(11, '123', '123', 'Meat & Seafood', 5, 97.00, 98.00, '2025-03-30 00:01:35', '2025-03-27'),
(12, '123123', '123123', 'Fruits & Vegetables', 123123, 123123.00, 123123.00, '2025-03-30 00:48:08', '2025-03-20'),
(13, '123123', '12323', 'Beverages', 0, 123.00, 123.00, '2025-03-30 01:19:24', '2025-03-31'),
(14, '234', '2fsdf', 'Fruits & Vegetables', 123, 123.00, 123.00, '2025-03-30 03:25:33', '2025-03-21');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `sale_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_items`
--

CREATE TABLE `sales_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `product_code` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','seller','cashier') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `password`, `role`) VALUES
(1, 'Rutty', 'Joshua', 'SHUWAWA', '$2y$10$DT14CHxeuOp7krG.8aogVOY5./6yW92z8lJBn7Cw3wTkYd1xIjb3y', 'admin'),
(2, 'Kevin', 'Kevin', 'KVN', '$2y$10$ffnHTuSXUogI9W6tIyo6iOmwSxvh8YJCbkoTHllIKcmdIxL4ctEzO', 'cashier');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_items`
--
ALTER TABLE `sales_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_items`
--
ALTER TABLE `sales_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales_items`
--
ALTER TABLE `sales_items`
  ADD CONSTRAINT `sales_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
