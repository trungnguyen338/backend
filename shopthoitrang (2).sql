-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 30, 2025 at 01:18 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shopthoitrang`
--

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
CREATE TABLE IF NOT EXISTS `carts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_carts_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `created_at`) VALUES
(8, 11, '2025-11-30 13:45:33'),
(9, NULL, '2025-11-30 13:51:38'),
(10, 10, '2025-11-30 16:23:44'),
(11, 10, '2025-11-30 16:23:44');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cart_id` int DEFAULT NULL,
  `product_variant_id` int NOT NULL,
  `quantity` int DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_cart_items_cart` (`cart_id`),
  KEY `fk_cart_items_variant` (`product_variant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_variant_id`, `quantity`) VALUES
(21, 8, 17, 1),
(23, 11, 78, 1),
(24, 11, 79, 1),
(26, 11, 58, 1),
(28, 11, 54, 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text,
  `sort_order` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `sort_order`) VALUES
(4, 'ÁO NAM', NULL, 0),
(7, 'QUẦN NAM', NULL, 0),
(8, 'ÁO NỮ', NULL, 0),
(9, 'QUẦN NỮ', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `colors`
--

DROP TABLE IF EXISTS `colors`;
CREATE TABLE IF NOT EXISTS `colors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `colors`
--

INSERT INTO `colors` (`id`, `name`, `image`) VALUES
(13, 'Đen', NULL),
(14, 'Trắng', NULL),
(15, 'Xanh navy', NULL),
(16, 'Xanh nhạt', NULL),
(17, 'Xanh', NULL),
(18, 'Xám', NULL),
(19, 'Xanh đậm', NULL),
(20, 'Be', NULL),
(21, 'Hồng', NULL),
(22, 'Đỏ', NULL),
(23, 'Caro', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `status` enum('pending','processing','shipped','completed','cancelled') DEFAULT 'pending',
  `total` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `shipping_id` int DEFAULT NULL,
  `delivery_status` enum('pending','shipping','delivered','returned') DEFAULT 'pending',
  `address` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_orders_user` (`user_id`),
  KEY `fk_orders_shipping` (`shipping_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `product_variant_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_order_items_order` (`order_id`),
  KEY `fk_order_items_variant` (`product_variant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `method` enum('cod','card','wallet') NOT NULL DEFAULT 'cod',
  `status` enum('pending','paid','failed') NOT NULL DEFAULT 'pending',
  `paid_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_payments_order` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text,
  `status` enum('available','hidden') DEFAULT 'available',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `image` varchar(255) DEFAULT NULL,
  `subcategory_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_products_subcategory` (`subcategory_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `status`, `created_at`, `image`, `subcategory_id`) VALUES
(15, 'Áo thun nam đen', 'Áo thun cotton co giãn thoải mái', 'available', '2025-11-29 14:25:37', 'https://cdn.hstatic.net/products/1000253775/160_set_bo_084-8_1a9a3c6d0765408180f9dbd65f7bc06e_1024x1024.jpg', 2),
(16, 'Áo sơ mi nam trắng', 'Áo sơ mi nam phong cách lịch sự', 'available', '2025-11-29 14:25:37', 'https://cdn.hstatic.net/products/1000253775/160_somi_360-1_e5fbdaa2f39f42009002d54b619bdcf9_1024x1024.jpg', 3),
(17, 'Áo khoác nam xanh', 'Áo khoác nhẹ, chống gió', 'available', '2025-11-29 14:25:37', 'https://th.bing.com/th/id/OIP.d1xMIytU4hBjm33WD1h2oQHaHa?w=210&h=210&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3', 4),
(18, 'Áo polo nam xám', 'Áo polo thoáng mát, năng động', 'available', '2025-11-29 14:25:37', 'https://th.bing.com/th/id/OIP.Gq4aO9d3pUFpnYpfdzgpOAHaHa?w=194&h=194&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3', 5),
(25, 'Quần jean nam xanh đậm', 'Quần jean co giãn thoải mái', 'available', '2025-11-29 17:15:55', 'https://th.bing.com/th/id/OIP.Ztrok6nZxNSIdUe35AiZuwHaHa?w=184&h=184&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3', 6),
(26, 'Quần tây nam xám', 'Quần tây lịch sự cho văn phòng', 'available', '2025-11-29 17:15:55', 'https://th.bing.com/th/id/OIP.-fMbWWab_9BQOm1LMr0YFgHaHa?w=178&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3', 7),
(27, 'Quần short nam kaki', 'Quần short thoải mái, mùa hè', 'available', '2025-11-29 17:15:55', 'https://th.bing.com/th/id/OIP._2cUz4pbuc6illn7qH0h4AHaHa?w=183&h=202&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3', 8),
(28, 'Áo thun nữ hồng', 'Áo thun nữ mềm mại, trẻ trung', 'available', '2025-11-29 17:15:55', 'https://th.bing.com/th/id/OIP.vMf7mM0aMQbje4syGBbrtwHaLI?w=133&h=200&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3', 2),
(29, 'Áo sơ mi nữ trắng', 'Áo sơ mi nữ thanh lịch', 'available', '2025-11-29 17:15:55', 'https://th.bing.com/th/id/OIP.aLHih1TUmMlFcjpOU2tD4AHaIM?w=182&h=202&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3', 3),
(30, 'Đầm nữ đỏ', 'Đầm nữ sang trọng', 'available', '2025-11-29 17:15:55', 'https://th.bing.com/th/id/OIP.i-tAl0I1_FR4u2m51jINzAHaJ3?w=208&h=277&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3', 11),
(31, 'Quần jean nữ xanh nhạt', 'Quần jean nữ thoải mái', 'available', '2025-11-29 17:24:10', 'https://th.bing.com/th/id/OIP.-kGulOpATVROIlB5mr7HXQHaHa?w=177&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3', NULL),
(32, 'Quần tây nữ đen', 'Quần tây nữ lịch sự', 'available', '2025-11-29 17:24:10', 'https://th.bing.com/th/id/OIP.a5wT3X4R-hAFGJINlrRIlQHaHa?w=187&h=187&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3', NULL),
(33, 'Váy nữ caro', 'Váy nữ trẻ trung', 'available', '2025-11-29 17:24:10', 'https://th.bing.com/th/id/OIP.cPUsnFSZcgY0MEZJt3YI1wHaHa?w=207&h=207&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3', 12),
(34, 'Quần tây nữ xịn', 'Quần tây nữ cao cấp', 'available', '2025-11-29 17:24:10', 'https://th.bing.com/th/id/OIP.a9VUHbZkT0prfp5jYyUNHAHaJ4?w=154&h=205&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3', NULL),
(35, 'Quần thun ba lỗ', 'ÁO THUN BA LỖ THOÁNG MÁT', 'available', '2025-11-29 17:29:11', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS2e2fmN8UvGsKi9mkmUIDNcWTUw1X3j1XUCA&s', 13),
(36, 'Áo thun ba lỗ nhiều màu', 'ÁO THUN BA LỖ THOÁNG MÁT', 'available', '2025-11-29 17:29:11', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS2e2fmN8UvGsKi9mkmUIDNcWTUw1X3j1XUCA&s', 5);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `variant_id` int NOT NULL,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_variant` (`variant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `variant_id`, `image_path`) VALUES
(5, 17, 'https://cdn.hstatic.net/products/1000253775/160_set_bo_084-8_1a9a3c6d0765408180f9dbd65f7bc06e_1024x1024.jpg'),
(6, 18, 'https://cdn.hstatic.net/products/1000253775/160_set_bo_084-8_1a9a3c6d0765408180f9dbd65f7bc06e_1024x1024.jpg'),
(7, 19, 'https://cdn.hstatic.net/products/1000253775/160_set_bo_084-8_1a9a3c6d0765408180f9dbd65f7bc06e_1024x1024.jpg'),
(8, 20, 'https://cdn.hstatic.net/products/1000253775/160_set_bo_084-8_1a9a3c6d0765408180f9dbd65f7bc06e_1024x1024.jpg'),
(9, 21, 'https://cdn.hstatic.net/products/1000253775/160_set_bo_084-8_1a9a3c6d0765408180f9dbd65f7bc06e_1024x1024.jpg'),
(12, 22, 'https://cdn.hstatic.net/products/1000253775/160_somi_360-1_e5fbdaa2f39f42009002d54b619bdcf9_1024x1024.jpg'),
(13, 23, 'https://cdn.hstatic.net/products/1000253775/160_somi_360-1_e5fbdaa2f39f42009002d54b619bdcf9_1024x1024.jpg'),
(14, 24, 'https://cdn.hstatic.net/products/1000253775/160_somi_360-1_e5fbdaa2f39f42009002d54b619bdcf9_1024x1024.jpg'),
(15, 25, 'https://cdn.hstatic.net/products/1000253775/160_somi_360-1_e5fbdaa2f39f42009002d54b619bdcf9_1024x1024.jpg'),
(16, 26, 'https://cdn.hstatic.net/products/1000253775/160_somi_360-1_e5fbdaa2f39f42009002d54b619bdcf9_1024x1024.jpg'),
(19, 27, 'https://th.bing.com/th/id/OIP.d1xMIytU4hBjm33WD1h2oQHaHa?w=210&h=210&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(20, 28, 'https://th.bing.com/th/id/OIP.d1xMIytU4hBjm33WD1h2oQHaHa?w=210&h=210&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(21, 29, 'https://th.bing.com/th/id/OIP.d1xMIytU4hBjm33WD1h2oQHaHa?w=210&h=210&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(22, 30, 'https://th.bing.com/th/id/OIP.d1xMIytU4hBjm33WD1h2oQHaHa?w=210&h=210&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(23, 31, 'https://th.bing.com/th/id/OIP.d1xMIytU4hBjm33WD1h2oQHaHa?w=210&h=210&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(26, 32, 'https://th.bing.com/th/id/OIP.Gq4aO9d3pUFpnYpfdzgpOAHaHa?w=194&h=194&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(27, 33, 'https://th.bing.com/th/id/OIP.Ztrok6nZxNSIdUe35AiZuwHaHa?w=184&h=184&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(28, 34, 'https://th.bing.com/th/id/OIP.Ztrok6nZxNSIdUe35AiZuwHaHa?w=184&h=184&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(29, 35, 'https://th.bing.com/th/id/OIP.Ztrok6nZxNSIdUe35AiZuwHaHa?w=184&h=184&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(30, 36, 'https://th.bing.com/th/id/OIP.Ztrok6nZxNSIdUe35AiZuwHaHa?w=184&h=184&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(31, 37, 'https://th.bing.com/th/id/OIP.Ztrok6nZxNSIdUe35AiZuwHaHa?w=184&h=184&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(34, 38, 'https://th.bing.com/th/id/OIP._2cUz4pbuc6illn7qH0h4AHaHa?w=183&h=202&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(35, 39, 'https://th.bing.com/th/id/OIP.vMf7mM0aMQbje4syGBbrtwHaLI?w=133&h=200&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(36, 40, 'https://th.bing.com/th/id/OIP.aLHih1TUmMlFcjpOU2tD4AHaIM?w=182&h=202&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(37, 41, 'https://th.bing.com/th/id/OIP.i-tAl0I1_FR4u2m51jINzAHaJ3?w=208&h=277&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(38, 42, 'https://th.bing.com/th/id/OIP.i-tAl0I1_FR4u2m51jINzAHaJ3?w=208&h=277&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(39, 43, 'https://th.bing.com/th/id/OIP.i-tAl0I1_FR4u2m51jINzAHaJ3?w=208&h=277&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(40, 44, 'https://th.bing.com/th/id/OIP.i-tAl0I1_FR4u2m51jINzAHaJ3?w=208&h=277&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(41, 45, 'https://th.bing.com/th/id/OIP.i-tAl0I1_FR4u2m51jINzAHaJ3?w=208&h=277&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(44, 46, 'https://th.bing.com/th/id/OIP.-kGulOpATVROIlB5mr7HXQHaHa?w=177&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(45, 47, 'https://th.bing.com/th/id/OIP.-kGulOpATVROIlB5mr7HXQHaHa?w=177&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(46, 48, 'https://th.bing.com/th/id/OIP.-kGulOpATVROIlB5mr7HXQHaHa?w=177&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(47, 49, 'https://th.bing.com/th/id/OIP.-kGulOpATVROIlB5mr7HXQHaHa?w=177&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(51, 50, 'https://th.bing.com/th/id/OIP.a5wT3X4R-hAFGJINlrRIlQHaHa?w=187&h=187&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(52, 51, 'https://th.bing.com/th/id/OIP.a5wT3X4R-hAFGJINlrRIlQHaHa?w=187&h=187&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(53, 52, 'https://th.bing.com/th/id/OIP.a5wT3X4R-hAFGJINlrRIlQHaHa?w=187&h=187&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(54, 53, 'https://th.bing.com/th/id/OIP.a5wT3X4R-hAFGJINlrRIlQHaHa?w=187&h=187&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(58, 54, 'https://th.bing.com/th/id/OIP.cPUsnFSZcgY0MEZJt3YI1wHaHa?w=207&h=207&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(59, 55, 'https://th.bing.com/th/id/OIP.cPUsnFSZcgY0MEZJt3YI1wHaHa?w=207&h=207&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(60, 56, 'https://th.bing.com/th/id/OIP.cPUsnFSZcgY0MEZJt3YI1wHaHa?w=207&h=207&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(61, 57, 'https://th.bing.com/th/id/OIP.cPUsnFSZcgY0MEZJt3YI1wHaHa?w=207&h=207&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3'),
(65, 70, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS2e2fmN8UvGsKi9mkmUIDNcWTUw1X3j1XUCA&s'),
(66, 71, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS2e2fmN8UvGsKi9mkmUIDNcWTUw1X3j1XUCA&s'),
(67, 72, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS2e2fmN8UvGsKi9mkmUIDNcWTUw1X3j1XUCA&s'),
(68, 73, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS2e2fmN8UvGsKi9mkmUIDNcWTUw1X3j1XUCA&s'),
(72, 74, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS2e2fmN8UvGsKi9mkmUIDNcWTUw1X3j1XUCA&s'),
(73, 75, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS2e2fmN8UvGsKi9mkmUIDNcWTUw1X3j1XUCA&s'),
(74, 76, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS2e2fmN8UvGsKi9mkmUIDNcWTUw1X3j1XUCA&s'),
(75, 77, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS2e2fmN8UvGsKi9mkmUIDNcWTUw1X3j1XUCA&s'),
(76, 78, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS2e2fmN8UvGsKi9mkmUIDNcWTUw1X3j1XUCA&s'),
(77, 79, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS2e2fmN8UvGsKi9mkmUIDNcWTUw1X3j1XUCA&s');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
CREATE TABLE IF NOT EXISTS `product_variants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `color_id` int NOT NULL,
  `size_id` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_color_size` (`product_id`,`color_id`,`size_id`),
  KEY `idx_variant_product` (`product_id`),
  KEY `idx_variant_color` (`color_id`),
  KEY `idx_variant_size` (`size_id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `color_id`, `size_id`, `price`, `stock`, `created_at`, `updated_at`) VALUES
(17, 15, 13, 5, 150000.00, 10, '2025-11-29 07:26:34', '2025-11-29 07:26:34'),
(18, 15, 13, 6, 150000.00, 8, '2025-11-29 07:26:34', '2025-11-29 07:26:34'),
(19, 15, 14, 5, 155000.00, 12, '2025-11-29 07:26:34', '2025-11-29 07:26:34'),
(20, 15, 14, 6, 155000.00, 9, '2025-11-29 07:26:34', '2025-11-29 07:26:34'),
(21, 15, 15, 7, 160000.00, 6, '2025-11-29 07:26:35', '2025-11-29 07:26:35'),
(22, 16, 14, 5, 220000.00, 12, '2025-11-29 07:26:35', '2025-11-29 07:26:35'),
(23, 16, 14, 6, 220000.00, 10, '2025-11-29 07:26:35', '2025-11-29 07:26:35'),
(24, 16, 16, 5, 225000.00, 8, '2025-11-29 07:26:35', '2025-11-29 07:26:35'),
(25, 16, 16, 6, 225000.00, 7, '2025-11-29 07:26:35', '2025-11-29 07:26:35'),
(26, 16, 13, 7, 230000.00, 5, '2025-11-29 07:26:35', '2025-11-29 07:26:35'),
(27, 17, 17, 5, 350000.00, 7, '2025-11-29 07:26:35', '2025-11-29 07:26:35'),
(28, 17, 17, 6, 350000.00, 6, '2025-11-29 07:26:35', '2025-11-29 07:26:35'),
(29, 17, 13, 5, 360000.00, 5, '2025-11-29 07:26:35', '2025-11-29 07:26:35'),
(30, 17, 13, 6, 360000.00, 4, '2025-11-29 07:26:35', '2025-11-29 07:26:35'),
(31, 17, 18, 7, 370000.00, 3, '2025-11-29 07:26:35', '2025-11-29 07:26:35'),
(32, 18, 18, 6, 180000.00, 12, '2025-11-29 07:26:35', '2025-11-29 07:26:35'),
(33, 25, 19, 5, 300000.00, 12, '2025-11-29 10:17:53', '2025-11-29 10:17:53'),
(34, 25, 19, 6, 300000.00, 10, '2025-11-29 10:17:53', '2025-11-29 10:17:53'),
(35, 25, 16, 5, 310000.00, 8, '2025-11-29 10:17:53', '2025-11-29 10:17:53'),
(36, 25, 16, 6, 310000.00, 7, '2025-11-29 10:17:53', '2025-11-29 10:17:53'),
(37, 25, 13, 7, 320000.00, 5, '2025-11-29 10:17:53', '2025-11-29 10:17:53'),
(38, 27, 20, 5, 180000.00, 15, '2025-11-29 10:20:14', '2025-11-29 10:20:14'),
(39, 28, 21, 5, 150000.00, 10, '2025-11-29 10:20:39', '2025-11-29 10:20:39'),
(40, 29, 14, 6, 220000.00, 7, '2025-11-29 10:20:51', '2025-11-29 10:20:51'),
(41, 30, 22, 8, 350000.00, 5, '2025-11-29 10:21:25', '2025-11-29 10:21:25'),
(42, 30, 22, 5, 350000.00, 7, '2025-11-29 10:21:25', '2025-11-29 10:21:25'),
(43, 30, 22, 6, 350000.00, 6, '2025-11-29 10:21:25', '2025-11-29 10:21:25'),
(44, 30, 13, 5, 360000.00, 4, '2025-11-29 10:21:25', '2025-11-29 10:21:25'),
(45, 30, 15, 6, 365000.00, 3, '2025-11-29 10:21:25', '2025-11-29 10:21:25'),
(46, 31, 16, 8, 300000.00, 10, '2025-11-29 10:25:01', '2025-11-29 10:25:01'),
(47, 31, 16, 5, 300000.00, 8, '2025-11-29 10:25:01', '2025-11-29 10:25:01'),
(48, 31, 16, 6, 305000.00, 6, '2025-11-29 10:25:01', '2025-11-29 10:25:01'),
(49, 31, 13, 5, 310000.00, 5, '2025-11-29 10:25:01', '2025-11-29 10:25:01'),
(50, 32, 13, 8, 250000.00, 8, '2025-11-29 10:25:14', '2025-11-29 10:25:14'),
(51, 32, 13, 5, 250000.00, 7, '2025-11-29 10:25:14', '2025-11-29 10:25:14'),
(52, 32, 13, 6, 255000.00, 5, '2025-11-29 10:25:14', '2025-11-29 10:25:14'),
(53, 32, 18, 5, 260000.00, 4, '2025-11-29 10:25:14', '2025-11-29 10:25:14'),
(54, 33, 23, 8, 350000.00, 5, '2025-11-29 10:25:28', '2025-11-29 10:25:28'),
(55, 33, 23, 5, 350000.00, 4, '2025-11-29 10:25:28', '2025-11-29 10:25:28'),
(56, 33, 23, 6, 355000.00, 3, '2025-11-29 10:25:28', '2025-11-29 10:25:28'),
(57, 33, 13, 5, 360000.00, 2, '2025-11-29 10:25:28', '2025-11-29 10:25:28'),
(58, 34, 13, 8, 250000.00, 8, '2025-11-29 10:25:43', '2025-11-29 10:25:43'),
(59, 34, 13, 5, 250000.00, 7, '2025-11-29 10:25:43', '2025-11-29 10:25:43'),
(60, 34, 13, 6, 255000.00, 5, '2025-11-29 10:25:43', '2025-11-29 10:25:43'),
(61, 34, 18, 5, 260000.00, 4, '2025-11-29 10:25:43', '2025-11-29 10:25:43'),
(70, 35, 13, 5, 250000.00, 8, '2025-11-29 10:29:26', '2025-11-29 10:29:26'),
(71, 35, 13, 6, 250000.00, 6, '2025-11-29 10:29:26', '2025-11-29 10:29:26'),
(72, 35, 18, 5, 255000.00, 5, '2025-11-29 10:29:26', '2025-11-29 10:29:26'),
(73, 35, 18, 6, 255000.00, 4, '2025-11-29 10:29:26', '2025-11-29 10:29:26'),
(74, 36, 13, 8, 150000.00, 15, '2025-11-29 10:29:38', '2025-11-29 10:29:38'),
(75, 36, 13, 5, 150000.00, 12, '2025-11-29 10:29:38', '2025-11-29 10:29:38'),
(76, 36, 14, 5, 150000.00, 10, '2025-11-29 10:29:38', '2025-11-29 10:29:38'),
(77, 36, 14, 6, 150000.00, 8, '2025-11-29 10:29:38', '2025-11-29 10:29:38'),
(78, 36, 17, 5, 155000.00, 6, '2025-11-29 10:29:38', '2025-11-29 10:29:38'),
(79, 36, 17, 6, 155000.00, 5, '2025-11-29 10:29:38', '2025-11-29 10:29:38');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_providers`
--

DROP TABLE IF EXISTS `shipping_providers`;
CREATE TABLE IF NOT EXISTS `shipping_providers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `price` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

DROP TABLE IF EXISTS `sizes`;
CREATE TABLE IF NOT EXISTS `sizes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`id`, `name`) VALUES
(5, 'M'),
(6, 'L'),
(7, 'XL'),
(8, 'S');

-- --------------------------------------------------------

--
-- Table structure for table `subcategory`
--

DROP TABLE IF EXISTS `subcategory`;
CREATE TABLE IF NOT EXISTS `subcategory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `category_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_category` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `subcategory`
--

INSERT INTO `subcategory` (`id`, `name`, `category_id`) VALUES
(2, 'ÁO THUN', 4),
(3, 'ÁO SƠ MI', 4),
(4, 'ÁO KHOÁC', 4),
(5, 'ÁO POLO', 4),
(6, 'QUẦN JEAN', 7),
(7, 'QUẦN TÂY', 7),
(8, 'QUẦN SHORT', 7),
(11, 'ĐẦM', 8),
(12, 'VÁY', 9),
(13, 'QUẦN THUN', 4);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(250) NOT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(250) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `username`, `phone`, `role`, `status`, `created_at`) VALUES
(5, 'trungnguyen@gmail.com', '$2y$10$.kSIS9Mw9Xx04mXvOlAsQOslHmpbvCTBJgQyGwtTbIlJFkXECx3qK', 'trungnguyen', '0123456789', 'user', 'active', '2025-11-23 17:07:27'),
(8, 'admin@example.com', '$2y$12$V0zFswcV75.1kH6uRSD7UuPX.nAH7ixTzDd6Qd8umm4Nie./5HFla', 'admin', NULL, 'admin', 'active', '2025-11-23 17:19:34'),
(10, 'haha@gmail.com', '$2y$10$LebVrLLXyiZSZvhlZ/XyF.S/ApxTJbMA3UmDFwj4yjQsfQxzoNPXy', 'toilaai1133', '0337629844', 'user', 'active', '2025-11-25 23:34:13'),
(11, 'nguyenchithinh@gmail.com', '$2y$10$CWoBf5CzBoqJwSESUnf7EutqjtDBlAYJOLiOTUw1F/O3aaCSmqRpe', 'chithinh', '0337629844', 'user', 'active', '2025-11-28 11:33:07');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

DROP TABLE IF EXISTS `user_addresses`;
CREATE TABLE IF NOT EXISTS `user_addresses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_address_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `user_id`, `address`, `phone`, `is_default`) VALUES
(1, 10, 'Bình chánh tp Hồ Chí Minh', '0337269844', 0),
(2, 11, 'trường đại học công nghệ sài gòn', '021922323', 0);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `fk_carts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_items_cart` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cart_items_variant` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_shipping` FOREIGN KEY (`shipping_id`) REFERENCES `shipping_providers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_variant` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_subcategory` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategory` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_product_image_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `fk_variant_color` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_variant_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_variant_size` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `subcategory`
--
ALTER TABLE `subcategory`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `fk_address_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
