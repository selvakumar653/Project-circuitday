-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2025 at 12:07 PM
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
-- Database: `hotel_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_settings`
--

CREATE TABLE `admin_settings` (
  `id` int(11) NOT NULL,
  `setting_name` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_settings`
--

INSERT INTO `admin_settings` (`id`, `setting_name`, `setting_value`, `updated_at`) VALUES
(1, 'restaurant_name', 'Chellappa Hotel', '2025-04-14 01:05:44'),
(2, 'contact_email', 'contact@chellappahotel.com', '2025-04-14 01:05:44'),
(3, 'opening_hours', '7:00-22:00', '2025-04-14 01:05:44'),
(4, 'theme_color', '#8B0000', '2025-04-14 01:05:44'),
(5, 'maintenance_mode', 'false', '2025-04-14 01:05:44');

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `bill_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `location_type` ENUM('table','room','takeaway') DEFAULT NULL,
  `location_number` INT DEFAULT NULL,
  `items` text NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`bill_id`, `customer_name`, `location_type`, `location_number`, `items`, `quantity`, `total_amount`, `status`, `order_date`) VALUES
(1, 'John Doe', NULL, NULL, 'Sample Order', 1, 100.00, 'completed', '2025-04-14 02:13:19'),
(2, 'Guest', NULL, NULL, 'Gulab Jamun x1, Payasam x2, Poori Masala x2', 5, 440.00, 'pending', '2025-04-14 02:26:44'),
(3, 'Guest', NULL, NULL, 'Gulab Jamun x1, Payasam x1', 2, 170.00, 'pending', '2025-04-14 02:29:05'),
(4, 'Guest', NULL, NULL, 'Ghee Roast Dosa x3', 3, 450.00, 'pending', '2025-04-14 04:12:21'),
(5, 'Guest', NULL, NULL, 'Chettinad Chicken x10', 10, 3200.00, 'pending', '2025-04-14 04:36:31'),
(6, 'Guest', NULL, NULL, 'Chettinad Chicken x1', 1, 320.00, 'pending', '2025-04-14 04:36:39');

-- --------------------------------------------------------

--
-- Table structure for table `food_items`
--

