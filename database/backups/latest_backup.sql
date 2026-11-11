-- ========================================================
-- Full Database Dump from Aiven MySQL
-- Database: dyzzdb | Host: mysql-34f613a5-chetranicky-703c.d.aivencloud.com
-- Generated: 2026-11-11 04:41:18 UTC
-- ========================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8mb4;
SET time_zone = "+00:00";

-- Select target database
CREATE DATABASE IF NOT EXISTS `dyzzstore`;
USE `dyzzstore`;


-- --------------------------------------------------------
-- Table structure for table `audit_logs`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `user_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `changes` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_index` (`user_id`),
  KEY `audit_logs_user_email_index` (`user_email`),
  KEY `audit_logs_action_index` (`action`),
  KEY `audit_logs_entity_type_index` (`entity_type`),
  KEY `audit_logs_entity_id_index` (`entity_id`),
  KEY `audit_logs_device_id_index` (`device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `audit_logs`
INSERT INTO `audit_logs` (`id`, `user_id`, `user_email`, `action`, `entity_type`, `entity_id`, `entity_name`, `description`, `device_id`, `device_name`, `ip_address`, `user_agent`, `changes`, `created_at`, `updated_at`) VALUES
(1, NULL, 'chetranicky@gmail.com', 'UPDATE', 'Product', '18', 'Gemini Pro 18 month', 'Updated product \'Gemini Pro 18 month\'', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9711', '{\"after\": {\"name\": \"Gemini Pro 18 month\", \"status\": \"Active\", \"is_active\": true, \"price_monthly\": 2.8, \"price_lifetime\": 0}, \"before\": {\"name\": \"Gemini Pro 18 month\", \"status\": \"Active\", \"is_active\": true, \"price_monthly\": 2.8, \"price_lifetime\": 0}}', '2026-10-25 09:31:55', '2026-10-25 09:31:55'),
(2, NULL, 'chetranicky@gmail.com', 'UPDATE', 'Category', '3', 'Code & DevOps1', 'Updated category \'Code & DevOps1\'', 'unknown', 'Browser Device', '27.109.115.145', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"icon\": \"Code2\", \"name\": \"Code & DevOps1\", \"slug\": \"code\", \"badge\": \"Dev Suite\", \"color\": \"#06b6d4\", \"status\": \"Active\", \"is_active\": true, \"description\": \"Autonomous coding agents, unit test generators, and repository refactoring engines.\", \"display_order\": 3}', '2026-10-25 09:34:24', '2026-10-25 09:34:24'),
(3, NULL, 'chetranicky@gmail.com', 'SETTINGS_CHANGE', 'Setting', NULL, NULL, 'Updated platform settings & changed master admin password', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '27.109.115.145', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', NULL, '2026-10-25 09:35:29', '2026-10-25 09:35:29'),
(4, NULL, 'chetranicky@gmail.com', 'UPDATE', 'Product', '18', 'Gemini Pro 18 month', 'Updated product \'Gemini Pro 18 month\'', 'unknown', 'Browser Device', '27.109.115.145', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"after\": {\"name\": \"Gemini Pro 18 month\", \"status\": \"Active\", \"is_active\": true, \"price_monthly\": 2.2, \"price_lifetime\": 0}, \"before\": {\"name\": \"Gemini Pro 18 month\", \"status\": \"Active\", \"is_active\": true, \"price_monthly\": 2.8, \"price_lifetime\": 0}}', '2026-10-25 09:37:06', '2026-10-25 09:37:06'),
(5, NULL, 'chetranicky@gmail.com', 'UPDATE', 'Product', '18', 'Gemini Pro 18 month', 'Updated product \'Gemini Pro 18 month\'', 'unknown', 'Browser Device', '27.109.115.145', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"after\": {\"name\": \"Gemini Pro 18 month\", \"status\": \"Inactive\", \"is_active\": false, \"price_monthly\": 2.2, \"price_lifetime\": 199}, \"before\": {\"name\": \"Gemini Pro 18 month\", \"status\": \"Active\", \"is_active\": true, \"price_monthly\": 2.2, \"price_lifetime\": 0}}', '2026-10-25 10:57:29', '2026-10-25 10:57:29'),
(6, NULL, 'chetranicky@gmail.com', 'UPDATE', 'Product', '17', 'CapCut Pro 1 Month', 'Updated product \'CapCut Pro 1 Month\'', 'unknown', 'Browser Device', '27.109.115.145', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"after\": {\"name\": \"CapCut Pro 1 Month\", \"status\": \"Inactive\", \"is_active\": false, \"price_monthly\": 1.5, \"price_lifetime\": 199}, \"before\": {\"name\": \"CapCut Pro 1 Month\", \"status\": \"Active\", \"is_active\": true, \"price_monthly\": 1.5, \"price_lifetime\": 0}}', '2026-10-25 10:57:36', '2026-10-25 10:57:36'),
(7, NULL, 'chetranicky@gmail.com', 'UPDATE', 'Category', '3', 'Code & DevOps1', 'Updated category \'Code & DevOps1\'', 'unknown', 'Browser Device', '27.109.115.145', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '{\"icon\": \"Code2\", \"name\": \"Code & DevOps1\", \"slug\": \"code\", \"badge\": \"Dev Suite\", \"color\": \"#06b6d4\", \"status\": \"Inactive\", \"is_active\": false, \"description\": \"Autonomous coding agents, unit test generators, and repository refactoring engines.\", \"display_order\": 3}', '2026-10-25 11:33:47', '2026-10-25 11:33:47'),
(8, NULL, 'chetranicky@gmail.com', 'LOGIN', 'Auth', 'dev_95gakefu_mvosfdm7', 'Android Phone - Chrome', 'User chetranicky@gmail.com logged in from Android Phone - Chrome (New/Secondary Device)', 'dev_95gakefu_mvosfdm7', 'Android Phone - Chrome', '203.147.140.43', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Mobile Safari/537.36', NULL, '2026-10-26 05:00:16', '2026-10-26 05:00:16'),
(9, NULL, 'chetranicky@gmail.com', 'LOGIN', 'Auth', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', 'User chetranicky@gmail.com logged in from Windows PC - Chrome (Primary Device)', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '27.109.115.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-03 12:47:47', '2026-11-03 12:47:47'),
(10, NULL, '855964129976@dyzzstore.user', 'LOGIN', 'Auth', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', 'User 855964129976@dyzzstore.user logged in from Windows PC - Chrome (Primary Device)', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '27.109.115.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-03 13:01:44', '2026-11-03 13:01:44'),
(11, NULL, '855964129976@dyzzstore.user', 'LOGIN', 'Auth', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', 'User 855964129976@dyzzstore.user logged in from Windows PC - Chrome (Primary Device)', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '27.109.115.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-03 13:05:45', '2026-11-03 13:05:45'),
(12, NULL, 'chetranicky@gmail.com', 'LOGIN', 'Auth', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', 'User chetranicky@gmail.com logged in from Windows PC - Chrome (Primary Device)', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '27.109.115.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-03 13:06:44', '2026-11-03 13:06:44'),
(13, NULL, 'chetranicky@gmail.com', 'LOGIN', 'Auth', 'dev_c745d050611ba42f', 'Web Browser', 'User chetranicky@gmail.com logged in from Web Browser (New/Secondary Device)', 'unknown', 'Browser Device', '127.0.0.1', 'Symfony', NULL, '2026-11-03 13:46:27', '2026-11-03 13:46:27'),
(14, NULL, 'admin@synapse.ai', 'LOGIN', 'Auth', 'dev_c745d050611ba42f', 'Web Browser', 'User admin@synapse.ai logged in from Web Browser (New/Secondary Device)', 'unknown', 'Browser Device', '127.0.0.1', 'Symfony', NULL, '2026-11-03 13:46:32', '2026-11-03 13:46:32'),
(15, NULL, 'chetranicky@gmail.com', 'LOGIN', 'Auth', 'dev_b5e199f507a03ede', 'Web Browser', 'User chetranicky@gmail.com logged in from Web Browser (New/Secondary Device)', 'unknown', 'Browser Device', '27.109.115.149', 'node', NULL, '2026-11-03 13:48:20', '2026-11-03 13:48:20'),
(16, NULL, 'admin@synapse.ai', 'LOGIN', 'Auth', 'dev_b5e199f507a03ede', 'Web Browser', 'User admin@synapse.ai logged in from Web Browser (New/Secondary Device)', 'unknown', 'Browser Device', '27.109.115.149', 'node', NULL, '2026-11-03 13:48:33', '2026-11-03 13:48:33'),
(17, NULL, 'admin@synapse.ai', 'LOGIN', 'Auth', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', 'User admin@synapse.ai logged in from Windows PC - Chrome (Primary Device)', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '27.109.115.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-03 14:07:57', '2026-11-03 14:07:57'),
(18, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_CREATED', 'Topup', NULL, NULL, 'Topup order ORD_20261103154837_UJTKDQ created for Mobile Legends (Limited-Time Value Pack)', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '27.109.115.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-03 15:48:38', '2026-11-03 15:48:38'),
(19, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_CREATED', 'Topup', NULL, NULL, 'Topup order ORD_20261103154954_JSALBH created for Mobile Legends (86 Diamonds)', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '27.109.115.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-03 15:49:55', '2026-11-03 15:49:55'),
(20, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_CREATED', 'Topup', NULL, NULL, 'Topup order ORD_20261103155001_V2OTJO created for Mobile Legends (86 Diamonds)', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '27.109.115.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-03 15:50:03', '2026-11-03 15:50:03'),
(21, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_CREATED', 'Topup', NULL, NULL, 'Topup order ORD_20261103161705_YRVJVL created for Mobile Legends (86 Diamonds)', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '27.109.115.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-03 16:17:14', '2026-11-03 16:17:14'),
(22, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_CREATED', 'Topup', NULL, NULL, 'Topup order ORD_20261104013417_GYCVJZ created for Mobile Legends Exclusive (5 Diamonds)', 'dev_r45dsnye_mw1fkqj1', 'Windows PC - Chrome', '203.147.140.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-04 01:34:18', '2026-11-04 01:34:18'),
(23, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_CREATED', 'Topup', NULL, NULL, 'Topup order ORD_20261104054615_C7SQRY created for Mobile Legends (Weekly Elite Pack)', 'dev_95gakefu_mvosfdm7', 'Android Phone - Chrome', '203.147.140.43', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36', NULL, '2026-11-04 05:46:16', '2026-11-04 05:46:16'),
(24, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_CREATED', 'Topup', NULL, NULL, 'Topup order ORD_20261104143745_7KJRXZ created for Mobile Legends (Limited-Time Value Pack)', 'dev_ufnqlw6o_mvnknd1n', 'Android Phone - Chrome', '27.109.115.140', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36', NULL, '2026-11-04 14:37:46', '2026-11-04 14:37:46'),
(25, NULL, 'chetranicky@gmail.com', 'TOPUP.GAME_UPDATED', 'Topup', NULL, NULL, 'Updated topup game Free Fire', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '27.109.115.140', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-04 15:08:30', '2026-11-04 15:08:30'),
(26, NULL, 'chetranicky@gmail.com', 'TOPUP.GAME_UPDATED', 'Topup', NULL, NULL, 'Updated topup game Magic Chess Gogo', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '27.109.115.140', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-04 15:08:38', '2026-11-04 15:08:38'),
(27, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_CREATED', 'Topup', NULL, NULL, 'Topup order ORD_20261105093407_BG0RL2 created for Mobile Legends (Limited-Time Value Pack)', 'dev_1bsssjlg_mw3clrik', 'Windows PC - Edge', '203.147.140.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', NULL, '2026-11-05 09:34:09', '2026-11-05 09:34:09'),
(28, NULL, 'chetranicky@gmail.com', 'TOPUP.GAME_UPDATED', 'Topup', NULL, NULL, 'Updated topup game Mobile Legends Ex', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '27.109.115.153', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-05 15:29:05', '2026-11-05 15:29:05'),
(29, NULL, 'chetranicky@gmail.com', 'TOPUP.MANUAL_VERIFY', 'Topup', NULL, NULL, 'Manual verify & push triggered for order ORD_20261105093407_BG0RL2', 'dev_1bsssjlg_mw3clrik', 'Windows PC - Edge', '203.147.140.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', NULL, '2026-11-07 04:32:26', '2026-11-07 04:32:26'),
(30, NULL, 'chetranicky@gmail.com', 'DELETE', 'Category', '3', 'Code & DevOps1', 'Deleted category \'Code & DevOps1\'', 'unknown', 'Browser Device', '203.147.140.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', NULL, '2026-11-07 04:32:46', '2026-11-07 04:32:46'),
(31, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_DELETED', 'Topup', NULL, NULL, 'Deleted topup order ORD_20261105093407_BG0RL2', 'dev_1bsssjlg_mw3clrik', 'Windows PC - Edge', '203.147.140.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', NULL, '2026-11-07 04:34:38', '2026-11-07 04:34:38'),
(32, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_DELETED', 'Topup', NULL, NULL, 'Deleted topup order ORD_20261022040253_TGCFE85Y', 'dev_1bsssjlg_mw3clrik', 'Windows PC - Edge', '203.147.140.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', NULL, '2026-11-07 04:34:48', '2026-11-07 04:34:48'),
(33, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_DELETED', 'Topup', NULL, NULL, 'Deleted topup order ORD_20261022040347_R3RERZFE', 'dev_1bsssjlg_mw3clrik', 'Windows PC - Edge', '203.147.140.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', NULL, '2026-11-07 04:34:52', '2026-11-07 04:34:52'),
(34, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_DELETED', 'Topup', NULL, NULL, 'Deleted topup order ORD_20261103155001_V2OTJO', 'dev_1bsssjlg_mw3clrik', 'Windows PC - Edge', '203.147.140.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', NULL, '2026-11-07 04:34:55', '2026-11-07 04:34:55'),
(35, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_DELETED', 'Topup', NULL, NULL, 'Deleted topup order ORD_20261022045856_HAHXSDN5', 'dev_1bsssjlg_mw3clrik', 'Windows PC - Edge', '203.147.140.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', NULL, '2026-11-07 04:35:00', '2026-11-07 04:35:00'),
(36, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_DELETED', 'Topup', NULL, NULL, 'Deleted topup order ORD_20261104143745_7KJRXZ', 'dev_1bsssjlg_mw3clrik', 'Windows PC - Edge', '203.147.140.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', NULL, '2026-11-07 04:35:05', '2026-11-07 04:35:05'),
(37, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_DELETED', 'Topup', NULL, NULL, 'Deleted topup order ORD_20261104054615_C7SQRY', 'dev_1bsssjlg_mw3clrik', 'Windows PC - Edge', '203.147.140.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', NULL, '2026-11-07 04:35:10', '2026-11-07 04:35:10'),
(38, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_DELETED', 'Topup', NULL, NULL, 'Deleted topup order ORD_20261104013417_GYCVJZ', 'dev_1bsssjlg_mw3clrik', 'Windows PC - Edge', '203.147.140.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', NULL, '2026-11-07 04:35:14', '2026-11-07 04:35:14'),
(39, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_DELETED', 'Topup', NULL, NULL, 'Deleted topup order ORD_20261103161705_YRVJVL', 'dev_1bsssjlg_mw3clrik', 'Windows PC - Edge', '203.147.140.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', NULL, '2026-11-07 04:35:20', '2026-11-07 04:35:20'),
(40, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_DELETED', 'Topup', NULL, NULL, 'Deleted topup order ORD_20261103154954_JSALBH', 'dev_1bsssjlg_mw3clrik', 'Windows PC - Edge', '203.147.140.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', NULL, '2026-11-07 04:35:26', '2026-11-07 04:35:26'),
(41, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_DELETED', 'Topup', NULL, NULL, 'Deleted topup order ORD_20261103154837_UJTKDQ', 'dev_1bsssjlg_mw3clrik', 'Windows PC - Edge', '203.147.140.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', NULL, '2026-11-07 04:35:32', '2026-11-07 04:35:32'),
(42, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_DELETED', 'Topup', NULL, NULL, 'Deleted topup order ORD_20261022035852_GIIWDQ2F', 'dev_1bsssjlg_mw3clrik', 'Windows PC - Edge', '203.147.140.43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36 Edg/156.0.0.0', NULL, '2026-11-07 04:35:38', '2026-11-07 04:35:38'),
(43, NULL, 'chetranicky@gmail.com', 'TOPUP.ORDER_CREATED', 'Topup', NULL, NULL, 'Topup order ORD_20261107082953_GW4RDW created for Mobile Legends (Limited-Time Value Pack)', 'dev_33khzbt6_mw0tvgho', 'iPhone (iOS) - Safari', '202.93.153.245', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_16 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/133.0.6943.120 Mobile/15E148 Safari/604.1', NULL, '2026-11-07 08:29:55', '2026-11-07 08:29:55'),
(44, NULL, 'chetranicky@gmail.com', 'LOGIN', 'Auth', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', 'User chetranicky@gmail.com logged in from Windows PC - Chrome (Primary Device)', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '202.93.153.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-07 10:25:47', '2026-11-07 10:25:47'),
(45, NULL, 'chetranicky@gmail.com', 'LOGIN', 'Auth', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', 'User chetranicky@gmail.com logged in from Windows PC - Chrome (Primary Device)', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '202.93.153.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-07 14:36:03', '2026-11-07 14:36:03'),
(46, NULL, 'chetranicky@gmail.com', 'SETTINGS_CHANGE', 'Setting', NULL, NULL, 'Updated platform settings & configurations', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '202.93.153.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-07 14:36:33', '2026-11-07 14:36:33'),
(47, NULL, 'chetranicky@gmail.com', 'SETTINGS_CHANGE', 'Setting', NULL, NULL, 'Updated platform settings & configurations', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '202.93.153.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-07 14:39:19', '2026-11-07 14:39:19'),
(48, NULL, 'chetranicky@gmail.com', 'SETTINGS_CHANGE', 'Setting', NULL, NULL, 'Updated platform settings & configurations', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '202.93.153.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-07 14:47:28', '2026-11-07 14:47:28'),
(49, NULL, 'chetranicky@gmail.com', 'SETTINGS_CHANGE', 'Setting', NULL, NULL, 'Updated platform settings & configurations', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '202.93.153.243', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-08 08:01:43', '2026-11-08 08:01:43'),
(50, NULL, 'chetranicky@gmail.com', 'SETTINGS_CHANGE', 'Setting', NULL, NULL, 'Updated platform settings & configurations', 'dev_nku878yn_mvncy0wo', 'Windows PC - Chrome', '202.93.153.243', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', NULL, '2026-11-08 08:02:12', '2026-11-08 08:02:12');
INSERT INTO `audit_logs` (`id`, `user_id`, `user_email`, `action`, `entity_type`, `entity_id`, `entity_name`, `description`, `device_id`, `device_name`, `ip_address`, `user_agent`, `changes`, `created_at`, `updated_at`) VALUES
(51, NULL, 'chetranicky@gmail.com', 'LOGIN', 'Auth', 'dev_33khzbt6_mw0tvgho', 'iPhone (iOS) - Safari', 'User chetranicky@gmail.com logged in from iPhone (iOS) - Safari (New/Secondary Device)', 'dev_33khzbt6_mw0tvgho', 'iPhone (iOS) - Safari', '203.147.140.43', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_7_16 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/133.0.6943.120 Mobile/15E148 Safari/604.1', NULL, '2026-11-10 09:15:40', '2026-11-10 09:15:40');


-- --------------------------------------------------------
-- Table structure for table `cache`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `cache`
-- (No rows in `cache`)


-- --------------------------------------------------------
-- Table structure for table `cache_locks`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `cache_locks`
-- (No rows in `cache_locks`)


-- --------------------------------------------------------
-- Table structure for table `categories`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Boxes',
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#8b5cf6',
  `badge` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `categories`
-- (No rows in `categories`)


-- --------------------------------------------------------
-- Table structure for table `failed_jobs`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `failed_jobs`
-- (No rows in `failed_jobs`)


-- --------------------------------------------------------
-- Table structure for table `job_batches`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `job_batches`
-- (No rows in `job_batches`)


-- --------------------------------------------------------
-- Table structure for table `jobs`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `jobs`
-- (No rows in `jobs`)


-- --------------------------------------------------------
-- Table structure for table `migrations`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `migrations`
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_10_16_070700_create_personal_access_tokens_table', 1),
(5, '2026_10_16_071000_create_categories_table', 1),
(6, '2026_10_16_071100_create_products_table', 1),
(7, '2026_10_16_071200_create_orders_table', 1),
(8, '2026_10_16_071300_create_subscribers_table', 1),
(9, '2026_10_16_071400_create_settings_table', 1),
(10, '2026_10_16_071500_add_role_to_users_table', 1),
(11, '2026_10_16_071600_add_activation_link_to_orders_table', 1),
(12, '2026_10_24_111117_add_is_active_to_products_table', 2),
(13, '2026_10_25_091942_create_audit_logs_table', 3),
(14, '2026_11_03_000001_create_topup_tables', 4);


-- --------------------------------------------------------
-- Table structure for table `orders`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `items` json DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ABA PAY / PayWay',
  `aba_link` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Verified',
  `api_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activation_link` text COLLATE utf8mb4_unicode_ci,
  `activation_note` text COLLATE utf8mb4_unicode_ci,
  `activated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `orders`
-- (No rows in `orders`)


-- --------------------------------------------------------
-- Table structure for table `password_reset_tokens`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `password_reset_tokens`
-- (No rows in `password_reset_tokens`)


-- --------------------------------------------------------
-- Table structure for table `personal_access_tokens`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `personal_access_tokens`
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 2, 'admin-api-token', '5b3c54431046af03abd981546ea7e96b452015c76acc5400f05b26546486b215', '[\"*\"]', NULL, NULL, '2026-10-24 01:26:53', '2026-10-24 01:26:53'),
(2, 'App\\Models\\User', 2, 'auth-token', 'ad96eb0c3c3961de3325b350aaba837ce894f800c39571dc1ab785b4d583c182', '[\"*\"]', NULL, NULL, '2026-10-24 16:31:39', '2026-10-24 16:31:39'),
(3, 'App\\Models\\User', 2, 'admin-api-token', 'feabbbccb70c8f3c8121adbccfaa021392b4f1a69f979aea2edaa83d416990f0', '[\"*\"]', NULL, NULL, '2026-10-24 16:35:20', '2026-10-24 16:35:20'),
(4, 'App\\Models\\User', 3, 'auth-token', '1f8d4f36f12daafdafdc1db28e04db11393ae92b543b4fffcbd59bfc658e4122', '[\"*\"]', NULL, NULL, '2026-10-25 02:26:49', '2026-10-25 02:26:49'),
(5, 'App\\Models\\User', 2, 'admin-api-token', '10e8eec06c1aa699d55c6386573d9bc66037df0d5da96cf4c57306851e85852c', '[\"*\"]', NULL, NULL, '2026-10-25 04:33:05', '2026-10-25 04:33:05'),
(6, 'App\\Models\\User', 2, 'admin-api-token', '8fbd3ca87c1ec2f5fa5dfed8e6406cc1dc6057c48d901b75f5341db582566c11', '[\"*\"]', NULL, NULL, '2026-10-25 07:44:04', '2026-10-25 07:44:04'),
(7, 'App\\Models\\User', 2, 'admin-api-token', '21995e6bda98a434c348f69cb0941dd3e8fe49bba3a4a2119859a3412b304d5b', '[\"*\"]', NULL, NULL, '2026-10-25 08:00:14', '2026-10-25 08:00:14'),
(8, 'App\\Models\\User', 2, 'admin-api-token', '6b5ad7e6cb1c4f09a23fd458dde73c34db97eb346c236d89fb9918cbc262e4c1', '[\"*\"]', NULL, NULL, '2026-10-25 08:31:42', '2026-10-25 08:31:42'),
(9, 'App\\Models\\User', 2, 'admin-api-token', '6e01ee5eb9125f21910207109b019b8806a6717d2d0fa8148dd07ee715cca5da', '[\"*\"]', NULL, NULL, '2026-10-25 08:34:59', '2026-10-25 08:34:59'),
(10, 'App\\Models\\User', 2, 'admin-api-token', '9f391511aebc7d079b9f8cc015c3de4a24087228995b1ef2516fe39be089625f', '[\"*\"]', NULL, NULL, '2026-10-25 08:39:49', '2026-10-25 08:39:49'),
(11, 'App\\Models\\User', 2, 'admin-api-token', '54071b892245e8cea81f3a865a42ebc6dcdeea235a19cf213bcd12d8eb8fa776', '[\"*\"]', NULL, NULL, '2026-10-25 08:40:36', '2026-10-25 08:40:36'),
(12, 'App\\Models\\User', 2, 'admin-api-token', 'ed8cbf81bc09ce5e30f384c062b47161a7e3bb8d547c619ab646dd412f95a70d', '[\"*\"]', NULL, NULL, '2026-10-25 08:57:47', '2026-10-25 08:57:47'),
(13, 'App\\Models\\User', 2, 'admin-api-token', '134a5637d6edaf54e71610d93fda2f7bbde5ec2a061d3668bc7bca39fc6f3acf', '[\"*\"]', NULL, NULL, '2026-10-25 09:01:44', '2026-10-25 09:01:44'),
(14, 'App\\Models\\User', 2, 'admin-api-token', 'efed407c31ddbbfffddab62bdb45e9672d0f38088f2c6be9468bbe8c344b4602', '[\"*\"]', NULL, NULL, '2026-10-25 09:08:49', '2026-10-25 09:08:49'),
(15, 'App\\Models\\User', 2, 'admin-api-token', '63e79651ffd03db33dd104b1aee0d075916db5c74296d48ac810e0dbf3c8264c', '[\"*\"]', NULL, NULL, '2026-10-25 09:16:22', '2026-10-25 09:16:22'),
(16, 'App\\Models\\User', 2, 'admin-api-token', 'd3a90d420aec7482fa35d9b7faa9779aefb66f8205bb2f7547d06bdfc41bca0c', '[\"*\"]', NULL, NULL, '2026-10-26 05:00:15', '2026-10-26 05:00:15'),
(17, 'App\\Models\\User', 2, 'admin-api-token', '996cf58fc1872effc0fce710813f7a06e0449c152f77cabe25df2f7a052dc98e', '[\"*\"]', NULL, NULL, '2026-11-03 13:46:20', '2026-11-03 13:46:20'),
(18, 'App\\Models\\User', 1, 'admin-api-token', 'dec2fef9c4788aa06d1a969c11507499b65d613508bd0857b46ddc74b072557a', '[\"*\"]', NULL, NULL, '2026-11-03 13:46:31', '2026-11-03 13:46:31'),
(19, 'App\\Models\\User', 2, 'admin-api-token', '496d2d35cb215026d263573694925b558e9ac7607fe01f2b96daab7cc2e66325', '[\"*\"]', NULL, NULL, '2026-11-03 13:48:19', '2026-11-03 13:48:19'),
(20, 'App\\Models\\User', 1, 'admin-api-token', '6c32b153e52cc4839806aeb015fb36b9cf7b03b88f1d31b1845fb39fd828865b', '[\"*\"]', NULL, NULL, '2026-11-03 13:48:32', '2026-11-03 13:48:32'),
(21, 'App\\Models\\User', 1, 'admin-api-token', '0145cf5d8fbb170902fd4b577578494033d26d985f99169159737377ace26146', '[\"*\"]', NULL, NULL, '2026-11-03 14:07:56', '2026-11-03 14:07:56'),
(22, 'App\\Models\\User', 2, 'admin-api-token', 'fb27e989d57787db2918358c5a555504330153e103e17f750f99a69ec6d02ce6', '[\"*\"]', NULL, NULL, '2026-11-07 10:25:45', '2026-11-07 10:25:45'),
(23, 'App\\Models\\User', 2, 'admin-api-token', '12f5beb2c4aa5a080d08ab5f6f6778d1f8bb459360c5b570e37fe34e667fdcf1', '[\"*\"]', NULL, NULL, '2026-11-07 14:36:01', '2026-11-07 14:36:01');


