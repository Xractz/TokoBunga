-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 16, 2025 at 02:24 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tokobunga`
--
DROP DATABASE IF EXISTS `tokobunga`;
CREATE DATABASE IF NOT EXISTS `tokobunga` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `tokobunga`;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(56, 4, 61, 1, '2025-12-16 13:01:26', '2025-12-16 13:01:26');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `order_code` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('pending','confirmed','processing','shipped','completed','cancelled') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','paid','refunded') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'unpaid',
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `recipient_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `recipient_phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `shipping_address` text COLLATE utf8mb4_general_ci NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `delivery_time` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `card_message` text COLLATE utf8mb4_general_ci,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_code`, `status`, `payment_status`, `payment_method`, `recipient_name`, `recipient_phone`, `shipping_address`, `delivery_date`, `delivery_time`, `card_message`, `subtotal`, `shipping_cost`, `grand_total`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(40, 4, 'ORD-20251212-131003-4ECF', 'completed', 'paid', 'qris', 'Samuel Christaura Geraldy', '087722002409', 'Pokoh RT 03 RW 04 Wonoboyo Wonogiri, Wonogiri, Jawa Tengah, 57615', '2025-12-12', '20:09', 'HBD REN', '30000.00', '25000.00', '55000.00', '-7.9620507', '110.9575733', '2025-12-12 20:10:03', '2025-12-12 20:24:10'),
(41, 4, 'ORD-20251212-131441-2630', 'pending', 'paid', 'qris', 'Ching Chong', '0987651234', 'ad, afd, adf, 12345', '2025-12-12', '20:11', 'sdfghjkl', '590000.00', '25000.00', '615000.00', '-7.7759559', '110.4161991', '2025-12-12 20:14:41', '2025-12-13 17:35:37'),
(42, 4, 'ORD-20251212-131950-90B4', 'processing', 'paid', 'qris', 'William', '08953247780', 'Dirgantara II/14 Babarsari Yogyakarta, Yogyakarta, Daerah Istimewa Yogyakarta, 55281', '2025-12-12', '20:18', 'Happy birtdayy my lovee luvvvluvvv', '60000.00', '25000.00', '85000.00', '-7.8012646', '110.3646857', '2025-12-12 20:19:50', '2025-12-12 20:20:01'),
(43, 4, 'ORD-20251212-132105-D696', 'processing', 'paid', 'qris', 'Ching Chong', '0987651234', 'ad, afd, adf, 12345', '2025-12-12', '20:21', '', '4200000.00', '25000.00', '4225000.00', '-7.7759762', '110.4162455', '2025-12-12 20:21:05', '2025-12-12 20:21:12'),
(44, 4, 'ORD-20251212-132751-DB76', 'processing', 'paid', 'qris', 'jondro', '8445654343', 'Idk, Samarimda, Aceh, 62834', '2025-12-12', '20:27', '', '16800000.00', '25000.00', '16825000.00', '-7.7758464', '110.4052224', '2025-12-12 20:27:51', '2025-12-12 20:27:56'),
(45, 4, 'ORD-20251212-133541-725C', 'processing', 'paid', 'qris', 'Cristensen Rendra Palinggi', '08218431541', 'JL.SELUANG,RT8 YOSODADI, METRO TIMUR, METRO, Lampung, 34111', '2025-12-12', '20:35', 'semoga bahagia', '2400000.00', '25000.00', '2425000.00', '-5.1078839', '105.3078642', '2025-12-12 20:35:41', '2025-12-12 20:35:56'),
(46, 4, 'ORD-20251215-122125-0D09', 'completed', 'paid', 'qris', 'Samuel Christaura Geraldy', '087722002409', 'Pokoh RT 03 RW 04 Wonoboyo Wonogiri, Wonogiri, Jawa Tengah, 57615', '2025-12-15', '19:20', 'Thanks Willy my Love from nune, mwahhh', '8400000.00', '25000.00', '8425000.00', '-7.8989106', '110.8963012', '2025-12-15 19:21:25', '2025-12-15 19:33:52'),
(47, 4, 'ORD-20251215-124701-A423', 'processing', 'paid', 'qris', 'Ching Chong', '0987651234', 'ad, afd, adf, 12345', '2025-12-15', '19:44', 'xfcgvhbjnk', '4200000.00', '25000.00', '4225000.00', '-7.7885632', '110.3692116', '2025-12-15 19:47:01', '2025-12-15 19:47:49'),
(48, 4, 'ORD-20251215-130804-1D6E', 'pending', 'unpaid', 'qris', 'William', '08953247780', 'Dirgantara II/14 Babarsari Yogyakarta, Yogyakarta, Daerah Istimewa Yogyakarta, 55281', '2025-12-15', '20:07', 'Happy', '6600000.00', '25000.00', '6625000.00', '-7.8012646', '110.3646857', '2025-12-15 20:08:04', '2025-12-15 20:08:04'),
(49, 4, 'ORD-20251215-130852-9622', 'processing', 'paid', 'qris', 'William', '08953247780', 'Dirgantara II/14 Babarsari Yogyakarta, Yogyakarta, Daerah Istimewa Yogyakarta, 55281', '2025-12-15', '20:07', 'Happy', '8400000.00', '25000.00', '8425000.00', '-7.7885632', '110.3692116', '2025-12-15 20:08:52', '2025-12-15 20:13:12'),
(51, 4, 'ORD-20251215-134400-D21C', 'processing', 'paid', 'qris', 'William', '08953247780', 'Dirgantara II/14 Babarsari Yogyakarta, Yogyakarta, Daerah Istimewa Yogyakarta, 55281', '2025-12-15', '20:43', '', '15000000.00', '25000.00', '15025000.00', '-7.7885632', '110.3692116', '2025-12-15 20:44:00', '2025-12-15 20:44:07'),
(53, 20, 'ORD-20251215-141043-4A53', 'shipped', 'paid', 'qris', 'Samuel Christaura Geraldy', '087722002409', 'Pokoh RT 03 RW 04 Wonoboyo Wonogiri, Wonogiri, Jawa Tengah, 57615', '2025-12-15', '21:10', 'Hi', '8490000.00', '25000.00', '8515000.00', '-7.9053282', '110.8940376', '2025-12-15 21:10:43', '2025-12-15 21:23:54'),
(54, 4, 'ORD-20251215-175535-7AD4', 'processing', 'paid', 'qris', 'MichelleGab', '02218882006', 'Jalan Babarsari No.43, Janti, Caturtunggal, Kec. Depok, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55281, Indonesia, Sleman, Depok, Yogyakarta, 55281', '2025-12-16', '00:54', '', '30000.00', '25000.00', '55000.00', '-7.7955532', '110.4013725', '2025-12-16 00:55:35', '2025-12-16 00:55:47');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `unit_price`, `quantity`, `subtotal`, `created_at`) VALUES
(28, 40, 55, 'Single Garbera Wrap', '30000.00', 1, '30000.00', '2025-12-12 20:10:03'),
(29, 41, 16, 'Anemone White & Black', '590000.00', 1, '590000.00', '2025-12-12 20:14:41'),
(30, 42, 61, 'Bloomify Ultimate Luxury Arrangement', '4200000.00', 1, '4200000.00', '2025-12-12 20:19:50'),
(31, 43, 61, 'Bloomify Ultimate Luxury Arrangement', '4200000.00', 1, '4200000.00', '2025-12-12 20:21:05'),
(32, 44, 61, 'Bloomify Ultimate Luxury Arrangement', '4200000.00', 4, '16800000.00', '2025-12-12 20:27:51'),
(33, 45, 59, 'Luxury Rose Grand Bouquet', '2400000.00', 1, '2400000.00', '2025-12-12 20:35:41'),
(34, 46, 61, 'Bloomify Ultimate Luxury Arrangement', '4200000.00', 2, '8400000.00', '2025-12-15 19:21:25'),
(35, 47, 61, 'Bloomify Ultimate Luxury Arrangement', '4200000.00', 1, '4200000.00', '2025-12-15 19:47:01'),
(36, 48, 59, 'Luxury Rose Grand Bouquet', '2400000.00', 1, '2400000.00', '2025-12-15 20:08:04'),
(37, 48, 61, 'Bloomify Ultimate Luxury Arrangement', '4200000.00', 3, '12600000.00', '2025-12-15 20:08:04'),
(38, 49, 59, 'Luxury Rose Grand Bouquet', '2400000.00', 1, '2400000.00', '2025-12-15 20:08:52'),
(39, 49, 61, 'Bloomify Ultimate Luxury Arrangement', '4200000.00', 2, '8400000.00', '2025-12-15 20:08:52'),
(42, 51, 59, 'Luxury Rose Grand Bouquet', '2400000.00', 1, '2400000.00', '2025-12-15 20:44:00'),
(43, 51, 61, 'Bloomify Ultimate Luxury Arrangement', '4200000.00', 3, '12600000.00', '2025-12-15 20:44:00'),
(45, 53, 56, 'Single Rose Wrap', '30000.00', 3, '90000.00', '2025-12-15 21:10:43'),
(46, 53, 61, 'Bloomify Ultimate Luxury Arrangement', '4200000.00', 2, '8400000.00', '2025-12-15 21:10:43'),
(47, 54, 55, 'Single Garbera Wrap', '30000.00', 1, '30000.00', '2025-12-16 00:55:35');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `category_id` int DEFAULT NULL,
  `name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `stock`, `image`, `is_active`, `created_at`, `updated_at`) VALUES
(10, 13, 'Garden Rose Bouquet', 'garden-rose-bouquet', 'Buket mawar merah segar dengan sentuhan bunga pink lembut dan wrapping elegan. Cocok untuk anniversary, ulang tahun, atau ungkapan cinta yang klasik.', '450000.00', 12, 'garden-rose-bouquet.jpg', 1, '2025-12-12 16:47:29', '2025-12-12 16:47:29'),
(11, 19, 'Peony Blush Bouquet', 'peony-blush-bouquet', 'Buket peony blush pink yang hanya tersedia di musim semi, dikenal dengan kelopaknya yang besar dan tampilan mewah.', '1150000.00', 10, 'peony-blush-bouquet.jpg', 1, '2025-12-12 16:54:30', '2025-12-12 17:04:15'),
(12, 19, 'White Peony Bouquet', 'white-peony-bouquet', 'Peony putih musiman dengan kesan bersih dan elegan, cocok untuk hadiah eksklusif.', '880000.00', 10, 'white-peony-bouquet.jpg', 1, '2025-12-12 16:56:14', '2025-12-12 17:04:04'),
(13, 19, 'Spring Mix Tulip', 'spring-mix-tulip', 'Rangkaian tulip warna-warni yang hanya tersedia saat musim semi.', '650000.00', 10, 'spring-mix-tulip.jpg', 1, '2025-12-12 17:03:52', '2025-12-12 17:04:41'),
(14, 19, 'Classic Pink Tulip', 'classic-pink-tulip', 'Buket tulip merah muda cerah dengan tampilan minimalis dan modern.', '750000.00', 10, 'classic-pink-tulip.jpg', 1, '2025-12-12 17:05:42', '2025-12-12 17:05:42'),
(15, 19, 'Ranunculus Pastel Bouquet', 'ranunculus-pastel-bouquet', 'Bunga ranunculus berlapis lembut dengan warna pastel yang hanya tersedia di awal tahun.', '550000.00', 10, 'ranunculus-pastel-bouquet.jpg', 1, '2025-12-12 17:06:48', '2025-12-12 17:06:48'),
(16, 19, 'Anemone White & Black', 'anemone-white-black', 'Anemone White & BlackAnemone musiman dengan pusat hitam yang unik dan kontras.', '590000.00', 9, 'anemone-white-black.jpg', 1, '2025-12-12 17:07:29', '2025-12-12 20:14:41'),
(17, 18, 'Luxury Rose Flower Box', 'luxury-rose-flower-box', 'Mawar merah premium dalam box elegan untuk hadiah romantis.', '750000.00', 10, 'luxury-rose-flower-box.jpg', 1, '2025-12-12 17:12:04', '2025-12-12 17:12:04'),
(18, 18, 'Pastel Bloom Box', 'pastel-bloom-box', 'Flower box dengan kombinasi bunga pastel lembut dan feminin.', '450000.00', 10, 'pastel-bloom-box.jpg', 1, '2025-12-12 17:12:48', '2025-12-12 17:12:48'),
(19, 18, 'White Harmony Flower Box', 'white-harmony-flower-box', 'Kombinasi bunga putih yang menenangkan dan bersih.', '600000.00', 10, 'white-harmony-flower-box.jpg', 1, '2025-12-12 17:13:30', '2025-12-12 17:13:30'),
(20, 18, 'Orchid Flower Box', 'orchid-flower-box', 'Anggrek eksotis dalam box eksklusif.', '880000.00', 10, 'orchid-flower-box.jpg', 1, '2025-12-12 17:14:20', '2025-12-12 17:14:20'),
(21, 17, 'Bridal White Rose Bouquet', 'bridal-white-rose-bouquet', 'Mawar putih klasik untuk pengantin wanita.', '400000.00', 10, 'bridal-white-rose-bouquet.jpg', 1, '2025-12-12 17:18:29', '2025-12-12 17:18:29'),
(22, 17, 'Rustic Wedding Bouquet', 'rustic-wedding-bouquet', 'Buket natural dengan sentuhan rustic.', '500000.00', 10, 'rustic-wedding-bouquet.jpg', 1, '2025-12-12 17:19:08', '2025-12-12 17:19:08'),
(23, 17, 'Elegant Peony Bridal', 'elegant-peony-bridal', 'Peony mewah untuk tampilan pengantin elegan.', '750000.00', 10, 'elegant-peony-brida.jpg', 1, '2025-12-12 17:19:42', '2025-12-12 17:28:25'),
(24, 16, 'Thumbelina Bouquet', 'thumbelina-bouquet', 'Produk eksklusif edisi terbaru Bloomify.', '700000.00', 10, 'thumbelina-bouquet.jpg', 1, '2025-12-12 17:22:42', '2025-12-12 17:22:42'),
(25, 16, 'Lavender Lisianthus Bouquet', 'lavender-lisianthus-bouquet', 'Lisianthus ungu lavender dengan bentuk bunga menyerupai mawar, memberikan kesan lembut, elegan, dan romantis.', '510000.00', 10, 'lavender-lisianthus-bouquet.jpg', 1, '2025-12-12 17:25:32', '2025-12-12 17:25:32'),
(26, 16, 'Yellow Freesia Garden Bouquet', 'yellow-freesia-garden-bouquet', 'Buket freesia kuning cerah dengan aroma lembut yang menyegarkan, cocok untuk hadiah penuh semangat dan kebahagiaan.', '600000.00', 10, 'yellow-freesia-garden-bouquet.jpg', 1, '2025-12-12 17:26:36', '2025-12-12 17:26:36'),
(27, 17, 'Calla Lily Ivory Elegance', 'calla-lily-ivory-elegance', 'Buket Calla Lily putih gading dengan bentuk ramping dan modern. Memberikan kesan minimalis, mewah, dan sophisticated untuk pernikahan.', '730000.00', 10, 'calla-lily-ivory-elegance.jpg', 1, '2025-12-12 17:31:57', '2025-12-12 17:31:57'),
(29, 19, 'Lily of the Valley Signature Bouquet', 'lily-of-the-valley-signature-bouquet', 'Rangkaian Lily of the Valley yang langka dengan bunga kecil berwarna putih dan aroma lembut yang ikonik. Melambangkan kemurnian, kebahagiaan, dan cinta yang tulus. Sangat eksklusif dan limited.', '620000.00', 10, 'lily-of-the-valley-signature-bouquet.jpg', 1, '2025-12-12 17:33:58', '2025-12-12 17:33:58'),
(30, 14, 'Elegant Lily Arrangement', 'elegant-lily-arrangement', 'Lily putih dalam susunan elegan dalam vase.', '490000.00', 10, 'elegant-lily-arrangement.jpg', 1, '2025-12-12 17:38:44', '2025-12-12 17:38:44'),
(31, 14, 'Modern Table Bloom', 'modern-table-bloom', 'Rangkaian bunga-bunga indah dengan nuansa modern dan menenangkan.', '500000.00', 10, 'modern-table-bloom.jpg', 1, '2025-12-12 17:40:36', '2025-12-12 17:40:36'),
(32, 14, 'Office Desk Flower', 'office-desk-flower', 'Dekorasi bunga kecil yang cocok untuk meja kerja.', '170000.00', 10, 'office-desk-flower.jpg', 1, '2025-12-12 17:43:18', '2025-12-12 17:43:18'),
(33, 14, 'Classic Rose Vase', 'classic-rose-vase', 'Mawar klasik yang disusun dengan tambahan nuansa hijau dalam vas.', '350000.00', 20, 'classic-rose-vase.jpg', 1, '2025-12-12 17:44:20', '2025-12-12 17:44:20'),
(34, 13, 'Sunflower Bright Day Bouquet', 'sunflower-bright-day-bouquet', 'Sunflower cerah yang melambangkan kebahagiaan, optimisme, dan energi positif.', '480000.00', 10, 'sunflower-bright-day-bouquet.jpg', 1, '2025-12-12 18:00:16', '2025-12-12 18:00:16'),
(35, 13, 'Baby Breath Cloud Bouquet', 'baby-breath-cloud-bouquet', 'Buket penuh babyâ€™s breath dengan tampilan airy, simpel, dan aesthetic.', '420000.00', 10, 'baby-breath-cloud-bouquet.jpg', 1, '2025-12-12 18:01:28', '2025-12-12 18:01:28'),
(36, 13, 'Hydrangea Blue Classic Bouquet', 'hydrangea-blue-classic-bouquet', 'Hydrangea biru dipadukan dengan nuansa hijau dan putih dengan volume penuh dan tampilan elegan yang menenangkan.', '560000.00', 10, 'hydrangea-blue-classic-bouquet.jpg', 1, '2025-12-12 18:02:29', '2025-12-12 18:02:29'),
(37, 13, 'White Lily Grace Bouquet', 'white-lily-grace-bouquet', 'Lily putih beraroma lembut dengan kesan anggun dan penuh ketenangan.', '540000.00', 10, 'white-lily-grace-bouquet.jpg', 1, '2025-12-12 18:03:12', '2025-12-12 18:03:12'),
(38, 13, 'Carnation Pastel Charm Bouquet', 'carnation-pastel-charm-bouquet', 'Carnation pastel lembut yang melambangkan kasih sayang dan ketulusan.', '490000.00', 10, 'carnation-pastel-charm-bouquet.jpg', 1, '2025-12-12 18:03:53', '2025-12-12 18:03:53'),
(39, 13, 'Rustic Wild Mix Bouquet', 'rustic-wild-mix-bouquet', 'Campuran bunga non-musiman dengan gaya rustic dan natural.', '420000.00', 10, 'rustic-wild-mix-bouquet.jpg', 1, '2025-12-12 18:04:47', '2025-12-12 18:04:47'),
(40, 13, 'Pink Gerbera Joy Bouquet', 'pink-gerbera-joy-bouquet', 'Gerbera pink cerah dengan nuansa playful dan penuh semangat.', '440000.00', 10, 'pink-gerbera-joy-bouquet.jpg', 1, '2025-12-12 18:06:02', '2025-12-12 18:06:02'),
(41, 13, 'Mixed Gerbera Colorful Bouquet', 'mixed-gerbera-colorful-bouquet', 'Perpaduan gerbera warna-warni yang ceria dan menyenangkan.', '450000.00', 10, 'mixed-gerbera-colorful-bouquet.jpg', 1, '2025-12-12 18:07:45', '2025-12-12 18:07:45'),
(42, 13, 'Rose & Chrysanthemum Harmony', 'rose-chrysanthemum-harmony', 'Kombinasi mawar dan chrysanthemum yang seimbang dan tahan lama.', '470000.00', 10, 'rose-chrysanthemum-harmony.jpg', 1, '2025-12-12 18:08:36', '2025-12-12 18:08:36'),
(43, 13, 'Purple Enchantment Bouquet', 'purple-enchantment-bouquet', 'Perpaduan bunga ungu dengan kesan elegan dan indah.', '530000.00', 10, 'purple-enchantment-bouquet.jpg', 1, '2025-12-12 18:09:42', '2025-12-12 18:09:42'),
(44, 13, 'White Chrysanthemum Serenity Bouquet', 'white-chrysanthemum-serenity-bouquet', 'Chrysanthemum putih yang rapi dan menenangkan untuk berbagai momen.', '340000.00', 10, 'white-chrysanthemum-serenity-bouquet.jpg', 1, '2025-12-12 18:11:10', '2025-12-12 18:11:10'),
(45, 13, 'Soft Pink Rose Bouquet', 'soft-pink-rose-bouquet', 'Mawar pink lembut yang menghadirkan kesan manis, hangat, dan penuh perhatian.', '470000.00', 10, 'soft-pink-rose-bouquet.jpg', 1, '2025-12-12 18:21:34', '2025-12-12 18:21:34'),
(46, 13, 'Pink Lily Soft Bloom Bouquet', 'pink-lily-soft-bloom-bouquet', 'Lily pink dengan kelopak besar yang menghadirkan kesan feminin dan elegan.', '680000.00', 10, 'pink-lily-soft-bloom-bouquet.jpg', 1, '2025-12-12 18:22:32', '2025-12-12 18:22:32'),
(47, 13, 'Red & White Carnation Bouquet', 'red-white-carnation-bouquet', 'Kombinasi carnation merah dan putih untuk tampilan klasik dan seimbang.', '490000.00', 10, 'red-white-carnation-bouquet.jpg', 1, '2025-12-12 18:25:02', '2025-12-12 18:25:02'),
(48, 13, 'Mini Red Rose Bouquet', 'mini-red-rose-bouquet', 'Buket kecil mawar merah dengan wrapping sederhana, cocok untuk hadiah romantis yang praktis.', '170000.00', 20, 'mini-red-rose-bouquet.jpg', 1, '2025-12-12 18:32:15', '2025-12-12 18:32:15'),
(49, 13, 'Pink Rose Hand Bouquet', 'pink-rose-hand-bouquet', 'Rangkaian mawar pink lembut dengan ukuran compact untuk hadiah manis dan hangat.', '180000.00', 20, 'pink-rose-hand-bouquet.jpg', 1, '2025-12-12 18:32:56', '2025-12-12 18:32:56'),
(50, 13, 'Sunflower Single Stem Bouquet', 'sunflower-single-stem-bouquet', 'Sunflower satu tangkai dengan wrapping minimalis yang ceria dan penuh makna.', '80000.00', 20, 'sunflower-single-stem-bouquet.jpg', 1, '2025-12-12 18:33:40', '2025-12-12 18:33:40'),
(51, 13, 'Yellow Rose Simple Bouquet', 'yellow-rose-simple-bouquet', 'Mawar kuning cerah dengan wrapping simpel, melambangkan persahabatan dan kebahagiaan.', '180000.00', 20, 'yellow-rose-simple-bouquet.jpg', 1, '2025-12-12 18:34:44', '2025-12-12 18:34:44'),
(52, 13, 'Gerbera Bright Mini Bouquet', 'gerbera-bright-mini-bouquet', 'Gerbera warna cerah dalam ukuran mini yang memberikan kesan ceria dan youthful.', '80000.00', 20, 'gerbera-bright-mini-bouquet.jpg', 1, '2025-12-12 18:35:46', '2025-12-12 18:35:46'),
(53, 13, 'White Daisy Simple Bouquet', 'white-daisy-simple-bouquet', 'Buket daisy putih sederhana dengan nuansa bersih dan natural.', '100000.00', 20, 'white-daisy-simple-bouquet.jpg', 1, '2025-12-12 18:36:28', '2025-12-12 18:36:28'),
(54, 13, 'Mini Chrysanthemum Bouquet', 'mini-chrysanthemum-bouquet', 'Rangkaian chrysanthemum kecil dengan tampilan rapi dan elegan.', '90000.00', 20, 'mini-chrysanthemum-bouquet.jpg', 1, '2025-12-12 18:37:47', '2025-12-12 18:37:47'),
(55, 13, 'Single Garbera Wrap', 'single-garbera-wrap', 'Satu tangkai bunga garbera dengan wrapping simpel.', '30000.00', 18, 'single-garbera-wrap.jpg', 1, '2025-12-12 18:39:56', '2025-12-16 00:55:35'),
(56, 13, 'Single Rose Wrap', 'single-rose-wrap', 'Satu tangkai mawar dengan wrapping yang elegan dan indah.', '30000.00', 15, 'single-rose-wrap.jpg', 1, '2025-12-12 18:41:21', '2025-12-15 21:10:43'),
(57, 16, 'Ophelia Bouquet', 'ophelia-bouquet', 'Kombinasi bungan kuning yang cocok untuk hadiah yang ceria.', '290000.00', 10, 'ophelia-bouquet.jpg', 1, '2025-12-12 18:43:33', '2025-12-12 18:43:33'),
(58, 13, 'Luxury Lily Wrap', 'luxury-lily-wrap', 'Lily merah mudah berukuran besar dengan aroma lembut yang memberikan kesan bersih, mewah, dan penuh ketenangan.', '2100000.00', 50, 'luxury-lily-wrap.jpg', 1, '2025-12-12 18:47:13', '2025-12-13 18:24:41'),
(59, 13, 'Luxury Rose Grand Bouquet', 'luxury-rose-grand-bouquet', 'Rangkaian 100 tangkai mawar premium dengan wrapping eksklusif, simbol cinta megah dan pernyataan perasaan yang kuat.', '2400000.00', 37, 'luxury-rose-grand-bouquet.jpg', 1, '2025-12-12 18:53:49', '2025-12-15 20:44:00'),
(60, 19, 'Peony Luxury Wrap', 'peony-luxury-wrap', 'Signature bouquet Bloomify dengan komposisi bunga peony premium pilihan florist senior, eksklusif dan terbatas.', '3500000.00', 50, 'peony-luxury-wrap.jpg', 1, '2025-12-12 18:56:07', '2025-12-13 18:24:28'),
(61, 13, 'Bloomify Ultimate Luxury Arrangement', 'bloomify-ultimate-luxury-arrangement', 'Karya puncak Bloomify dengan bunga anggrek dan lily premium impor, desain eksklusif, dan pengerjaan custom.', '4200000.00', 145, 'bloomify-ultimate-luxury-arrangement.jpg', 1, '2025-12-12 19:01:47', '2025-12-15 21:10:43');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`, `slug`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(13, 'Bouquets', 'bouquets', 'Koleksi buket bunga segar yang dirangkai dengan penuh perhatian untuk berbagai momen spesial. Cocok sebagai hadiah personal maupun kejutan istimewa.', 1, '2025-12-12 16:34:52', '2025-12-12 16:39:56'),
(14, 'Flower Arrangements', 'flower-arrangements', 'Rangkaian bunga elegan yang disusun di dalam vas atau wadah premium. Memberikan sentuhan keindahan yang tahan lama untuk rumah, kantor, atau acara resmi.', 1, '2025-12-12 16:35:37', '2025-12-12 16:38:49'),
(16, 'New Arrivals', 'new-arrivals', 'Produk terbaru Bloomify dengan desain segar dan tren terkini untuk melengkapi koleksi bunga Anda.', 1, '2025-12-12 16:40:32', '2025-12-12 16:40:32'),
(17, 'Wedding Flowers', 'wedding-flowers', 'Rangkaian bunga romantis dan anggun untuk melengkapi momen pernikahan, baik sebagai buket pengantin maupun dekorasi pendukung.', 1, '2025-12-12 16:40:49', '2025-12-12 16:40:49'),
(18, 'Flower Boxes', 'flower-boxes', 'Buket bunga yang disajikan dalam box eksklusif dengan tampilan modern dan mewah. Pilihan sempurna untuk hadiah premium yang berkesan.', 1, '2025-12-12 16:41:14', '2025-12-12 16:41:14'),
(19, 'Seasonal Collection', 'seasonal-collection', 'Koleksi bunga tematik yang hadir mengikuti musim dan perayaan tertentu sepanjang tahun.', 1, '2025-12-12 16:41:40', '2025-12-12 16:41:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `reset_token` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `profile_photo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` enum('customer','admin') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'customer',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `activation_token` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `reset_token`, `reset_token_expires`, `username`, `password`, `phone`, `address`, `profile_photo`, `role`, `is_active`, `activation_token`, `created_at`, `updated_at`) VALUES
(3, 'admin', 'admin@gmail.com', NULL, NULL, 'admin', '$2y$10$gnlSLdVa82URIU0/dEFTwes1CFqgABtWxoQ80OtxxaUk7./n4x4M2', '081234567890', 'Jogja', NULL, 'admin', 1, NULL, '2025-12-03 19:23:31', '2025-12-12 13:02:33'),
(4, 'cust', 'cust@gmail.com', NULL, NULL, 'testuserupdate', '$2y$10$fSWJlfsnnS.ZYTaZy6q.eOSkzCaWXxPGYFSyWf1bRgTg6vjIpcwnu', '081234567890', 'Jogja', NULL, 'customer', 1, NULL, '2025-12-04 14:24:16', '2025-12-15 19:26:58'),
(7, 'ching chong', '230712375@students.uajy.ac.id', NULL, NULL, 'chingcc', '$2y$10$RQBrjczS6UURCL4ONJ7W/efEP0SZWbkglombKr0e.1GVerm513tjC', '09876543211', NULL, NULL, 'customer', 1, NULL, '2025-12-12 21:30:53', '2025-12-12 21:31:57'),
(15, 'ching chong', 'ssellymonica@outlook.com', NULL, NULL, 'chingccc', '$2y$10$ATS9aNeWq2YFhSZ5vAOghuKmHWTuvDYkx0gZbzE9AZsmJpNUbNDES', '1234567890', NULL, NULL, 'customer', 1, NULL, '2025-12-13 18:18:48', '2025-12-13 18:19:53'),
(17, 'Cristensen Rendra Palinggi', '230712384@students.uajy.ac.id', NULL, NULL, 'apa', '$2y$10$l1WsafYcj9xx0VZOu8VlLOm1/vPWZ8jJKun.2FeG/V.QDRqcjYNQS', '082184315415', NULL, NULL, 'customer', 1, NULL, '2025-12-15 20:12:01', '2025-12-15 21:16:24'),
(20, 'Samuel', 'sam.christg@gmail.com', NULL, NULL, 'samchristg', '$2y$10$pzgKKuWgUQTtcAWJC0E0w.St9KqWh2tWHUU0dRc/T76nUdQ8VLcV.', '087722002409', NULL, 'profile_69401721add2e4.43789562.jpg', 'customer', 1, NULL, '2025-12-15 21:07:33', '2025-12-15 21:11:45'),
(21, 'Michelle Gabriela Laquintania', 'michellelaquin01@gmail.com', NULL, NULL, 'michelle', '$2y$10$MbkwzpGZdisgeOV2dIl6M.KzBz1pnQTw6LZnIqCvKKZCDKiCZgDhG', '082218882006', NULL, NULL, 'customer', 0, 'df44ef3499625ea344f9d325eee12f9e6f85521a5739640dd5cfab9003a052b5', '2025-12-16 00:50:21', '2025-12-16 00:50:21'),
(22, 'Michelle Gabriela', 'michellelaquintania@gmail.com', NULL, NULL, 'mic', '$2y$10$cmIyXDUSBAVPnVY.dS/D9uAZ0j9C382pIM8E91zjBtZycwXXgHDBu', '082282837333', NULL, NULL, 'customer', 0, 'c07ce34f0c33b99a40efa686b4e23cebfab7545b72b3b32fb354519f66908217', '2025-12-16 00:53:35', '2025-12-16 00:53:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`product_id`),
  ADD KEY `fk_cart_product` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `fk_order_user` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_item_order` (`order_id`),
  ADD KEY `fk_order_item_product` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_products_category` (`category_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_item_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_item_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