CREATE TABLE `food_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(50) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `alert_threshold` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `description`, `price`, `category`, `stock_quantity`, `image_url`, `available`, `created_at`, `alert_threshold`) VALUES
(1, 'Chettinad Chicken', 'Spicy chicken curry with authentic Chettinad masala', 320.00, 'Main Course', -5, 'https://images.unsplash.com/photo-1631515243349-e0cb75fb8d3a', 1, '2025-04-14 01:46:51', 5),
(2, 'Meen Kuzhambu', 'Traditional Tamil fish curry with tamarind', 280.00, 'Main Course', 30, 'https://images.unsplash.com/photo-1601050690597-df0568f70950', 1, '2025-04-14 01:46:51', 5),
(3, 'Ghee Roast Dosa', 'Crispy fermented rice crepe roasted in pure ghee', 150.00, 'Breakfast', 97, 'https://images.unsplash.com/photo-1633945274309-2c16c9682a8c', 1, '2025-04-14 01:46:51', 5),
(4, 'Idli Sambar', 'Soft steamed rice cakes served with sambar and chutney', 60.00, 'Breakfast', 200, 'https://images.unsplash.com/photo-1630891905826-d67cdb516967', 1, '2025-04-14 01:50:14', 5),
(5, 'Pongal', 'Traditional rice and lentil dish with pepper and ghee', 80.00, 'Breakfast', 100, 'https://images.unsplash.com/photo-1610192244261-63c6e09c798d', 1, '2025-04-14 01:50:14', 5),
(6, 'Vada', 'Crispy lentil donuts served with sambar and chutney', 50.00, 'Breakfast', 150, 'https://images.unsplash.com/photo-1630891905792-21f3b0293cd5', 1, '2025-04-14 01:50:14', 5),
(7, 'Poori Masala', 'Fluffy deep-fried bread with potato masala', 90.00, 'Breakfast', 98, 'https://images.unsplash.com/photo-1630891905684-33c369d26770', 1, '2025-04-14 01:50:14', 5),
(8, 'Paneer Butter Masala', 'Cottage cheese in rich tomato gravy', 180.00, 'Main Course', 80, 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7', 1, '2025-04-14 01:50:14', 5),
(9, 'Vegetable Biryani', 'Fragrant rice cooked with mixed vegetables and spices', 160.00, 'Main Course', 100, 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8', 1, '2025-04-14 01:50:14', 5),
(10, 'Dal Tadka', 'Yellow lentils tempered with spices', 140.00, 'Main Course', 90, 'https://images.unsplash.com/photo-1546833999-b9f581a1996d', 1, '2025-04-14 01:50:14', 5),
(11, 'Kadai Mushroom', 'Mushrooms cooked with bell peppers in spicy gravy', 170.00, 'Main Course', 60, 'https://images.unsplash.com/photo-1631452180775-4b60f6dde2fb', 1, '2025-04-14 01:50:14', 5),
(12, 'Chicken 65', 'Spicy deep-fried chicken with curry leaves', 220.00, 'Main Course', 80, 'https://images.unsplash.com/photo-1610057099443-fde8c4d50f91', 1, '2025-04-14 01:50:14', 5),
(13, 'Mutton Curry', 'Traditional Tamil-style mutton curry', 280.00, 'Main Course', 50, 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7', 1, '2025-04-14 01:50:14', 5),
(14, 'Fish Curry', 'Fresh fish cooked in tangy tamarind sauce', 240.00, 'Main Course', 40, 'https://images.unsplash.com/photo-1631452180927-df0a647a29b4', 1, '2025-04-14 01:50:14', 5),
(15, 'Prawn Masala', 'Prawns cooked in spicy masala gravy', 260.00, 'Main Course', 30, 'https://images.unsplash.com/photo-1610057099431-d8a9c69fe0c7', 1, '2025-04-14 01:50:14', 5),
(16, 'Sambar Rice', 'Rice mixed with traditional lentil stew', 100.00, 'Rice', 150, 'https://images.unsplash.com/photo-1630891905924-71d975b45206', 1, '2025-04-14 01:50:14', 5),
(17, 'Curd Rice', 'Yogurt rice tempered with mustard and curry leaves', 90.00, 'Rice', 100, 'https://images.unsplash.com/photo-1630891905775-5f5fc70dd6c5', 1, '2025-04-14 01:50:14', 5),
(18, 'Lemon Rice', 'Tangy rice with peanuts and curry leaves', 95.00, 'Rice', 100, 'https://images.unsplash.com/photo-1630891905739-8c83247e237f', 1, '2025-04-14 01:50:14', 5),
(19, 'Coconut Rice', 'Rice flavored with fresh coconut and spices', 100.00, 'Rice', 80, 'https://images.unsplash.com/photo-1630891905890-15e958c1c176', 1, '2025-04-14 01:50:14', 5),
(20, 'Parotta', 'Flaky layered flatbread', 40.00, 'Breads', 200, 'https://images.unsplash.com/photo-1630891905860-f7b5b4464f99', 1, '2025-04-14 01:50:14', 5),
(21, 'Naan', 'Tandoor-baked flatbread', 45.00, 'Breads', 150, 'https://images.unsplash.com/photo-1610057099493-b30bd767fc47', 1, '2025-04-14 01:50:14', 5),
(22, 'Chapati', 'Whole wheat flatbread', 35.00, 'Breads', 200, 'https://images.unsplash.com/photo-1630891905849-21e2e0a40a5c', 1, '2025-04-14 01:50:14', 5),
(23, 'Butter Roti', 'Whole wheat bread with butter', 40.00, 'Breads', 150, 'https://images.unsplash.com/photo-1630891905833-f6e8f2f71c18', 1, '2025-04-14 01:50:14', 5),
(24, 'Gulab Jamun', 'Sweet milk dumplings in sugar syrup', 80.00, 'Desserts', 98, 'https://images.unsplash.com/photo-1630891905935-d4a3cce636c5', 1, '2025-04-14 01:50:14', 5),
(25, 'Payasam', 'Traditional South Indian sweet pudding', 90.00, 'Desserts', 77, 'https://images.unsplash.com/photo-1630891905847-7f9b34a8b6a9', 1, '2025-04-14 01:50:14', 5),
(26, 'Rasmalai', 'Soft cottage cheese dumplings in sweet milk', 100.00, 'Desserts', 60, 'https://images.unsplash.com/photo-1630891905853-d6f4b3a7e751', 1, '2025-04-14 01:50:14', 5),
(27, 'Jalebi', 'Crispy spiral sweets in sugar syrup', 70.00, 'Desserts', 90, 'https://images.unsplash.com/photo-1630891905869-f6b7b36d1fd1', 1, '2025-04-14 01:50:14', 5),
(28, 'Masala Chai', 'Indian spiced tea', 30.00, 'Beverages', 200, 'https://images.unsplash.com/photo-1561336526-2914f13ceb29', 1, '2025-04-14 01:50:14', 5),
(29, 'Filter Coffee', 'Traditional South Indian coffee', 35.00, 'Beverages', 200, 'https://images.unsplash.com/photo-1610057099453-ce3f52ee9e4c', 1, '2025-04-14 01:50:14', 5),
(30, 'Buttermilk', 'Spiced yogurt drink', 25.00, 'Beverages', 150, 'https://images.unsplash.com/photo-1630891905854-f6c46b2c7a0e', 1, '2025-04-14 01:50:14', 5),
(31, 'Fresh Lime Soda', 'Refreshing lime-based drink', 40.00, 'Beverages', 100, 'https://images.unsplash.com/photo-1630891905858-9b5f5f5e5e5d', 1, '2025-04-14 01:50:14', 5),
(32, 'Onion Pakoda', 'Crispy onion fritters', 80.00, 'Starters', 100, 'https://images.unsplash.com/photo-1630891905842-f6c46b2c7a0d', 1, '2025-04-14 01:50:14', 5),
(33, 'Paneer 65', 'Spicy cottage cheese starter', 160.00, 'Starters', 80, 'https://images.unsplash.com/photo-1630891905851-f6c46b2c7a0f', 1, '2025-04-14 01:50:14', 5),
(34, 'Gobi Manchurian', 'Indo-Chinese cauliflower fritters', 140.00, 'Starters', 90, 'https://images.unsplash.com/photo-1630891905836-f6c46b2c7a0b', 1, '2025-04-14 01:50:14', 5),
(35, 'Papad', 'Crispy lentil wafers', 20.00, 'Sides', 300, 'https://images.unsplash.com/photo-1630891905844-f6c46b2c7a0c', 1, '2025-04-14 01:50:14', 5);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `location_type` ENUM('table','room','takeaway') DEFAULT NULL,
  `location_number` INT DEFAULT NULL,
  `order_date` datetime NOT NULL,
  `status` enum('pending','confirmed','preparing','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `item_name` VARCHAR(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` DECIMAL(10,2) DEFAULT NULL,
  `subtotal` DECIMAL(10,2) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `phone`, `room_number`, `password`, `created_at`) VALUES