-- --------------------------------------------------------
-- Table structure for table `products`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `category_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'llm',
  `badge` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` decimal(3,2) NOT NULL DEFAULT '4.95',
  `reviews_count` int NOT NULL DEFAULT '100',
  `users_count` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '10k+',
  `short_desc` text COLLATE utf8mb4_unicode_ci,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `capabilities` json DEFAULT NULL,
  `price_monthly` decimal(10,2) NOT NULL DEFAULT '29.00',
  `price_lifetime` decimal(10,2) NOT NULL DEFAULT '199.00',
  `included_in_sub` tinyint(1) NOT NULL DEFAULT '1',
  `engine_version` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latency` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Brain',
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#8b5cf6',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  UNIQUE KEY `products_product_id_unique` (`product_id`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `products`
INSERT INTO `products` (`id`, `product_id`, `name`, `slug`, `category_id`, `category_slug`, `badge`, `rating`, `reviews_count`, `users_count`, `short_desc`, `description`, `capabilities`, `price_monthly`, `price_lifetime`, `included_in_sub`, `engine_version`, `latency`, `icon`, `color`, `status`, `is_active`, `created_at`, `updated_at`) VALUES
(17, 'capcut-pro-1-month', 'CapCut Pro 1 Month', 'capcut-pro-1-month', NULL, 'uncategorized', 'NEW', '4.95', 100, '10k+', 'CapCut Pro Subscription (iOS, Android, PC) 1 Month - Private Account (Global)', NULL, '[\"Neural Processing\", \"Low Latency\", \"24/7 API\"]', '1.50', '199.00', 1, NULL, NULL, 'Brain', '#8b5cf6', 'Inactive', 0, '2026-10-23 04:29:48', '2026-11-07 04:32:45'),
(18, 'gemini-pro-18-month', 'Gemini Pro 18 month', 'gemini-pro-18-month', NULL, 'uncategorized', 'NEW', '4.95', 100, '10k+', 'activate link', NULL, '[\"Neural Processing\", \"Low Latency\", \"24/7 API\"]', '2.20', '199.00', 1, NULL, NULL, 'Brain', '#8b5cf6', 'Inactive', 0, '2026-10-24 11:49:29', '2026-11-07 04:32:45');


-- --------------------------------------------------------
-- Table structure for table `sessions`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `sessions`
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0UZrCuSJQWBBdgcLP3oRTWuXJb2KNX5JbCl4qB98', NULL, '5.161.113.195', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiIwVWprM3V4S3M5QWVCSnd5QWN6SWxwQ3dXVFhMbWtzN3poVFJqOVdjIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794354826),
('1Qai6GOa3QlmmO6R7ObmBDMFVq3fl1JL0kNBbreq', NULL, '5.161.61.238', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJWM2Rhc3Z6eHR4UEN4UVlJUmpVYWo0RmFncHVQU1VVNGh6N1RMSmh1IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794343829),
('1tFdjkYevCuAKq6dJaAtuzqfXJ1gqoAmnXyAGg5v', NULL, '5.161.113.195', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJXWUJReHZtWGtHOFFDZ2haaVVJbzZXeTNNNHFjVjZ5c1lsN3d2ZjJiIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794348540),
('1z9x22qvHNG4uQ4NEfhJ0qYkQfqoZmkTzzHV3wI3', NULL, '3.133.226.214', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJmT0d5ZHRiMERoTnExdnlocWMyVWVVQW9WV1E5ZlJlZ25GUDlhYWhDIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794349325),
('4pYHKkNYIkLIDvV0I3fhhpXIpKRsNXvc2llturkX', NULL, '178.156.187.238', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJVdVhndWttWm9nUFVHRGxseXQ1OUxJMHBhRnVJT0w1RmtsTndWdExzIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794354041),
('53xAIO2iZ3Z1MuWjsAGgZNgV3ISNq9F3wKp7MGeq', NULL, '5.161.73.160', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJiUDdFYkh6QVlCYU1rekQwdWdONGo4V0t1ZDlnWmw1cjR5MWIyaFpIIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794355613),
('6sFLKttcSPL0IcE7FjZJRlCwW5tRLhJPU1cHErbS', NULL, '178.156.185.127', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJidVE4cEpkY3dpY0ROT0Jjb1FrbFpEdkJFM1F6bEE0YU1nU3VnOUVWIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794363469),
('7tAF5Zsv9J5ufnMmXHdp9GDInVkE7IfXvtHSyuVy', NULL, '5.161.61.238', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJOM2NrWTAzWFlMTXJuRGxETFc5Nk5qVG9ZdE10S0lBZmRYUjB1dVllIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794367403),
('7ZHb9ply5u3ZxgYsoWc9Nh9wMZjgevIKswKW8tC5', NULL, '3.20.63.178', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJIa0hKdFRNOVcwVDhkd1JidXg3OVp5c0ttcUVqSGpoVmg5dDR5aVV1IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794365047),
('9gwqyaFXL3kpSZlXlCQsiZwFpoVrKgZduejrGNao', NULL, '5.161.117.52', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJ0RlhKZXpvNVZ0Z1VhenVsYjdhYnRjaUQ4clI3djhaMUYwRllzZlVvIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794352470),
('aCvtPAhmd3bgBErJqAU7k87bUs1VYRvD0lepOzEY', NULL, '5.161.75.7', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJEa0hidDQ4OUlsRHhWVDN6eEljYVJVUmtOcHR1Vm1OdlZ6cWRYa2t5IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794350896),
('BxcOQ9F0T5rR3qGoblkgDAPWR5NcmJepqDqAoOny', NULL, '3.149.57.90', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJMb0NzWjM2SWxIUTEwVU9tUldLZzRlcGlFZ1dNNDNLam9nMEY4T00zIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794346971),
('cAlqyBfTH5kNjLpeJzhY7An6koOgZdMENRLZ4Ubo', NULL, '178.156.185.127', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiI4RTliV2xPZFNSOXRkbkxxUUZVbFJEaXBtTmZwSDZlazdmTTBrcVlPIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794369759),
('eZiEInBM0OPQCOzCn2oCjXZcauNgCqSKytRN8aKB', NULL, '54.167.223.174', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJMVHg5aDRtN010aXVUbUxIQUo1SE1EMGtyUXdWN0F1WEFOSk9LTUpZIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794339114),
('GdxJjgTlSS1kJdU0LLnq96YbDGRP0f2gGDgWeZhM', NULL, '34.198.201.66', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiIxYUZBeVg5SjFjczNVbDFYQlZXZ0YzVWJWelRBdXRJR3lYY3ZnOHdzIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794343043),
('GvGrWXa6EKmDPXrkQmYUZwNPLGrqTuPYH76wPF4N', NULL, '178.156.181.172', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJ2SEl5VVUyMGlQOU9DQnVUUzc2dHBuSE5uT0o2U1NRbmNoOElURnY1IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794362685),
('HPg8w4CZiZJkMtjo1XZUByZTEEd2XkevwHA7Xyam', NULL, '54.167.223.174', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJNV29wY1lPOUdicVVaQTlmTnp0WVB6dFd1cUdMQVo1SWdGb2txd1ROIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794371330),
('htlfUkhe8DShoVorUGHBo7NnxGkpylSJBp7NUSsG', NULL, '5.161.215.244', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJYQUNld1ZmRFpWdklKRWlnTEtqQTZnNmg3dUxUbElsckpXTXBieDJEIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794341472),
('IKgO3QKxjfASmSry3OeRxsLUrLjkDkN8wrfvotrH', NULL, '54.167.223.174', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJZVVVGQ0VFanFFSFlzMFRtOWxrTkFqMmU3Z05UUXpad0hOTlZKcG5jIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794350110),
('J5ieAALu22o3OhCNp0PYmCow9XVGOnKg9M9ly0r6', NULL, '178.156.185.231', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJab0NHcWloelpVMUdBVThIem4xOENEWWdJa25RYVQzaFJ4UmpPRVZmIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794360329),
('jc06HcsbQrSKoLpQmegGMtU3WJGUVXVj9NBcehGX', NULL, '52.15.147.27', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJaWXFNaWltTEpub2pURTEyek5XQW55NjZBb2xwd2xOcTRIcXJnSE84IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794359544),
('k7JQBq1U9IxduxXJmqTSI7MnopQJQXHwCIohAgZA', NULL, '34.198.201.66', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJNQ2xnYjZFUjlhS0xFVnBxZTdvZEJ5RHhhaENpTEhOMjdtTHhraFVuIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794346185),
('K7NYkfS010aiTQdntW9lIUp2dlLNC4snE10dQWO8', NULL, '3.149.57.90', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJlZ2VCZWIybXZETzZnTzAybnRsSmJUVzZPNDA4MEU4ZjhHV2kyaWpYIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794358757),
('lQLohcHoRO980i2E0ZpZ3G0kw1ATKEt0wNlCYS0q', NULL, '52.22.236.30', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJKWTFmaDVpOXhsdXh3Q0dFRFVIQkxBMmVoWE1LVjdabmF3NWtXNVhTIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794370544),
('M52WVFHnoBxDygQS12knsvzxHfY6tlJ8jcKKrjfY', NULL, '52.22.236.30', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJvUTl2Y08wVWZ4RTBLeVB3YWtsY09wQWtNNnJ1aVdGcDR0d2lyOHRlIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794356400),
('m7zDETQPQtq2PBS5mRpfD4CUPu0j0FEIKRC5OYZ9', NULL, '178.156.184.20', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJ1TktVRjRzZW5McW1WZ0dlWGxsclJkSXQwWU1IR21WQWVacTFZRndiIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794357186),
('MwH5fB97IqBns1rrH6zY7jkEA3KyWYs3bpcsx9c4', NULL, '52.22.236.30', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJvVlBveE9LZGx3UWI2ajF4Y24yN2o1SzdLeXhKNjJOeG1samI2N3R3IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794365832),
('oyqt3RORXRvGTwbCyIiY2dGqPXkQ0ppu6GljTPoN', NULL, '178.156.187.238', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiIyamZkYzBwRTBDajdUazBqVHRVbk5yT1AzVFhpcFVmOTJFZ2Y2MjZrIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794368974),
('PiOt4D4NvMVzxLeg13ca4J5ImrPELgXOmryASyxp', NULL, '5.161.177.47', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiI4alFsR3RnRHFUckE5QUt4YThxTWRWSE13bDlRRkV4TzhzWUJCajEwIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794342258),
('po4eXKNf1ofy72P4hXIAEmDiutuaHBShH5pzuJw6', NULL, '3.149.57.90', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJNcDhCWU9sajU2c1Z2Y2RoQktzcVU2Qm9tUlJhWXlYUURYRkR3YWRyIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794340687),
('pVPjbaVvByMCb7NAETf2yR4BWzaTkP1JBvcjP90F', NULL, '52.15.147.27', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJIYWtaOEFMQmxnMTlrYzdjT0xZMlhSU3Bqc3huOWEzOUxOWnFTOWhxIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794361115),
('py3aU0CSNRgs70AlDR4RcIFZjuAM6Jmf4R8c2ODR', NULL, '178.156.185.231', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJZelYxT3R6Q2NJUXRvVjRMbFVIUHhVTzBLQmtKZ1FyRU1QNTlKVXFoIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794345400),
('rGUY9q8DyWWFhiEhFh4DTcNzBuT917gMvbp0kHdL', NULL, '3.133.226.214', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJmeFB4d2RPRXBWemR4YlNpamY2Ym41d1BsejI5NDBTVnlDR2tqMEFpIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794347755),
('rgzzcVe9fd48qPxo44z7OqNCyMLn4SLQx7ntIaVh', NULL, '178.156.189.249', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJ2ZXU5SlVqSmY3bzBMTEIwSTZUWExNQm10Y29SbUxGT2hBRkliNktjIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794361901),
('U1p1HnUGmTfxaqzDIkqnM7RJn6h9tkixWuQp8fgV', NULL, '54.87.112.51', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJIQ01IY0ZDcExmSm9GcTBJNmtSeE00ZktISHpGNjlESWRzcTFXVm5pIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794366618),
('UAmbgDgWMBNR7TpZZVH0yU2jeuez02a1In3cTRgq', NULL, '3.20.63.178', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJhRmYxelJEcWFBNUdDamt4TU41UGtFV0FpVzhodGJEVjRpeDBYMDNCIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794364256),
('UShtQlccODs1pmw40xYF8rUIawGM2jIXnACgbDoA', NULL, '178.156.185.127', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJTaVkxOGVLYTVlNFlXVVZBdFhNaGhTaWZVT29yeTRCaWp3MG9CMjdJIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794351683),
('uWiqtKoJPE2GW2tryFC8HdhZ8PcZzAMDKBrOyPR6', NULL, '54.87.112.51', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJkM3d6UldIR2RhaVVvU3lzSDdKamdWbHNUMGoxZjRhVWV5Z255eXRFIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794353256),
('viSOKO4b0RYMZW8mofyNosIaYRfxDH6wGjPoBZRY', NULL, '34.198.201.66', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJLUk1pQmkzbWV4MWZEVFVmbDVMWXJUYWJCZmkyUUNMSER6bHRHS28xIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794339901),
('VwyMJ07DWpCVsYewI7F4a3Qdd34dMqeGYExzX0Tb', NULL, '5.161.215.244', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJIZ256dWczUER4VWJvcUFnVkl0ZU45NEp5b0ptMEFMWkIzS0tNNWdNIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794357972),
('WZ24NvOtaQ1JkAT7TOMIuyFGKgNbx56B2gJKJzzW', NULL, '5.161.177.47', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJqR3RlMGhNOEN6dG1GdEJ5WTlXQUtqZVVWalFHTXJKdEs0eXkxZmJHIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794368189),
('yDPqfr4EO61OEFreUntE1jhUWnYLHYbNF4O7igEX', NULL, '5.161.215.244', 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/)', 'eyJfdG9rZW4iOiJDOW9XRUZmSHh0Z1FWTFpzSHdnQk1UdEZYVE1iVXFhbHlzQmk4N3RTIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1794344613);


-- --------------------------------------------------------
-- Table structure for table `settings`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `settings`
INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'aba_pay_link', 'https://link.payway.com.kh/ABAPAY9s540501U', '2026-10-16 07:11:42', '2026-10-16 07:11:42'),
(2, 'announcement_text', 'Synapse Omni-LLM 4.5 & VideoGen 4D are now live! Get 20% OFF all annual subscriptions with code NEURON20', '2026-10-16 07:11:42', '2026-10-16 07:11:42'),
(3, 'system_status', 'All AI Clusters Operational (99.99% SLA)', '2026-10-16 07:11:42', '2026-10-16 07:11:42'),
(4, 'maintenance_mode', '', '2026-10-16 07:11:42', '2026-10-16 07:11:42'),
(5, 'promo_codes', '[{\"code\":\"NEURON20\",\"percent\":20,\"active\":true},{\"code\":\"AIWELCOME\",\"amount\":10,\"active\":true}]', '2026-10-16 07:11:42', '2026-10-16 07:11:42'),
(6, 'telegram_bot_token', '8624924883:AAGv-1CM4Rrak3ezl0Na-L4uEkDBcCNwcfg', '2026-10-18 06:31:59', '2026-10-20 13:46:40'),
(7, 'telegram_chat_id', '1660380634', '2026-10-18 06:31:59', '2026-10-20 13:46:40'),
(8, 'telegram_enabled', '1', '2026-10-18 06:32:18', '2026-10-20 13:46:40'),
(9, 'khqr_gateway_url', 'https://anajakpay.com/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS', '2026-10-20 13:46:40', '2026-10-23 04:13:11'),
(10, 'khqr_profile_id', 'tW6PHjgPyzISFi3KK22hKZ57rag1cWHS', '2026-10-20 13:46:40', '2026-10-20 13:46:40'),
(11, 'khqr_secret_key', 'zsFq7SWHV4gYFSAdfg2ud8WV747tBOei', '2026-10-20 13:46:40', '2026-10-20 13:46:40'),
(12, 'plasgate_private_key', '5mTORnzm_otfIc4Pv8C4ydB0L7HRAbtUPVxw2RlE7Ed6N4-XJLDB-LmwrNes7SMqlitldKzzorF5IwR-rqeNCA', '2026-10-24 16:32:57', '2026-10-24 16:32:57'),
(13, 'plasgate_secret_key', '$5$rounds=535000$MCxplb66oxDOQkEN$uvJGKM9Da2we/.MzAL99lj7HU9/WlETpoqFsWbToAy5', '2026-10-24 16:32:57', '2026-10-24 16:32:57'),
(14, 'plasgate_sender_id', 'PlasGateUAT', '2026-10-24 16:32:58', '2026-10-25 02:25:46'),
(15, 'plasgate_enabled', '1', '2026-10-24 16:32:58', '2026-10-25 02:25:46'),
(16, 'primary_device_id', 'dev_nku878yn_mvncy0wo', '2026-10-25 07:11:53', '2026-10-25 07:11:53'),
(17, 'primary_device_name', 'Windows PC - Chrome', '2026-10-25 07:11:53', '2026-10-25 07:45:21'),
(18, 'primary_device_ip', '27.109.115.145', '2026-10-25 07:11:54', '2026-10-25 07:45:21'),
(19, 'primary_device_at', '2026-10-25T07:46:36+00:00', '2026-10-25 07:11:54', '2026-10-25 07:46:36'),
(20, 'single_device_settings_only', '1', '2026-10-25 07:45:11', '2026-10-25 07:52:37'),
(21, 'telegram_device_chat_id', '1660380634', '2026-10-25 07:45:59', '2026-10-25 07:45:59'),
(22, 'telegram_device_bot_token', '8993645245:AAFlx5tIr_yqeUVIGB9lu52fShoXDgETa6o', '2026-10-25 07:45:59', '2026-10-25 07:45:59'),
(23, 'telegram_device_alerts_enabled', '1', '2026-10-25 07:45:59', '2026-10-25 07:45:59'),
(24, 'telegram_alert_on_new_device', '1', '2026-10-25 07:45:59', '2026-10-25 07:45:59'),
(25, 'telegram_alert_on_primary_login', '1', '2026-10-25 07:46:00', '2026-10-25 07:46:00'),
(26, 'telegram_snap_intruder_photo', '', '2026-10-25 09:02:36', '2026-10-25 09:15:36'),
(27, 'admin_email', 'chetranicky@gmail.com', '2026-10-25 09:02:36', '2026-10-25 09:02:36'),
(28, 'mail_username', 'rrqkh61@gmail.com', '2026-10-25 09:02:37', '2026-10-25 09:02:37'),
(29, 'mail_password', 'ikowlciyetgjggil', '2026-10-25 09:02:37', '2026-10-25 09:02:37'),
(30, 'mail_from_name', 'Admin OTP', '2026-10-25 09:02:37', '2026-10-25 09:02:37'),
(31, 'mail_from_address', 'rrqkh61@gmail.com', '2026-10-25 09:02:37', '2026-10-25 09:02:37'),
(32, 'admin_password_hash', '$2y$12$RJ08ymXktaC4mgRNsZAC/ukrLnIcJXrrrt2rJBo.A6AKyCIkvEWgq', '2026-10-25 09:35:28', '2026-11-03 13:45:22'),
(33, 'admin_password_updated_at', '2026-10-25T09:35:28+00:00', '2026-10-25 09:35:28', '2026-10-25 09:35:28'),
(34, 'seo_title', 'Dyzzstore | Premier AI Tools Marketplace & Game Top-Up', '2026-11-07 14:36:31', '2026-11-07 14:36:31'),
(35, 'seo_description', 'Discover, buy, and top-up MLBB diamonds, Magic Chess, Free Fire, and premier AI software tools. Instant delivery via Cambodia National KHQR or Card.', '2026-11-07 14:36:32', '2026-11-07 14:36:32'),
(36, 'seo_keywords', 'Dyzzstore, MLBB topup, Diamond Mobile Legends, Magic Chess, Free Fire topup, AI tools, Midjourney, ChatGPT, perpetual license, KHQR Bakong payment, cheap game topup Cambodia, ថោកជាងគេ ស្រុកខ្មែរ, Midjourney Lifetime, ChatGPT Plus Cambodia, AI Tools Marketplace, Cambodia KHQR Payment, ទិញ Diamonds MLBB, Weekly Diamond Pass, ABA PayWay Scan', '2026-11-07 14:36:32', '2026-11-07 14:47:27'),
(37, 'google_verification', 'RhwAQDXFLfwzFSBq0YM1kouGUUJ228ZjagAQ3Zr6K_I', '2026-11-07 14:36:32', '2026-11-07 14:36:32'),
(38, 'seo_robots', 'index, follow', '2026-11-07 14:36:32', '2026-11-07 14:36:32'),
(39, 'social_facebook', 'https://web.facebook.com/ismechett', '2026-11-07 14:36:32', '2026-11-08 08:02:11'),
(40, 'social_instagram', '', '2026-11-07 14:36:32', '2026-11-07 14:36:32'),
(41, 'social_twitter', '', '2026-11-07 14:36:33', '2026-11-07 14:36:33'),
(42, 'social_linkedin', 'https://linkedin.com/company/yourcompany', '2026-11-07 14:36:33', '2026-11-07 14:36:33'),
(43, 'social_telegram', 'https://t.me/dyzzstore_bot', '2026-11-07 14:36:33', '2026-11-07 14:36:33'),
(44, 'social_whatsapp', '', '2026-11-07 14:36:33', '2026-11-07 14:36:33'),
(45, 'social_tiktok', 'https://www.tiktok.com/@.reizz4?is_from_webapp=1&sender_device=pc', '2026-11-07 14:36:33', '2026-11-08 08:01:43');


-- --------------------------------------------------------
-- Table structure for table `subscribers`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `subscribers`;
CREATE TABLE `subscribers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pro AI Suite (Annual)',
  `tier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pro',
  `tokens_used` bigint NOT NULL DEFAULT '0',
  `token_limit` bigint NOT NULL DEFAULT '15000000',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `renews_on` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ABA PAY / PayWay',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscribers_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `subscribers`
INSERT INTO `subscribers` (`id`, `name`, `email`, `plan`, `tier`, `tokens_used`, `token_limit`, `status`, `renews_on`, `payment_method`, `created_at`, `updated_at`) VALUES
(6, 'Bunthoeun Chetra Test', 'chetranicky@gmail.com', 'Pro AI Suite (Annual)', 'pro', 0, 15000000, 'Active', '2027-10-18', 'KHQR Direct Gateway', '2026-10-18 07:24:22', '2026-10-18 07:24:22'),
(7, 'Alex Vance', 'alex@venturelabs.ai', 'Pro AI Suite (Annual)', 'pro', 0, 15000000, 'Active', '2027-10-25', 'KHQR Direct Gateway', '2026-10-25 09:54:58', '2026-10-25 09:54:58');


-- --------------------------------------------------------
-- Table structure for table `topup_games`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `topup_games`;
CREATE TABLE `topup_games` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `api_game_id` bigint unsigned DEFAULT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `topup_games_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `topup_games`
INSERT INTO `topup_games` (`id`, `api_game_id`, `code`, `name`, `icon_url`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 3, 'mlbb', 'Mobile Legends', NULL, 1, '2026-06-18 10:02:07', '2026-06-18 10:02:07'),
(12, 5, 'topup_mobile_legends_exclusive', 'Mobile Legends Ex', NULL, 1, '2026-06-24 11:05:39', '2026-11-05 15:29:05'),
(13, 107, 'magic_chest_gogo', 'Magic Chess Gogo', NULL, 0, '2026-06-24 13:34:25', '2026-11-04 15:08:37'),
(14, NULL, 'freefire_global', 'Free Fire', NULL, 0, '2026-08-24 09:07:02', '2026-11-04 15:08:29');


