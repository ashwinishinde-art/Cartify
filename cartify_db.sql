-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 20, 2026 at 07:13 AM
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
-- Database: `cartify_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `merchants`
--

CREATE TABLE `merchants` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `business_email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_id` varchar(100) NOT NULL,
  `status` enum('Pending','Shipped','Delivered','Cancelled','Returned') DEFAULT 'Pending',
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `delivery_address` text NOT NULL,
  `is_reviewed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `quantity`, `total_price`, `payment_id`, `status`, `customer_name`, `customer_phone`, `delivery_address`, `is_reviewed`, `created_at`) VALUES
(1, 1, 3, 1, 145.00, 'pay_TErhAjWpbuW6FP', 'Cancelled', 'soham sonawane', '', 'Delivery Address Simulation', 0, '2026-07-18 06:23:07'),
(2, 7, 4, 1, 10.00, 'pay_TEu4CnN34IGYAo', 'Pending', 'Soham Sonawane', '', 'Delivery Address Simulation', 0, '2026-07-18 08:42:18'),
(3, 2, 4, 1, 10.00, 'pay_TFNzRTLW1cF33G', 'Pending', 'Sohams sonawane', '9511679983', 'narhe, pune,\npune - 431120', 0, '2026-07-19 13:59:27'),
(4, 2, 4, 2, 20.00, 'pay_TFO48BjmcS1OOG', 'Delivered', 'Kanchan sony', '9518907045', 'Nimbayati Tal- soegaon,\nSoegaon - 431120', 1, '2026-07-19 14:03:04'),
(5, 3, 8, 1, 10.00, 'pay_TFcouSdqA3AAIZ', 'Cancelled', 'SOham ', '9511679983', 'Pune, narhe,\nPune - 431120', 0, '2026-07-20 04:29:01'),
(6, 2, 8, 1, 10.00, 'pay_TFdJcgi2au0WQN', 'Delivered', 'kanchan sony', '9518907045', 'nimbayati,\n431120 - 431120', 1, '2026-07-20 04:58:06');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `merchant_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `image_gallery` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `in_stock` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `merchant_id`, `title`, `description`, `price`, `image_url`, `image_gallery`, `category`, `in_stock`, `created_at`) VALUES
(2, 1, 'Vertical Striped Shirt', 'Elevate your everyday wardrobe with our classic Vertical Striped Shirt. Crafted from 100% breathable cotton, it features a sharp spread collar, a buttoned placket, and a flattering cut designed to give you a sleek, structured appearance. It pairs perfectly with chinos or jeans', 212.00, 'uploads/prod_6a591cbb9913a1.83384651.png', NULL, 'Fashion', 1, '2026-07-16 18:02:35'),
(3, 1, 'Courage Graphic T-shirt', 'Channel your inner hero and wear your heart on your sleeve with the Courage Graphic T-Shirt. Featuring nostalgic, pop-art illustrations and heavyweight premium fabrics, it blends all-day comfort with a bold streetwear silhouette.', 145.00, 'uploads/prod_6a5920488e1dc5.48748333.png', NULL, 'Fashion', 1, '2026-07-16 18:17:44'),
(4, 1, 'Cotton T shirt', 'A shirt is an upper-body garment that traditionally features a collar, sleeves, and a front opening. For an exact breakdown of technical details (fabric composition, fit, and care instructions) that make or break a product description, or to browse various clothing styles for inspiration, you can check out the resources below. ', 10.00, 'uploads/prod_6a5b2379d94880.72622704.jpeg', NULL, 'Fashion', 1, '2026-07-18 06:55:53'),
(8, 3, 'METRONAUT Self Design Men Round Neck Polycotton Navy Blue T-Shirt', 'Brand\r\nMETRONAUT\r\nType\r\nRound Neck\r\nSleeve\r\nFull Sleeve\r\nFit\r\nSlim\r\nFabric\r\nPolycotton\r\nSales Package\r\n1 Full Sleeve Tshirt\r\nPack of\r\n1\r\nStyle Code\r\nMT417\r\nNeck Type\r\nRound Neck\r\nIdeal For\r\nMen\r\nSize\r\nS\r\nPattern\r\nSelf Design\r\nSuitable For\r\nWestern Wear\r\nReversible\r\nNo', 10.00, 'uploads/prod_6a5cef0f970a18.46448492.webp', NULL, 'Fashion', 1, '2026-07-19 15:36:47');

-- --------------------------------------------------------

--
-- Table structure for table `promo_codes`
--

CREATE TABLE `promo_codes` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_value` decimal(10,2) DEFAULT 0.00,
  `expiry_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promo_codes`
--

INSERT INTO `promo_codes` (`id`, `code`, `discount_type`, `discount_value`, `min_order_value`, `expiry_date`, `is_active`, `created_at`) VALUES
(1, 'CARTIFY20', 'percentage', 20.00, 50.00, '2028-12-31', 1, '2026-07-18 05:54:45');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `user_name`, `rating`, `comment`, `created_at`) VALUES
(1, 4, 2, 'Kanchan Sonawane', 5, 'Its a good product', '2026-07-20 04:56:37'),
(2, 8, 2, 'Kanchan Sonawane', 5, 'Real cotton, and very good product', '2026-07-20 04:59:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'buyer',
  `store_name` varchar(100) DEFAULT NULL,
  `tax_id` varchar(50) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `business_address` text DEFAULT NULL,
  `shipping_method` varchar(50) DEFAULT NULL,
  `bank_account_no` varchar(50) DEFAULT NULL,
  `bank_ifsc` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `merchant_status` enum('none','active','revoked') DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `store_name`, `tax_id`, `phone_number`, `business_address`, `shipping_method`, `bank_account_no`, `bank_ifsc`, `created_at`, `merchant_status`) VALUES
(1, 'soham sonawane', 'sohamsonawane22@gmail.com', '$2y$10$rQDUaXAMarp0SwKRgb8kquvXq/WPisbKKnT.Jh0b.A5q1b1giviY6', 'customer', 'Soham\'s store', NULL, '9511679983', 'sohambhai54@', 'Self-Ship', '12345', 'SBIN124', '2026-07-16 13:38:10', 'none'),
(2, 'Kanchan Sonawane', 'kanchansony3@gmail.com', '$2y$10$fwwbCbncenitgwG50oL/bOGJMOy/APCczGXNLpCbY9XynKIVGvJba', 'buyer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-16 15:02:35', 'none'),
(3, 'vijay sonawane', 'vijaybhaiyya3333@gmail.com', '$2y$10$mzNJHyQ3rFQs3hrUsp.xj.0WgVC2755uKJogtFPN6gF.0Blgn2kJy', 'merchant', 'Vijaya Store', NULL, '9970208201', 'Nimbayati, Tal- soegaon , Dist- Sambhajinagar', 'Cartify-Fulfill', '123457', 'SBIN1234', '2026-07-19 15:22:09', 'active'),
(4, 'John Doe', 'john@example.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1e8...', 'merchant', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-20 04:48:48', 'active'),
(5, 'Admin', 'admin@cartify.com', '$2y$10$lyNzUWFgfj6KmhZViQg6YetnnaAO1wqFkycby0gGFj5Umu4seVycS', 'admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-20 04:50:27', 'none');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `merchants`
--
ALTER TABLE `merchants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `merchant_id` (`merchant_id`);

--
-- Indexes for table `promo_codes`
--
ALTER TABLE `promo_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `merchants`
--
ALTER TABLE `merchants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `promo_codes`
--
ALTER TABLE `promo_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`merchant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