(14, 'sxdcfvgb', 'selvakl653@gmail.com', '6', '100', '$2y$10$Jdcp6HFzTVW2XXUfyWOJoec0Vu9EMuydbHkxwWmK6OVOGN2DYtGLi', '2025-03-30 11:28:34'),
(15, 'sxdcfvgb', 'selvakur@gmail.com', '6379516896', '100', '$2y$10$Wcb81LoazpeeG.VAJ8bM5uvtV3vctYOGBrkkVmT/3tEUMNqDFVbl2', '2025-03-30 11:29:35'),
(16, 'selvakumar', 'selvarsakthivel653@gmail.com', '6379516896', '100', '$2y$10$jm4wKnUrqsvjjpj51ha8zObpato4vHQdZ2MjAvFnqcn9fprwTKuHe', '2025-03-30 11:30:58'),
(17, 'selvakumar', 'selvakarsathival653@gmail.com', '6379516896', '100', '$2y$10$R6OoGcSCikRJf4monUE3Zew1uOXxRdYJhe0w2AwKlLqxBXKAvYemy', '2025-03-30 11:31:58'),
(18, 'selvakumar', 'selvakumarsathival653@gmail.com', '6379516896', '100', '$2y$10$z6LoQZQWsutq2Hlkr9PdMuYSLPu9iOpTpZ6YCmy3xGJwA.o/Sp2p.', '2025-03-30 11:32:56'),
(19, 'selvakumar', 'sersathival653@gmail.com', '6379516896', '100', '$2y$10$0Sl8INxTUJWag2ND4fP4PuZAbOVSg7yY2j9fcQV18KYjyeqUlw5IW', '2025-03-30 12:24:23'),
(20, 'selvakumar', 'selvakumarsathival53@gmail.com', '6379516896', '100', '$2y$10$.hn6AJF449Wp6ZrJLU4r1O1oA0SD5sngRJCpqApUhK6FF.1nQCkXO', '2025-04-10 15:00:31'),
(21, 'John Doe', 'john@example.com', '1234567890', '', '', '0000-00-00 00:00:00'),
(22, 'Jane Smith', 'jane@example.com', '9876543210', '', '', '0000-00-00 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_settings`
--
ALTER TABLE `admin_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_name` (`setting_name`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`bill_id`);

--
-- Indexes for table `food_items`
--
ALTER TABLE `food_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `food_id` (`food_id`);

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
-- AUTO_INCREMENT for table `admin_settings`
--
ALTER TABLE `admin_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `bill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `food_items`
--
ALTER TABLE `food_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`food_id`) REFERENCES `food_items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