-- --------------------------------------------------------
-- Table structure for table `topup_orders`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `topup_orders`;
CREATE TABLE `topup_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `topup_game_id` bigint unsigned NOT NULL,
  `topup_package_id` bigint unsigned NOT NULL,
  `player_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `player_username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zone_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'payway',
  `amount` decimal(12,2) NOT NULL,
  `diamond_amount` int unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `gateway_transaction_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_checkout_url` text COLLATE utf8mb4_unicode_ci,
  `gateway_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_payload` json DEFAULT NULL,
  `supplier_order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier_payload` json DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `processing_at` timestamp NULL DEFAULT NULL,
  `success_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `failure_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `topup_orders_order_no_unique` (`order_no`),
  UNIQUE KEY `topup_orders_gateway_transaction_id_unique` (`gateway_transaction_id`),
  KEY `topup_orders_topup_game_id_foreign` (`topup_game_id`),
  KEY `topup_orders_topup_package_id_foreign` (`topup_package_id`),
  CONSTRAINT `topup_orders_topup_game_id_foreign` FOREIGN KEY (`topup_game_id`) REFERENCES `topup_games` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `topup_orders_topup_package_id_foreign` FOREIGN KEY (`topup_package_id`) REFERENCES `topup_packages` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=203 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `topup_orders`
INSERT INTO `topup_orders` (`id`, `order_no`, `topup_game_id`, `topup_package_id`, `player_id`, `player_username`, `zone_id`, `payment_method`, `amount`, `diamond_amount`, `status`, `gateway_transaction_id`, `gateway_checkout_url`, `gateway_hash`, `gateway_payload`, `supplier_order_id`, `supplier_payload`, `paid_at`, `processing_at`, `success_at`, `failed_at`, `failure_reason`, `created_at`, `updated_at`) VALUES
(58, 'ORD_20260620023600_VGM8EK7X', 1, 1, '222', NULL, '111', 'khqr', '0.01', 5, 'success', 'ORD_ORD_20260620023600_VGM8EK7X_20260620023600', 'https://anajakpay.com/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260620023600_VGM8EK7X_20260620023600&amount=0.01&remark=MLBB%7CID%3A222%7CS%3A111&hash=022110a2fd54082b6b638d1cd3ef1041988ca69c&success_url=http%3A%2F%2Flocalhost%3A8000%2Fpayment-success.html', '2f869852356576d5ed53e03c87d01e50794b20d6', '{\"hash\": \"022110a2fd54082b6b638d1cd3ef1041988ca69c\", \"amount\": \"0.01\", \"remark\": \"MLBB|ID:222|S:111\", \"success\": true, \"currency\": \"USD\", \"created_at\": \"2026-11-03T15:58:07+00:00\", \"iframe_url\": \"https://anajakpay.com/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260620023600_VGM8EK7X_20260620023600&amount=0.01&remark=MLBB%7CID%3A222%7CS%3A111&hash=022110a2fd54082b6b638d1cd3ef1041988ca69c&success_url=http%3A%2F%2Flocalhost%3A8000%2Fpayment-success.html&iframe=1\", \"profile_id\": \"tW6PHjgPyzISFi3KK22hKZ57rag1cWHS\", \"gateway_url\": \"https://anajakpay.com/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS\", \"success_url\": \"http://localhost:8000/payment-success.html\", \"checkout_url\": \"https://anajakpay.com/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260620023600_VGM8EK7X_20260620023600&amount=0.01&remark=MLBB%7CID%3A222%7CS%3A111&hash=022110a2fd54082b6b638d1cd3ef1041988ca69c&success_url=http%3A%2F%2Flocalhost%3A8000%2Fpayment-success.html\", \"merchant_name\": \"dyzz\", \"transaction_id\": \"ORD_ORD_20260620023600_VGM8EK7X_20260620023600\"}', NULL, NULL, '2026-06-20 02:40:43', '2026-06-20 02:40:43', '2026-06-20 02:40:43', NULL, NULL, '2026-06-20 02:36:00', '2026-11-03 15:58:07'),
(60, 'ORD_20260620045008_YIHADHUI', 1, 1, '123', '', '1111', 'khqr', '0.01', 5, 'success', 'ORD_ORD_20260620045008_YIHADHUI_20260620045008', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260620045008_YIHADHUI_20260620045008&amount=0.01&remark=MLBB%7CID%3A123%7CS%3A1111&hash=ae5174a6b49a8d812f51b9effc99acc2dde38769', 'ae5174a6b49a8d812f51b9effc99acc2dde38769', NULL, NULL, NULL, '2026-06-20 04:50:45', NULL, '2026-06-20 04:50:45', NULL, NULL, '2026-06-20 04:50:08', '2026-06-20 04:50:45'),
(62, 'ORD_20260620050448_NW32XGLU', 1, 1, '12322', '', '1111222', 'khqr', '0.01', 5, 'pending', 'ORD_ORD_20260620050448_NW32XGLU_20260620050449', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260620050448_NW32XGLU_20260620050449&amount=0.01&remark=MLBB%7CID%3A12322%7CS%3A1111222&hash=98b8db565e6fb533832a1aca199236a7768b337c', '98b8db565e6fb533832a1aca199236a7768b337c', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-20 05:04:48', '2026-06-20 05:04:49'),
(63, 'ORD_20260620051244_0VW2FKPQ', 1, 2, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.02', 10, 'pending', 'ORD_ORD_20260620051244_0VW2FKPQ_20260620051245', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260620051244_0VW2FKPQ_20260620051245&amount=0.02&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=da7965bc7aaf31b9a41eb690874168174033a0fc', 'da7965bc7aaf31b9a41eb690874168174033a0fc', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-20 05:12:44', '2026-06-20 05:12:45'),
(64, 'ORD_20260620052052_EBYVU6GC', 1, 2, '702425515', '', '10301', 'khqr', '0.02', 10, 'pending', 'ORD_ORD_20260620052052_EBYVU6GC_20260620052053', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260620052052_EBYVU6GC_20260620052053&amount=0.02&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=db0a244c6dbc51f80a8258455a0bf856a9fd613a', 'db0a244c6dbc51f80a8258455a0bf856a9fd613a', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-20 05:20:52', '2026-06-20 05:20:53'),
(65, 'ORD_20260620052959_FBRL5ALY', 1, 2, '555', '', '444', 'khqr', '0.02', 10, 'pending', 'ORD_ORD_20260620052959_FBRL5ALY_20260620052959', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260620052959_FBRL5ALY_20260620052959&amount=0.02&remark=MLBB%7CID%3A555%7CS%3A444&hash=ad98e06537924e69352933d50d103af31caa1fed', 'ad98e06537924e69352933d50d103af31caa1fed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-20 05:29:59', '2026-06-20 05:30:00'),
(66, 'ORD_20260620053739_BI5ZHQCZ', 1, 2, '555', '', '444', 'khqr', '0.02', 10, 'success', 'ORD_ORD_20260620053739_BI5ZHQCZ_20260620053739', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260620053739_BI5ZHQCZ_20260620053739&amount=0.02&remark=MLBB%7CID%3A555%7CS%3A444&hash=61a9ef0348b1337a5b22ed5d5cacba652b045f6e', '61a9ef0348b1337a5b22ed5d5cacba652b045f6e', NULL, NULL, NULL, '2026-06-20 05:38:08', NULL, '2026-06-20 05:38:08', NULL, NULL, '2026-06-20 05:37:39', '2026-06-20 05:38:08'),
(67, 'ORD_20260620055218_MX6XAKK3', 1, 1, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.01', 5, 'success', 'ORD_ORD_20260620055218_MX6XAKK3_20260620055219', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260620055218_MX6XAKK3_20260620055219&amount=0.01&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=145ce63c0d46bad97bee41cc4eb9484b45bb901f', '145ce63c0d46bad97bee41cc4eb9484b45bb901f', NULL, NULL, NULL, '2026-06-20 05:52:49', NULL, '2026-06-20 05:52:49', NULL, NULL, '2026-06-20 05:52:18', '2026-06-20 05:52:49'),
(68, 'ORD_20260620062814_LZ7W0ZIF', 1, 1, '1245178176', 'Syn.', '14013', 'khqr', '0.01', 5, 'pending', 'ORD_ORD_20260620062814_LZ7W0ZIF_20260620062815', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260620062814_LZ7W0ZIF_20260620062815&amount=0.01&remark=MLBB%7CID%3A1245178176%7CS%3A14013&hash=eb9eb833ae86c02f100150e064da67d3f85730a3', 'eb9eb833ae86c02f100150e064da67d3f85730a3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-20 06:28:14', '2026-06-20 06:28:15'),
(69, 'ORD_20260620063206_KDINKNNZ', 1, 1, '1245178176', 'Syn.', '14013', 'khqr', '0.01', 5, 'success', 'ORD_ORD_20260620063206_KDINKNNZ_20260620063206', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260620063206_KDINKNNZ_20260620063206&amount=0.01&remark=MLBB%7CID%3A1245178176%7CS%3A14013&hash=511de25233ef2c0cfbf862821b9bb2909cf3b6cf', '511de25233ef2c0cfbf862821b9bb2909cf3b6cf', NULL, NULL, NULL, '2026-06-20 06:33:09', NULL, '2026-06-20 06:33:09', NULL, NULL, '2026-06-20 06:32:06', '2026-06-20 06:33:09'),
(70, 'ORD_20260620063238_VXBXXRUX', 1, 1, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.01', 5, 'pending', 'ORD_ORD_20260620063238_VXBXXRUX_20260620063239', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260620063238_VXBXXRUX_20260620063239&amount=0.01&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=2a9cedca16b8818c6ae2fbe824fed9ad23af0f35', '2a9cedca16b8818c6ae2fbe824fed9ad23af0f35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-20 06:32:38', '2026-06-20 06:32:39'),
(71, 'ORD_20260620133348_AGLJFBBL', 1, 1, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.01', 5, 'success', 'ORD_ORD_20260620133348_AGLJFBBL_20260620133350', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260620133348_AGLJFBBL_20260620133350&amount=0.01&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=6763326ddace5174a308902f06d9d2851e898d87', '6763326ddace5174a308902f06d9d2851e898d87', NULL, NULL, NULL, '2026-06-20 13:36:03', NULL, '2026-06-20 13:36:03', NULL, NULL, '2026-06-20 13:33:48', '2026-06-20 13:36:03'),
(72, 'ORD_20260621123016_QUQO6G0L', 1, 5, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.04', 250, 'pending', 'ORD_ORD_20260621123016_QUQO6G0L_20260621123017', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260621123016_QUQO6G0L_20260621123017&amount=0.04&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=1867275788ac8bf35535bc4dc58c5f751b0a649b', '1867275788ac8bf35535bc4dc58c5f751b0a649b', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-21 12:30:16', '2026-06-21 12:30:17'),
(73, 'ORD_20260622132212_YYHK8YPD', 1, 10, '702425515', 'DyzzIsMine', '10301', 'khqr', '1.40', 90, 'pending', 'ORD_ORD_20260622132212_YYHK8YPD_20260622132213', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260622132212_YYHK8YPD_20260622132213&amount=1.40&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=b6450ab0c8cfef4a6d24234dc383d55c8f06e50a', 'b6450ab0c8cfef4a6d24234dc383d55c8f06e50a', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-22 13:22:12', '2026-06-22 13:22:14'),
(74, 'ORD_20260622132225_1OTSH7US', 1, 1, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.01', 5, 'success', 'ORD_ORD_20260622132225_1OTSH7US_20260622132226', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260622132225_1OTSH7US_20260622132226&amount=0.01&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=7166d0b3abc1f74648c88da22e0f835e671659a0', '7166d0b3abc1f74648c88da22e0f835e671659a0', NULL, NULL, NULL, '2026-06-22 13:22:54', NULL, '2026-06-22 13:22:54', NULL, NULL, '2026-06-22 13:22:25', '2026-06-22 13:22:54'),
(75, 'ORD_20260622133515_ZQRKA0RX', 1, 10, '11', '', '11', 'khqr', '1.40', 90, 'pending', 'ORD_ORD_20260622133515_ZQRKA0RX_20260622133515', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260622133515_ZQRKA0RX_20260622133515&amount=1.40&remark=MLBB%7CID%3A11%7CS%3A11&hash=803033261753629e2803a2b9ecea9ad84ebf0a7f', '803033261753629e2803a2b9ecea9ad84ebf0a7f', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-22 13:35:15', '2026-06-22 13:35:16'),
(76, 'ORD_20260622133548_VTENVGBQ', 1, 10, '1', '', '1', 'khqr', '1.40', 90, 'pending', 'ORD_ORD_20260622133548_VTENVGBQ_20260622133549', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260622133548_VTENVGBQ_20260622133549&amount=1.40&remark=MLBB%7CID%3A1%7CS%3A1&hash=718ae71588f68035136aa928f393e6d3c878127a', '718ae71588f68035136aa928f393e6d3c878127a', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-22 13:35:48', '2026-06-22 13:35:49'),
(77, 'ORD_20260623010721_DW2ZCFTE', 1, 1, '248683587', 'Papoyy Papoyy', '3536', 'khqr', '0.01', 5, 'pending', 'ORD_ORD_20260623010721_DW2ZCFTE_20260623010722', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260623010721_DW2ZCFTE_20260623010722&amount=0.01&remark=MLBB%7CID%3A248683587%7CS%3A3536&hash=0592d21fcdd4c6d537b46145a673724ec8c6b8cf', '0592d21fcdd4c6d537b46145a673724ec8c6b8cf', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-23 01:07:21', '2026-06-23 01:07:22'),
(78, 'ORD_20260623142101_MKEHGT1G', 1, 10, '702425515', '', '10301', 'khqr', '1.40', 90, 'pending', 'ORD_ORD_20260623142101_MKEHGT1G_20260623142101', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260623142101_MKEHGT1G_20260623142101&amount=1.40&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=7ae5b0b0f4e18e388c481397a86b34b8666dfe0f', '7ae5b0b0f4e18e388c481397a86b34b8666dfe0f', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-23 14:21:01', '2026-06-23 14:21:02'),
(79, 'ORD_20260623143021_GKYXRW1L', 1, 1, '702425515', '', '10301', 'khqr', '0.01', 5, 'pending', 'ORD_ORD_20260623143021_GKYXRW1L_20260623143021', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260623143021_GKYXRW1L_20260623143021&amount=0.01&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=3f08ff3c282e65f5d9962445761f19fe11edb405', '3f08ff3c282e65f5d9962445761f19fe11edb405', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-23 14:30:21', '2026-06-23 14:30:22'),
(80, 'ORD_20260623144735_S5IVBAPZ', 1, 1, '702425515', '', '10301', 'khqr', '0.01', 5, 'pending', 'ORD_ORD_20260623144735_S5IVBAPZ_20260623144735', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260623144735_S5IVBAPZ_20260623144735&amount=0.01&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=c14814ecaaaa56fbafa98e0b5ac7c10840223e3b', 'c14814ecaaaa56fbafa98e0b5ac7c10840223e3b', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-23 14:47:35', '2026-06-23 14:47:36'),
(81, 'ORD_20260623144802_XVX6U9LD', 1, 10, '702425515', '', '10301', 'khqr', '1.40', 90, 'pending', 'ORD_ORD_20260623144802_XVX6U9LD_20260623144802', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260623144802_XVX6U9LD_20260623144802&amount=1.40&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=e174f0c5c11709bf1ccfb1875747770e547cc0d2', 'e174f0c5c11709bf1ccfb1875747770e547cc0d2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-23 14:48:02', '2026-06-23 14:48:03'),
(82, 'ORD_20260623145105_W067ADWS', 1, 10, '702425515', '', '10301', 'khqr', '1.40', 90, 'pending', 'ORD_ORD_20260623145105_W067ADWS_20260623145105', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260623145105_W067ADWS_20260623145105&amount=1.40&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=4adfeea8522f056f87f0f1a1198af9b4c8563ce5', '4adfeea8522f056f87f0f1a1198af9b4c8563ce5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-23 14:51:05', '2026-06-23 14:51:06'),
(83, 'ORD_20260623145506_JVUON22N', 1, 10, '702425515', '', '10301', 'khqr', '0.85', 55, 'success', 'ORD_ORD_20260623145506_JVUON22N_20260623145507', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260623145506_JVUON22N_20260623145507&amount=0.85&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=d9010d1fe650eafd2cf57ac803f0d341017fc16e', 'd9010d1fe650eafd2cf57ac803f0d341017fc16e', NULL, NULL, NULL, '2026-06-23 14:55:39', NULL, '2026-06-23 14:55:39', NULL, NULL, '2026-06-23 14:55:06', '2026-06-23 14:55:39'),
(84, 'ORD_20260623161823_7TL4NTVJ', 1, 4, '1109565493', 'Reitzuu.', '13470', 'khqr', '0.90', 55, 'pending', 'ORD_ORD_20260623161823_7TL4NTVJ_20260623161823', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260623161823_7TL4NTVJ_20260623161823&amount=0.90&remark=MLBB%7CID%3A1109565493%7CS%3A13470&hash=e63a0dc5dfc4fa752ce6af7a56fc40abea8c1119', 'e63a0dc5dfc4fa752ce6af7a56fc40abea8c1119', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-23 16:18:23', '2026-06-23 16:18:24'),
(85, 'ORD_20260624054839_SDSLVOEH', 1, 10, '702425515', '', '10301', 'khqr', '0.85', 55, 'manual_hold', 'ORD_ORD_20260624054839_SDSLVOEH_20260624054840', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260624054839_SDSLVOEH_20260624054840&amount=0.85&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=198d9dfd97ad0ae051503116630f5d640de23eeb', '198d9dfd97ad0ae051503116630f5d640de23eeb', NULL, NULL, NULL, '2026-06-24 05:49:12', NULL, NULL, NULL, NULL, '2026-06-24 05:48:39', '2026-06-24 05:49:14'),
(86, 'ORD_20260624084241_OQROQVFR', 1, 10, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.85', 55, 'manual_hold', 'ORD_ORD_20260624084241_OQROQVFR_20260624084242', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260624084241_OQROQVFR_20260624084242&amount=0.85&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=2b0505340a15404c7a2433e1cb7ebbc4fd2e134b', '2b0505340a15404c7a2433e1cb7ebbc4fd2e134b', NULL, NULL, NULL, '2026-06-24 08:43:07', NULL, NULL, NULL, NULL, '2026-06-24 08:42:41', '2026-06-24 08:43:10'),
(87, 'ORD_20260624094239_7DI13UEO', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'manual_hold', 'ORD_ORD_20260624094239_7DI13UEO_20260624094240', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260624094239_7DI13UEO_20260624094240&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=92109f86a3c729cc7acb10b195e132d03c58cf3f', '92109f86a3c729cc7acb10b195e132d03c58cf3f', NULL, NULL, NULL, '2026-06-24 09:43:31', NULL, NULL, NULL, NULL, '2026-06-24 09:42:39', '2026-06-24 09:43:34'),
(88, 'ORD_20260624133022_UR4VXB9Z', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20260624133022_UR4VXB9Z_20260624133023', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260624133022_UR4VXB9Z_20260624133023&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=3273ecf1f2b87988be5b6a700c43b9ac502c378f', '3273ecf1f2b87988be5b6a700c43b9ac502c378f', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-24 13:30:22', '2026-06-24 13:30:23'),
(89, 'ORD_20260624161838_1W2GBTA1', 12, 18, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.10', 5, 'pending', 'ORD_ORD_20260624161838_1W2GBTA1_20260624161839', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260624161838_1W2GBTA1_20260624161839&amount=0.10&remark=MLBB_EXCLUSIVE%7CID%3A702425515%7CS%3A10301&hash=4d36d5651b87478c75313ac3ec1e50e75a973c33', '4d36d5651b87478c75313ac3ec1e50e75a973c33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-24 16:18:38', '2026-06-24 16:18:39'),
(90, 'ORD_20260624161920_NQRH7UYK', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20260624161920_NQRH7UYK_20260624161921', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260624161920_NQRH7UYK_20260624161921&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=ed80c3037c19e2cf6e079c390b30f73da2e85c60', 'ed80c3037c19e2cf6e079c390b30f73da2e85c60', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-24 16:19:20', '2026-06-24 16:19:21'),
(91, 'ORD_20260624162323_CLZTS7C2', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20260624162323_CLZTS7C2_20260624162323', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260624162323_CLZTS7C2_20260624162323&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=c88f3fac7c42feb34f076cd296fcb3d2a8e29895', 'c88f3fac7c42feb34f076cd296fcb3d2a8e29895', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-24 16:23:23', '2026-06-24 16:23:24'),
(92, 'ORD_20260624162334_WEAO3FU0', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20260624162334_WEAO3FU0_20260624162334', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260624162334_WEAO3FU0_20260624162334&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=9bc1597fb87db2ace5b07f69903a857aa0229e6a', '9bc1597fb87db2ace5b07f69903a857aa0229e6a', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-24 16:23:34', '2026-06-24 16:23:35'),
(93, 'ORD_20260624162458_YUN3713E', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20260624162458_YUN3713E_20260624162459', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260624162458_YUN3713E_20260624162459&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=377fe2f9b80a58b0aab7ad1833c4be7e762c7442', '377fe2f9b80a58b0aab7ad1833c4be7e762c7442', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-24 16:24:58', '2026-06-24 16:24:59'),
(94, 'ORD_20260624162552_WOUSFM7O', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20260624162552_WOUSFM7O_20260624162552', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260624162552_WOUSFM7O_20260624162552&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=4902240fc429d990df10b2717b6c41d6ce292656', '4902240fc429d990df10b2717b6c41d6ce292656', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-24 16:25:52', '2026-06-24 16:25:53'),
(95, 'ORD_20260624163852_T5P1Y5EI', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20260624163852_T5P1Y5EI_20260624163853', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260624163852_T5P1Y5EI_20260624163853&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=3865a172d30d47dc1b2f5e549320f2c4c50504d2', '3865a172d30d47dc1b2f5e549320f2c4c50504d2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-24 16:38:52', '2026-06-24 16:38:53'),
(96, 'ORD_20260624164228_HSYRR3YK', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20260624164228_HSYRR3YK_20260624164228', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260624164228_HSYRR3YK_20260624164228&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=9722a7ce9565ec23cce45fd648ceaf4b21270aba', '9722a7ce9565ec23cce45fd648ceaf4b21270aba', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-24 16:42:28', '2026-06-24 16:42:29'),
(97, 'ORD_20260624164606_FXXLSHRC', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20260624164606_FXXLSHRC_20260624164606', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260624164606_FXXLSHRC_20260624164606&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=94dadf95762c3c32235b3eecfbe52156ab9e8f05', '94dadf95762c3c32235b3eecfbe52156ab9e8f05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-24 16:46:06', '2026-06-24 16:46:07'),
(98, 'ORD_20260624165852_LQWY2HWX', 12, 18, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.10', 5, 'pending', 'ORD_ORD_20260624165852_LQWY2HWX_20260624165853', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260624165852_LQWY2HWX_20260624165853&amount=0.10&remark=MLBB_EXCLUSIVE%7CID%3A702425515%7CS%3A10301&hash=1c9c957be43874bf2152dfdd8f8f7cf445cd05e6', '1c9c957be43874bf2152dfdd8f8f7cf445cd05e6', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-24 16:58:52', '2026-06-24 16:58:53'),
(99, 'ORD_20260624172300_YARGPCMT', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20260624172300_YARGPCMT_20260624172301', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260624172300_YARGPCMT_20260624172301&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=2a18060f4c8d8592c5e21bd010978f0329146120', '2a18060f4c8d8592c5e21bd010978f0329146120', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-24 17:23:00', '2026-06-24 17:23:01'),
(100, 'ORD_20260624230546_KNIGGMVF', 12, 18, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.10', 5, 'manual_hold', 'ORD_ORD_20260624230546_KNIGGMVF_20260624230546', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260624230546_KNIGGMVF_20260624230546&amount=0.10&remark=MLBB_EXCLUSIVE%7CID%3A702425515%7CS%3A10301&hash=3876fbe03934eb0d300b6c0f33502d6694801ca6', '3876fbe03934eb0d300b6c0f33502d6694801ca6', NULL, NULL, NULL, '2026-10-08 03:14:34', NULL, NULL, NULL, NULL, '2026-06-24 23:05:46', '2026-10-08 03:14:36'),
(101, 'ORD_20260625015726_WZXDBEJX', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20260625015726_WZXDBEJX_20260625015727', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260625015726_WZXDBEJX_20260625015727&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=c50ab52937dcc6bee00b8724ad65db1949c7969f', 'c50ab52937dcc6bee00b8724ad65db1949c7969f', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-25 01:57:26', '2026-06-25 01:57:28'),
(102, 'ORD_20260625024013_L4YML1HZ', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'manual_hold', 'ORD_ORD_20260625024013_L4YML1HZ_20260625024014', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260625024013_L4YML1HZ_20260625024014&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=4570c6a0adce7502388f40b98ef5246509637c9d', '4570c6a0adce7502388f40b98ef5246509637c9d', NULL, NULL, NULL, '2026-06-25 02:41:10', NULL, NULL, NULL, NULL, '2026-06-25 02:40:13', '2026-06-25 02:41:13'),
(103, 'ORD_20260625025625_CKEZM55P', 12, 18, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.10', 5, 'success', 'ORD_ORD_20260625025625_CKEZM55P_20260625025626', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260625025625_CKEZM55P_20260625025626&amount=0.10&remark=MLBB_EXCLUSIVE%7CID%3A702425515%7CS%3A10301&hash=939358a681f741ce5ae81fae71ed12956c7a48c7', '939358a681f741ce5ae81fae71ed12956c7a48c7', NULL, NULL, NULL, '2026-06-25 02:56:55', NULL, NULL, NULL, NULL, '2026-06-25 02:56:25', '2026-06-25 02:56:58'),
(104, 'ORD_20260625031244_QAZFVTQJ', 12, 18, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.10', 5, 'success', 'ORD_ORD_20260625031244_QAZFVTQJ_20260625031245', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260625031244_QAZFVTQJ_20260625031245&amount=0.10&remark=MLBB_EXCLUSIVE%7CID%3A702425515%7CS%3A10301&hash=66968e525a95ddde96414ef40b84b6d396522655', '66968e525a95ddde96414ef40b84b6d396522655', NULL, NULL, NULL, '2026-06-25 03:13:05', NULL, NULL, NULL, NULL, '2026-06-25 03:12:44', '2026-06-25 03:13:08'),
(105, 'ORD_20260625044335_ES4DM805', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'success', 'ORD_ORD_20260625044335_ES4DM805_20260625044335', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260625044335_ES4DM805_20260625044335&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=4b27be5c5cc6e8457f3b2e17a34fcff466dc5e14', '4b27be5c5cc6e8457f3b2e17a34fcff466dc5e14', NULL, NULL, NULL, '2026-06-25 04:43:59', NULL, NULL, NULL, NULL, '2026-06-25 04:43:35', '2026-06-25 04:44:02'),
(106, 'ORD_20260625053632_YMLZ3P2U', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20260625053632_YMLZ3P2U_20260625053633', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260625053632_YMLZ3P2U_20260625053633&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=022886dea3705a0be4c0bda7cda6c6fb0360fb3a', '022886dea3705a0be4c0bda7cda6c6fb0360fb3a', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-25 05:36:32', '2026-06-25 05:36:33'),
(107, 'ORD_20260625053702_91LXXWHR', 1, 31, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.92', 55, 'success', 'ORD_ORD_20260625053702_91LXXWHR_20260625053702', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260625053702_91LXXWHR_20260625053702&amount=0.92&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=cff7be41947a675d7dcfc8b0ef2e82d83fa7e6aa', 'cff7be41947a675d7dcfc8b0ef2e82d83fa7e6aa', NULL, NULL, NULL, '2026-06-25 05:37:46', NULL, NULL, NULL, NULL, '2026-06-25 05:37:02', '2026-06-25 05:37:49'),
(108, 'ORD_20260625054744_KFRG2CJE', 1, 31, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.92', 55, 'manual_hold', 'ORD_ORD_20260625054744_KFRG2CJE_20260625054745', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260625054744_KFRG2CJE_20260625054745&amount=0.92&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=a88b4662f6f6a6bc9c0c28921a213e22843f207b', 'a88b4662f6f6a6bc9c0c28921a213e22843f207b', NULL, NULL, NULL, '2026-06-25 05:48:13', NULL, NULL, NULL, NULL, '2026-06-25 05:47:44', '2026-06-25 05:48:16'),
(109, 'ORD_20260625060027_TSYRSTFE', 1, 31, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.92', 55, 'manual_hold', 'ORD_ORD_20260625060027_TSYRSTFE_20260625060028', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260625060027_TSYRSTFE_20260625060028&amount=0.92&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=df2251fce10453dcfb4e31e6b616ac6d7ca0a388', 'df2251fce10453dcfb4e31e6b616ac6d7ca0a388', NULL, NULL, NULL, '2026-06-25 06:00:52', NULL, NULL, NULL, NULL, '2026-06-25 06:00:27', '2026-06-25 06:00:54');
INSERT INTO `topup_orders` (`id`, `order_no`, `topup_game_id`, `topup_package_id`, `player_id`, `player_username`, `zone_id`, `payment_method`, `amount`, `diamond_amount`, `status`, `gateway_transaction_id`, `gateway_checkout_url`, `gateway_hash`, `gateway_payload`, `supplier_order_id`, `supplier_payload`, `paid_at`, `processing_at`, `success_at`, `failed_at`, `failure_reason`, `created_at`, `updated_at`) VALUES
(110, 'ORD_20260625061024_JBJIEPOV', 1, 31, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.92', 55, 'success', 'ORD_ORD_20260625061024_JBJIEPOV_20260625061024', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260625061024_JBJIEPOV_20260625061024&amount=0.92&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=9ce0f2b85729da125be7ef02499373a64a558e5d', '9ce0f2b85729da125be7ef02499373a64a558e5d', NULL, NULL, NULL, '2026-06-25 06:10:47', NULL, NULL, NULL, NULL, '2026-06-25 06:10:24', '2026-06-25 06:16:31'),
(111, 'ORD_20260625061749_NV6FLLAH', 1, 31, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.92', 55, 'success', 'ORD_ORD_20260625061749_NV6FLLAH_20260625061750', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260625061749_NV6FLLAH_20260625061750&amount=0.92&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=1bb2184b3e41ba7c9b0cb3ffb79e6cb436508bf7', '1bb2184b3e41ba7c9b0cb3ffb79e6cb436508bf7', NULL, NULL, NULL, '2026-06-25 06:18:13', NULL, NULL, NULL, NULL, '2026-06-25 06:17:49', '2026-06-25 06:18:41'),
(112, 'ORD_20260625063704_EZIB4KBN', 1, 31, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.92', 55, 'manual_hold', 'ORD_ORD_20260625063704_EZIB4KBN_20260625063705', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260625063704_EZIB4KBN_20260625063705&amount=0.92&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=6ef117dc57e33c6c2fc074a610eec165b3b825f0', '6ef117dc57e33c6c2fc074a610eec165b3b825f0', NULL, NULL, NULL, '2026-06-25 06:54:02', NULL, NULL, NULL, NULL, '2026-06-25 06:37:04', '2026-06-25 06:54:05'),
(113, 'ORD_20260625065718_VGUVLZNN', 1, 10, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.45', 55, 'manual_hold', 'ORD_ORD_20260625065718_VGUVLZNN_20260625065718', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260625065718_VGUVLZNN_20260625065718&amount=0.45&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=aea4f6777af58890d7a6e6262d164844f741ea31', 'aea4f6777af58890d7a6e6262d164844f741ea31', NULL, NULL, NULL, '2026-06-25 06:58:18', NULL, NULL, NULL, NULL, '2026-06-25 06:57:18', '2026-06-25 06:58:20'),
(114, 'ORD_20260625070640_KBY0AVD4', 1, 10, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.45', 55, 'manual_hold', 'ORD_ORD_20260625070640_KBY0AVD4_20260625070641', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260625070640_KBY0AVD4_20260625070641&amount=0.45&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=c16be59bd8bc02cc11b38c251c33971901841d1e', 'c16be59bd8bc02cc11b38c251c33971901841d1e', NULL, NULL, NULL, '2026-06-25 07:07:28', NULL, NULL, NULL, NULL, '2026-06-25 07:06:40', '2026-06-25 07:07:30'),
(116, 'ORD_20260625073702_JZN8LXET', 1, 10, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.45', 55, 'manual_hold', 'ORD_ORD_20260625073702_JZN8LXET_20260625073703', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260625073702_JZN8LXET_20260625073703&amount=0.45&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=344d85a8045b97830fb9e8a2ef4bc30002b2634d', '344d85a8045b97830fb9e8a2ef4bc30002b2634d', NULL, NULL, NULL, '2026-06-25 09:11:10', NULL, NULL, NULL, NULL, '2026-06-25 07:37:02', '2026-06-25 09:11:13'),
(118, 'ORD_20260625095842_NM5XCUSR', 1, 10, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.45', 55, 'success', 'ORD_ORD_20260625095842_NM5XCUSR_20260625095843', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260625095842_NM5XCUSR_20260625095843&amount=0.45&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=b7a6dce708bb89ee5ee81857c8eecea50ff6d529', 'b7a6dce708bb89ee5ee81857c8eecea50ff6d529', NULL, NULL, NULL, '2026-06-25 09:59:14', NULL, '2026-06-25 09:59:19', NULL, NULL, '2026-06-25 09:58:42', '2026-06-25 09:59:19'),
(119, 'ORD_20260625100503_CM4EERTH', 1, 31, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.92', 55, 'success', 'ORD_ORD_20260625100503_CM4EERTH_20260625100504', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260625100503_CM4EERTH_20260625100504&amount=0.92&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=dd9ebe1949c04dc8afce2b3272179180a119cba1', 'dd9ebe1949c04dc8afce2b3272179180a119cba1', NULL, NULL, NULL, '2026-06-25 10:05:26', NULL, '2026-06-25 10:05:31', NULL, NULL, '2026-06-25 10:05:03', '2026-06-25 10:05:31'),
(122, 'ORD_20260625105350_PYP62LKT', 1, 3, '1245178176', 'Syn.', '14013', 'khqr', '1.58', 220, 'success', 'ORD_ORD_20260625105350_PYP62LKT_20260625105351', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260625105350_PYP62LKT_20260625105351&amount=1.58&remark=MLBB%7CID%3A1245178176%7CS%3A14013&hash=9042137754bd11a2f6411d566cc8bd5e69a09718', '9042137754bd11a2f6411d566cc8bd5e69a09718', NULL, NULL, NULL, '2026-06-25 10:55:15', NULL, '2026-06-25 10:55:22', NULL, NULL, '2026-06-25 10:53:50', '2026-06-25 10:55:22'),
(124, 'ORD_20260627032234_0V42H2KZ', 12, 18, '1167656763', '☠WANG⚰YANN亗', '11351', 'khqr', '0.11', 5, 'success', 'ORD_ORD_20260627032234_0V42H2KZ_20260627032235', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260627032234_0V42H2KZ_20260627032235&amount=0.11&remark=MLBB_EXCLUSIVE%7CID%3A1167656763%7CS%3A11351&hash=561115bcad82d5bd6b3cbfd3c8aaa9f027596995', '561115bcad82d5bd6b3cbfd3c8aaa9f027596995', NULL, NULL, NULL, '2026-06-27 03:23:32', NULL, '2026-06-27 03:23:37', NULL, NULL, '2026-06-27 03:22:34', '2026-06-27 03:23:37'),
(125, 'ORD_20260627034240_NKUBDBYB', 1, 31, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.88', 55, 'success', 'ORD_ORD_20260627034240_NKUBDBYB_20260627034241', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260627034240_NKUBDBYB_20260627034241&amount=0.88&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=f2230c921cae4729bc1ff1506d86c2601b1dfdf5', 'f2230c921cae4729bc1ff1506d86c2601b1dfdf5', NULL, NULL, NULL, '2026-06-27 04:02:22', NULL, '2026-06-27 04:02:28', NULL, NULL, '2026-06-27 03:42:40', '2026-06-27 04:02:28'),
(126, 'ORD_20260721084256_XWJMCM7E', 1, 3, '1923464841', 'Rathana desro Ozze', '14921', 'khqr', '1.50', 220, 'success', 'ORD_ORD_20260721084256_XWJMCM7E_20260721084256', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260721084256_XWJMCM7E_20260721084256&amount=1.50&remark=MLBB%7CID%3A1923464841%7CS%3A14921&hash=e88e0442ac3ff5230794eca71fddb7e47659e2f6', 'e88e0442ac3ff5230794eca71fddb7e47659e2f6', NULL, NULL, NULL, '2026-07-21 08:43:23', NULL, '2026-07-21 08:44:27', NULL, NULL, '2026-07-21 08:42:56', '2026-07-21 08:44:27'),
(127, 'ORD_20260721084447_T4HCYYDC', 1, 3, '1923464841', 'Rathana desro Ozze', '14921', 'khqr', '1.50', 220, 'success', 'ORD_ORD_20260721084447_T4HCYYDC_20260721084448', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260721084447_T4HCYYDC_20260721084448&amount=1.50&remark=MLBB%7CID%3A1923464841%7CS%3A14921&hash=f5411df97fe2f4a95d6ce0a9489f1f611ddf73c4', 'f5411df97fe2f4a95d6ce0a9489f1f611ddf73c4', NULL, NULL, NULL, '2026-07-21 08:46:14', NULL, '2026-07-21 08:48:28', NULL, NULL, '2026-07-21 08:44:47', '2026-07-21 08:48:28'),
(128, 'ORD_20260725025521_G6W8KQ0E', 12, 18, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.11', 5, 'success', 'ORD_ORD_20260725025521_G6W8KQ0E_20260725025522', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260725025521_G6W8KQ0E_20260725025522&amount=0.11&remark=MLBB_EXCLUSIVE%7CID%3A702425515%7CS%3A10301&hash=43f22c7176ca75fa1370a073a9797a83e5c3ba5b', '43f22c7176ca75fa1370a073a9797a83e5c3ba5b', NULL, NULL, NULL, '2026-07-25 03:04:41', NULL, '2026-07-25 03:09:48', NULL, NULL, '2026-07-25 02:55:21', '2026-07-25 03:09:48'),
(129, 'ORD_20260725030912_LCAERBG2', 12, 18, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.11', 5, 'success', 'ORD_ORD_20260725030912_LCAERBG2_20260725030912', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260725030912_LCAERBG2_20260725030912&amount=0.11&remark=MLBB_EXCLUSIVE%7CID%3A702425515%7CS%3A10301&hash=bdb48ac388c7beb8e8cb05cae33726ef8da10147', 'bdb48ac388c7beb8e8cb05cae33726ef8da10147', NULL, NULL, NULL, '2026-07-25 03:09:33', NULL, '2026-07-25 03:12:08', NULL, NULL, '2026-07-25 03:09:12', '2026-07-25 03:12:08'),
(130, 'ORD_20260725122820_HP1ORCGU', 12, 18, '1269731897', 'Snaaaz', '14048', 'khqr', '0.11', 5, 'success', 'ORD_ORD_20260725122820_HP1ORCGU_20260725122821', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260725122820_HP1ORCGU_20260725122821&amount=0.11&remark=MLBB_EXCLUSIVE%7CID%3A1269731897%7CS%3A14048&hash=573b5a49c08ed101659d29799a91ddc79ad84608', '573b5a49c08ed101659d29799a91ddc79ad84608', NULL, NULL, NULL, '2026-07-25 12:29:19', NULL, '2026-07-25 12:29:31', NULL, NULL, '2026-07-25 12:28:20', '2026-07-25 12:29:31'),
(131, 'ORD_20260812114053_PGE6SEXY', 1, 3, '1245178176', 'Syn.', '14013', 'khqr', '1.50', 220, 'success', 'ORD_ORD_20260812114053_PGE6SEXY_20260812114054', 'https://khqr.cc/api/payment/request/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20260812114053_PGE6SEXY_20260812114054&amount=1.50&remark=MLBB%7CID%3A1245178176%7CS%3A14013&hash=ed2c45cdacddb7f999682ee2cc0a1bdb4a6edf3d', 'ed2c45cdacddb7f999682ee2cc0a1bdb4a6edf3d', NULL, NULL, NULL, '2026-08-12 11:48:18', NULL, '2026-08-12 11:48:47', NULL, NULL, '2026-08-12 11:40:53', '2026-08-12 11:48:47'),
(161, 'ORD_20261007095549_DB5Q7GZO', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20261007095549_DB5Q7GZO_20261007095550', 'https://khqr.cc/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261007095549_DB5Q7GZO_20261007095550&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=cecdbb58c2c97f90aa818612f17efeb31d406d53', 'cecdbb58c2c97f90aa818612f17efeb31d406d53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-07 09:55:49', '2026-10-07 09:55:50'),
(163, 'ORD_20261008030118_QWXFOHVD', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20261008030118_QWXFOHVD_20261008030119', 'https://khqr.cc/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261008030118_QWXFOHVD_20261008030119&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=02139fd9ee9212f0f6c268611825881ec3682246', '02139fd9ee9212f0f6c268611825881ec3682246', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 03:01:18', '2026-10-08 03:01:19'),
(164, 'ORD_20261008030358_USUTTDVO', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20261008030358_USUTTDVO_20261008030359', 'https://khqr.cc/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261008030358_USUTTDVO_20261008030359&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=6e4982714e5dc06d5cb4f8f19b2a887efc68331e', '6e4982714e5dc06d5cb4f8f19b2a887efc68331e', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 03:03:58', '2026-10-08 03:03:59'),
(165, 'ORD_20261008030848_DVGJJX0A', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20261008030848_DVGJJX0A_20261008030849', 'https://khqr.cc/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261008030848_DVGJJX0A_20261008030849&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=1eaed9ca5cb6758dcfd49ce5415a9a69cc90e048', '1eaed9ca5cb6758dcfd49ce5415a9a69cc90e048', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 03:08:48', '2026-10-08 03:08:49'),
(167, 'ORD_20261008040731_KJPYVHSR', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20261008040731_KJPYVHSR_20261008040731', 'https://khqr.cc/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261008040731_KJPYVHSR_20261008040731&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=1da6fa6500c0797e0aa4771f902185749f830423', '1da6fa6500c0797e0aa4771f902185749f830423', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-08 04:07:31', '2026-10-08 04:07:32'),
(168, 'ORD_20261009051859_QHACDI34', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20261009051859_QHACDI34_20261009051859', 'https://khqr.cc/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261009051859_QHACDI34_20261009051859&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=58f88276d6079dbeefcb31b79654782a595d4cca', '58f88276d6079dbeefcb31b79654782a595d4cca', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-09 05:18:59', '2026-10-09 05:19:00'),
(169, 'ORD_20261013024153_M7UYTLBP', 1, 3, '702425515', 'DyzzIsMine', '10301', 'khqr', '1.50', 220, 'pending', 'ORD_ORD_20261013024153_M7UYTLBP_20261013024154', 'https://khqr.cc/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261013024153_M7UYTLBP_20261013024154&amount=1.50&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=458d4f6c6913820c18a059d7baa9bbc4d57da516', '458d4f6c6913820c18a059d7baa9bbc4d57da516', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 02:41:53', '2026-10-13 02:41:54'),
(170, 'ORD_20261013024236_WMIPIZZ8', 1, 14, '702425515', 'DyzzIsMine', '10301', 'khqr', '3.99', 275, 'pending', 'ORD_ORD_20261013024236_WMIPIZZ8_20261013024236', 'https://khqr.cc/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261013024236_WMIPIZZ8_20261013024236&amount=3.99&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=ba56a94e598109cfe0a99117c1d936b2d7dbda0e', 'ba56a94e598109cfe0a99117c1d936b2d7dbda0e', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 02:42:36', '2026-10-13 02:42:37'),
(171, 'ORD_20261015155618_J6NRVGNS', 1, 17, '702425515', '', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20261015155618_J6NRVGNS_20261015155619', 'https://khqr.cc/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261015155618_J6NRVGNS_20261015155619&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=a0c1a09fc37516eb7fe7769dd43fd83926b9c34a', 'a0c1a09fc37516eb7fe7769dd43fd83926b9c34a', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 15:56:18', '2026-10-15 15:56:19'),
(172, 'ORD_20261015160117_9XNFIACA', 1, 17, '702425515', '', '10301', 'payway', '0.29', 11, 'pending', 'ORD_ORD_20261015160117_9XNFIACA_20261015160117', 'https://khqr.cc/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261015160117_9XNFIACA_20261015160117&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=44dfe95a72eef33f3d7e68b00943e6e791c20ad4', '44dfe95a72eef33f3d7e68b00943e6e791c20ad4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 16:01:17', '2026-10-15 16:01:18'),
(173, 'ORD_20261015160623_7AGACATV', 1, 17, '234', '', '234', 'payway', '0.29', 11, 'pending', 'ORD_ORD_20261015160623_7AGACATV_20261015160623', 'https://khqr.cc/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261015160623_7AGACATV_20261015160623&amount=0.29&remark=MLBB%7CID%3A234%7CS%3A234&hash=59e781baecdc2d44de4cd002e1ad1a6c1b3b16e7', '59e781baecdc2d44de4cd002e1ad1a6c1b3b16e7', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 16:06:23', '2026-10-15 16:06:24'),
(174, 'ORD_20261015160652_EHYLT98Y', 1, 17, '234', '', '234', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20261015160652_EHYLT98Y_20261015160653', 'https://khqr.cc/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261015160652_EHYLT98Y_20261015160653&amount=0.29&remark=MLBB%7CID%3A234%7CS%3A234&hash=6ca253ee6bfa7aaed1f734fc58dd40e02cb1e2e9', '6ca253ee6bfa7aaed1f734fc58dd40e02cb1e2e9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 16:06:52', '2026-10-15 16:06:53'),
(175, 'ORD_20261015172523_LOPRNR7G', 1, 17, '702425515', '', '10301', 'payway', '0.29', 11, 'pending', 'ORD_ORD_20261015172523_LOPRNR7G_20261015172524', 'https://link.payway.com.kh/ABAPAY9s540501U', '6a090830209c0e3915f824c48a8a984c632ae0a5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 17:25:23', '2026-10-15 17:25:24'),
(176, 'ORD_20261015172732_EMLMW0UR', 1, 17, '702425515', '', '10301', 'payway', '0.29', 11, 'pending', 'ORD_ORD_20261015172732_EMLMW0UR_20261015172733', 'https://link.payway.com.kh/ABAPAY9s540501U', '85ce25ee8250445c45dae1d998a15db0a296f143', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 17:27:32', '2026-10-15 17:27:33'),
(177, 'ORD_20261015172754_NYQF79O4', 1, 17, '702425515', '', '10301', 'payway', '0.29', 11, 'pending', 'ORD_ORD_20261015172754_NYQF79O4_20261015172755', 'https://link.payway.com.kh/ABAPAY9s540501U', '84949e8894be225d8d824dcc1631d7b3b9260fba', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 17:27:54', '2026-10-15 17:27:55'),
(178, 'ORD_20261015172805_BJWDCS83', 1, 17, '702425515', '', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20261015172805_BJWDCS83_20261015172805', 'https://khqr.cc/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261015172805_BJWDCS83_20261015172805&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=683a7c7b7d18ce24a836c6f0d2d88b140a2df840', '683a7c7b7d18ce24a836c6f0d2d88b140a2df840', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 17:28:05', '2026-10-15 17:28:06'),
(179, 'ORD_20261015173122_TWZHHWZX', 1, 17, '792425515', '', '10301', 'payway', '0.29', 11, 'pending', 'ORD_ORD_20261015173122_TWZHHWZX_20261015173122', 'https://link.payway.com.kh/ABAPAY9s540501U', '4141236d5a0d0edae385d3ce5a25750cc65c5c62', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 17:31:22', '2026-10-15 17:31:23'),
(180, 'ORD_20261015173144_OWG53K0G', 1, 17, '792425515', '', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20261015173144_OWG53K0G_20261015173144', 'https://khqr.cc/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261015173144_OWG53K0G_20261015173144&amount=0.29&remark=MLBB%7CID%3A792425515%7CS%3A10301&hash=4374e55a5b647f6c9722df063a2eca84a18d41ea', '4374e55a5b647f6c9722df063a2eca84a18d41ea', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 17:31:44', '2026-10-15 17:31:45'),
(181, 'ORD_20261016054733_06X7AROT', 1, 17, '702425515', '', '10301', 'payway', '0.29', 11, 'pending', 'ORD_ORD_20261016054733_06X7AROT_20261016054734', 'https://link.payway.com.kh/ABAPAY9s540501U', 'ffc99a7f2cd9dbcdb5a144a00e0425eaa8f9ad2e', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-16 05:47:33', '2026-10-16 05:47:35'),
(182, 'ORD_20261016054744_38WNSFDD', 1, 17, '702425515', '', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20261016054744_38WNSFDD_20261016054744', 'https://khqr.cc/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261016054744_38WNSFDD_20261016054744&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=795634a21e7740181f12f8f6902a69523b2b96cd', '795634a21e7740181f12f8f6902a69523b2b96cd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-16 05:47:44', '2026-10-16 05:47:44'),
(183, 'ORD_20261020175753_8M01XXXY', 1, 17, '702425515', 'DyzzIsMine', '10301', 'payway', '0.29', 11, 'pending', 'ORD_ORD_20261020175753_8M01XXXY_20261020175754', 'https://link.payway.com.kh/ABAPAY9s540501U', '414549dca28396e7aadf60f08f01f0fa1c989c79', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-20 17:57:53', '2026-10-20 17:57:55'),
(184, 'ORD_20261022023840_4YSHHZGR', 1, 17, '702425515', 'DyzzIsMine', '10301', 'payway', '0.29', 11, 'pending', 'ORD_ORD_20261022023840_4YSHHZGR_20261022023841', 'https://link.payway.com.kh/ABAPAY9s540501U', 'a6124aa5bf0a135beed162ca86f61b4b4b8244f5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-22 02:38:40', '2026-10-22 02:38:41'),
(185, 'ORD_20261022034349_U9FUOEJC', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20261022034349_U9FUOEJC_20261022034350', 'https://khqr.cc/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261022034349_U9FUOEJC_20261022034350&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=41a0b56a0878a098e0b31ba137634a60283da2f6', '41a0b56a0878a098e0b31ba137634a60283da2f6', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-22 03:43:49', '2026-10-22 03:43:50'),
(186, 'ORD_20261022034406_6TTM9CQB', 1, 17, '702425515', '', '10301', 'payway', '0.29', 11, 'pending', 'ORD_ORD_20261022034406_6TTM9CQB_20261022034407', 'https://link.payway.com.kh/ABAPAY9s540501U', '87ed2f29f6ad319fb7e95b3aca2f2345f220fa1a', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-22 03:44:06', '2026-10-22 03:44:07'),
(187, 'ORD_20261022035807_KR4B0RJ9', 1, 17, '702425515', '', '10301', 'payway', '0.29', 11, 'pending', 'ORD_ORD_20261022035807_KR4B0RJ9_20261022035808', 'https://link.payway.com.kh/ABAPAY9s540501U', 'a178e040444f14fc30c6e11afe4a8d61fda1adb7', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-22 03:58:07', '2026-10-22 03:58:08'),
(188, 'ORD_20261022035819_DATBJNWW', 1, 17, '702425515', '', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20261022035819_DATBJNWW_20261022035820', 'https://khqr.cc/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261022035819_DATBJNWW_20261022035820&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=342dbd3adc81c1cdb4e5325fb261152604ec508b', '342dbd3adc81c1cdb4e5325fb261152604ec508b', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-22 03:58:19', '2026-10-22 03:58:20'),
(189, 'ORD_20261022035843_ZOJJAXC4', 1, 17, '702425515', 'DyzzIsMine', '10301', 'payway', '0.29', 11, 'pending', 'ORD_ORD_20261022035843_ZOJJAXC4_20261022035844', 'https://link.payway.com.kh/ABAPAY9s540501U', 'ac04211268a8a8f8c90b69c1348c9303db0bc113', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-22 03:58:43', '2026-10-22 03:58:44'),
(202, 'ORD_20261107082953_GW4RDW', 1, 17, '702425515', 'DyzzIsMine', '10301', 'khqr', '0.29', 11, 'pending', 'ORD_ORD_20261107082953_GW4RDW', 'https://anajakpay.com/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261107082953_GW4RDW&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=9fd69e9b6158a85939c58a012ec64d60c35160bd&success_url=https%3A%2F%2Fbackend-1-lawx.onrender.com%2Fpayment-success.html&iframe=1', '9fd69e9b6158a85939c58a012ec64d60c35160bd', '{\"hash\": \"9fd69e9b6158a85939c58a012ec64d60c35160bd\", \"amount\": \"0.29\", \"remark\": \"MLBB|ID:702425515|S:10301\", \"success\": true, \"currency\": \"USD\", \"created_at\": \"2026-11-07T08:29:54+00:00\", \"iframe_url\": \"https://anajakpay.com/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261107082953_GW4RDW&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=9fd69e9b6158a85939c58a012ec64d60c35160bd&success_url=https%3A%2F%2Fbackend-1-lawx.onrender.com%2Fpayment-success.html&iframe=1\", \"profile_id\": \"tW6PHjgPyzISFi3KK22hKZ57rag1cWHS\", \"gateway_url\": \"https://anajakpay.com/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS\", \"success_url\": \"https://backend-1-lawx.onrender.com/payment-success.html\", \"checkout_url\": \"https://anajakpay.com/api/payment/requestv2/tW6PHjgPyzISFi3KK22hKZ57rag1cWHS?transaction_id=ORD_ORD_20261107082953_GW4RDW&amount=0.29&remark=MLBB%7CID%3A702425515%7CS%3A10301&hash=9fd69e9b6158a85939c58a012ec64d60c35160bd&success_url=https%3A%2F%2Fbackend-1-lawx.onrender.com%2Fpayment-success.html\", \"merchant_name\": \"dyzz\", \"transaction_id\": \"ORD_ORD_20261107082953_GW4RDW\"}', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-07 08:29:53', '2026-11-07 08:29:54');


-- --------------------------------------------------------
-- Table structure for table `topup_packages`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `topup_packages`;
CREATE TABLE `topup_packages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `topup_game_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `diamond_amount` int unsigned NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `topup_packages_topup_game_id_foreign` (`topup_game_id`),
  CONSTRAINT `topup_packages_topup_game_id_foreign` FOREIGN KEY (`topup_game_id`) REFERENCES `topup_games` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `topup_packages`
INSERT INTO `topup_packages` (`id`, `topup_game_id`, `name`, `diamond_amount`, `price`, `sku`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, '86 Diamonds', 86, '1.30', '89', 1, 1, '2026-06-18 10:02:08', '2026-06-25 04:59:18'),
(2, 1, '165 Diamonds', 165, '2.50', NULL, 2, 1, '2026-06-18 10:02:09', '2026-06-23 15:38:34'),
(3, 1, 'Weekly Pass', 220, '1.50', '142', 3, 1, '2026-06-18 10:02:10', '2026-07-21 08:31:51'),
(4, 1, 'Weekly Elite Pack', 55, '0.90', NULL, 4, 0, '2026-06-18 10:02:11', '2026-06-25 10:03:38'),
(5, 1, '165 Diamonds', 165, '2.45', '40', 5, 1, '2026-06-18 10:02:12', '2026-06-25 04:59:50'),
(10, 1, '55 Diamonds', 55, '0.45', 'TOPUP_MOBILE_LEGENDS_55_DIAMONDS', 0, 0, '2026-06-19 12:28:26', '2026-06-25 10:05:55'),
(11, 1, '343 Diamonds', 343, '4.86', '44', 0, 1, '2026-06-24 02:16:28', '2026-06-25 11:09:07'),
(12, 1, '172 Diamonds', 172, '2.60', '41', 0, 1, '2026-06-24 02:22:41', '2026-06-25 10:08:36'),
(13, 1, '257 Diamonds', 257, '3.60', '42', 0, 1, '2026-06-24 02:23:09', '2026-06-25 10:08:28'),
(14, 1, '275 Diamonds', 275, '3.99', '43', 0, 1, '2026-06-24 02:23:36', '2026-06-25 10:07:56'),
(15, 1, '343 Diamonds', 343, '4.99', '44', 0, 0, '2026-06-24 02:24:03', '2026-06-25 11:09:14'),
(16, 1, 'Weekly Elite Pack', 55, '0.95', '140', 0, 1, '2026-06-24 09:31:20', '2026-06-25 05:00:34'),
(17, 1, 'Limited-Time Value Pack', 11, '0.29', '139', 0, 1, '2026-06-24 09:35:11', '2026-06-25 10:07:29'),
(18, 12, '5 Diamonds', 5, '0.11', '267', 0, 1, '2026-06-24 11:06:52', '2026-06-25 13:19:28'),
(19, 12, '11 Diamonds', 11, '0.23', '268', 0, 1, '2026-06-24 11:07:15', '2026-06-25 13:22:24'),
(20, 12, '22 Diamonds', 22, '0.42', '268', 0, 1, '2026-06-24 11:08:03', '2026-06-25 13:20:40'),
(21, 12, '33 Diamonds', 33, '0.55', '269', 0, 1, '2026-06-24 11:08:33', '2026-06-25 13:22:52'),
(22, 12, '55 Diamonds', 55, '0.91', '38', 0, 1, '2026-06-24 11:09:06', '2026-06-25 06:56:34'),
(23, 12, '112 Diamonds', 112, '1.70', '273', 0, 1, '2026-06-24 11:09:46', '2026-06-25 13:24:27'),
(24, 12, '165 Diamonds', 165, '2.53', '274', 0, 1, '2026-06-24 11:10:21', '2026-06-25 13:25:07'),
(25, 12, '275 Diamonds', 275, '4.12', NULL, 0, 0, '2026-06-24 11:11:42', '2026-06-25 13:23:55'),
(26, 12, 'Limited-Time Value Pack', 11, '0.29', NULL, 0, 0, '2026-06-24 11:12:22', '2026-06-25 13:23:32'),
(27, 12, 'Weekly', 220, '1.65', NULL, 0, 0, '2026-06-24 11:12:42', '2026-06-25 13:23:10'),
(28, 13, 'Weekly Pass', 220, '2.05', '2165', 0, 0, '2026-06-24 13:39:59', '2026-06-25 13:42:02'),
(29, 13, '5 Diamonds', 5, '0.13', '2134', 0, 1, '2026-06-24 13:40:26', '2026-06-25 13:39:11'),
(30, 13, '22 Diamonds', 22, '0.35', '2136', 0, 1, '2026-06-24 13:40:48', '2026-06-25 13:39:37'),
(31, 1, '55 Diamonds', 55, '0.88', '38', 0, 1, '2026-06-24 13:41:07', '2026-06-25 10:40:44'),
(32, 13, '86 Diamonds', 86, '1.35', '2139', 0, 1, '2026-06-24 13:42:01', '2026-06-25 13:41:37'),
(33, 1, 'Weekly Pass x 2', 440, '3.20', NULL, 0, 0, '2026-06-24 13:49:46', '2026-06-25 10:03:23'),
(34, 1, 'Weekly Pass x 3', 660, '4.80', NULL, 0, 0, '2026-06-24 13:49:50', '2026-06-25 10:03:16');


-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `users`
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Master Admin', 'admin@synapse.ai', NULL, '$2y$12$RJ08ymXktaC4mgRNsZAC/ukrLnIcJXrrrt2rJBo.A6AKyCIkvEWgq', 'admin', NULL, '2026-10-16 07:11:41', '2026-11-03 13:45:22'),
(2, 'Master Admin', 'chetranicky@gmail.com', NULL, '$2y$12$RJ08ymXktaC4mgRNsZAC/ukrLnIcJXrrrt2rJBo.A6AKyCIkvEWgq', 'admin', NULL, '2026-10-17 14:41:44', '2026-11-03 13:45:22'),
(3, 'User 9976', '855964129976@dyzzstore.user', NULL, '$2y$12$ylfTUzbEzIBTpMJabsCN0eob7EF/a/PyRhc7vVrVTlaAZkNst66wK', 'user', NULL, '2026-10-25 02:26:49', '2026-10-25 02:26:49'),
(4, 'tinnstore05', 'tinnstore05@gmail.com', NULL, '$2y$12$NwozMgTK89132yTbJ/CZeuRkV2J.rkj8D3Tv5WzzUyb/WeVzDAQW.', 'user', NULL, '2026-10-25 09:00:24', '2026-10-25 09:00:24'),
(5, 'Master Admin', 'admin@dyzzstore.com', NULL, '$2y$12$RJ08ymXktaC4mgRNsZAC/ukrLnIcJXrrrt2rJBo.A6AKyCIkvEWgq', 'admin', NULL, '2026-11-03 13:45:23', '2026-11-03 13:45:23');


SET FOREIGN_KEY_CHECKS=1;
-- Dump completed on 2026-11-11 04:41:24 UTC
