-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2025 at 04:36 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `water_station`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `quantity`) VALUES
(1, 'Container w/ Faucet', '29.00', 'container with faucet.png', 31),
(2, 'Round Container', '35.00', 'PET5gal Round blue.png', 0),
(3, 'Water Bottle', '12.00', 'Water Bottle.png', 0),
(5, 'Water Bottle 1 Liter', '18.00', 'Water Bottle.png', 0),
(8, 'Juice', '15.00', '1-liter-mineral-water-bottles.jpg', 37),
(10, 'Juice 3', '15.00', '', 50);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `product` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `product`, `price`, `quantity`, `timestamp`, `date`) VALUES
(1, 'Container w/ Faucet', '29.00', 1, '2025-04-27 10:03:03', '2025-04-27'),
(2, 'Container w/ Faucet', '29.00', 1, '2025-04-27 10:07:07', '2025-04-27'),
(3, 'Container w/ Faucet', '29.00', 1, '2025-04-27 10:12:50', '2025-04-27'),
(4, 'Container w/ Faucet', '29.00', 1, '2025-04-27 10:17:57', '2025-04-27'),
(5, 'Container w/ Faucet', '29.00', 1, '2025-05-09 08:36:15', '2025-05-09'),
(6, 'Container w/ Faucet', '29.00', 6, '2025-05-09 08:39:15', '2025-05-09'),
(7, NULL, NULL, NULL, '2025-05-09 08:57:15', '2025-05-09'),
(8, NULL, NULL, NULL, '2025-05-09 17:23:02', '2025-05-09');

-- --------------------------------------------------------

--
-- Table structure for table `sales_items`
--

CREATE TABLE `sales_items` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sales_items`
--

INSERT INTO `sales_items` (`id`, `transaction_id`, `product_id`, `product_name`, `price`, `quantity`) VALUES
(1, 1, 1, 'Container w/ Faucet', '29.00', 1),
(2, 1, 2, 'Round Container', '35.00', 1),
(3, 2, 1, 'Container w/ Faucet', '29.00', 1),
(4, 2, 2, 'Round Container', '35.00', 1),
(5, 3, 1, 'Container w/ Faucet', '29.00', 3),
(6, 3, 2, 'Round Container', '35.00', 2),
(7, 4, 1, 'Container w/ Faucet', '29.00', 2),
(8, 4, 6, 'Juice', '15.00', 1),
(9, 5, 1, 'Container w/ Faucet', '29.00', 2),
(10, 5, 8, 'Juice', '15.00', 2),
(11, 6, 1, 'Container w/ Faucet', '29.00', 1),
(12, 6, 3, 'Water Bottle', '12.00', 1),
(13, 7, 1, 'Container w/ Faucet', '29.00', 1),
(14, 8, 1, 'Container w/ Faucet', '29.00', 1),
(15, 9, 1, 'Container w/ Faucet', '29.00', 1),
(16, 10, 1, 'Container w/ Faucet', '29.00', 1),
(17, 11, 1, 'Container w/ Faucet', '29.00', 1),
(18, 12, 1, 'Container w/ Faucet', '29.00', 1),
(19, 12, 5, 'Water Bottle 1 Liter', '18.00', 1),
(20, 13, 1, 'Container w/ Faucet', '29.00', 1),
(21, 13, 5, 'Water Bottle 1 Liter', '18.00', 1),
(22, 14, 9, 'Juice 1', '15.00', 1),
(23, 14, 5, 'Water Bottle 1 Liter', '18.00', 1),
(24, 15, 1, 'Container w/ Faucet', '29.00', 1),
(25, 15, 5, 'Water Bottle 1 Liter', '18.00', 1),
(26, 16, 1, 'Container w/ Faucet', '29.00', 1),
(27, 16, 5, 'Water Bottle 1 Liter', '18.00', 1),
(28, 16, 8, 'Juice', '15.00', 1),
(29, 17, 8, 'Juice', '15.00', 7),
(30, 17, 5, 'Water Bottle 1 Liter', '18.00', 1),
(31, 18, 5, 'Water Bottle 1 Liter', '18.00', 6),
(32, 18, 1, 'Container w/ Faucet', '29.00', 9),
(33, 19, 1, 'Container w/ Faucet', '29.00', 5),
(34, 19, 5, 'Water Bottle 1 Liter', '18.00', 2),
(35, 20, 1, 'Container w/ Faucet', '29.00', 4),
(36, 20, 8, 'Juice', '15.00', 3),
(37, 21, 1, 'Container w/ Faucet', '29.00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sales_transactions`
--

CREATE TABLE `sales_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `user` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sales_transactions`
--

INSERT INTO `sales_transactions` (`id`, `user_id`, `date`, `user`, `customer_name`) VALUES
(1, NULL, '2025-05-09 17:29:57', 'admin', NULL),
(2, NULL, '2025-05-09 17:30:02', 'admin', NULL),
(3, NULL, '2025-05-09 17:35:05', 'admin', NULL),
(4, NULL, '2025-05-09 17:35:38', 'admin', NULL),
(5, NULL, '2025-05-09 18:04:55', 'admin', 'Linda'),
(6, NULL, '2025-05-09 18:23:18', 'admin', 'Linda'),
(7, NULL, '2025-05-09 18:23:38', 'admin', 'Linda'),
(8, NULL, '2025-05-09 18:27:36', 'admin', 'Linda'),
(9, NULL, '2025-05-09 18:28:17', 'admin', 'Linda'),
(10, NULL, '2025-05-09 18:28:31', 'admin', 'Linda'),
(11, NULL, '2025-05-09 18:46:25', 'admin', 'Test'),
(12, NULL, '2025-05-09 18:46:41', 'admin', 'Test'),
(13, NULL, '2025-05-11 12:04:05', 'admin1', 'Test'),
(14, NULL, '2025-05-11 12:05:07', 'admin1', 'Test'),
(15, NULL, '2025-05-11 12:14:49', 'admin', 'Test'),
(16, NULL, '2025-05-11 13:00:36', 'admin', 'Test'),
(17, NULL, '2025-05-11 13:02:36', 'admin', 'Test'),
(18, NULL, '2025-05-11 13:02:50', 'admin', 'Linda'),
(19, NULL, '2025-05-14 19:16:44', 'admin', 'Test'),
(20, NULL, '2025-05-14 19:18:22', 'admin1', 'Test'),
(21, NULL, '2025-05-18 22:31:03', 'admin', 'Test');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(11, 'admin1', '$2y$10$VP9x8ebgoRKmnWC3D3MOruhT1hdfLvuI95evFl.JVBwKj.gpciYj2'),
(12, 'admin', '$2y$10$ukxwGpZeJOQVyubQIxpH.eKswWpL84EsQx6pLYzIoyn7oD6n86zay'),
(13, 'admin2', '$2y$10$YU5Za4oetlB5ffXkfsNqceMP6AY9vGvXfi481mc/GBF1KIW4gk/3O');

--
-- Indexes for dumped tables
--

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
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `sales_transactions`
--
ALTER TABLE `sales_transactions`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sales_items`
--
ALTER TABLE `sales_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `sales_transactions`
--
ALTER TABLE `sales_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sales_items`
--
ALTER TABLE `sales_items`
  ADD CONSTRAINT `sales_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `sales_transactions` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
