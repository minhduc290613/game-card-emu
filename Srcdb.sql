-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th5 09, 2026 lúc 03:11 AM
-- Phiên bản máy phục vụ: 10.6.20-MariaDB-cll-lve
-- Phiên bản PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `eaythrmg_src`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(64) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Đang đổ dữ liệu cho bảng `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `full_name`, `created_at`) VALUES
(1, 'admin', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'Administrator', '2026-05-08 10:01:53');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `game_packages`
--

CREATE TABLE `game_packages` (
  `id` int(11) NOT NULL,
  `game_name` varchar(255) DEFAULT NULL,
  `icon` text DEFAULT NULL,
  `package_name` varchar(255) DEFAULT NULL,
  `diamonds` varchar(50) NOT NULL,
  `price` int(11) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `game_packages`
--

INSERT INTO `game_packages` (`id`, `game_name`, `icon`, `package_name`, `diamonds`, `price`, `sort_order`, `created_at`) VALUES
(1, 'Free Fire', 'https://ext.same-assets.com/1321103649/3992697621.png', '20.000 Kim Cương', '20000', 20000, 1, '2026-05-08 10:01:53'),
(2, 'Free Fire', 'https://ext.same-assets.com/1321103649/3992697621.png', '50.000 Kim Cương', '50000', 50000, 2, '2026-05-08 10:01:53'),
(3, 'Free Fire', 'https://ext.same-assets.com/1321103649/3992697621.png', '100.000 Kim Cương', '100000', 100000, 3, '2026-05-08 10:01:53'),
(4, 'Liên Quân Mobile', 'https://napthe.vn/images/games/aov.jpg', '300 Quân Huy', '300', 30000, 1, '2026-05-08 10:01:53'),
(5, 'Liên Quân Mobile', 'https://napthe.vn/images/games/aov.jpg', '600 Quân Huy', '600', 60000, 2, '2026-05-08 10:01:53');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_name', 'card giá rẻ vcl', '2026-05-09 03:04:50'),
(2, 'main_banner_title', 'card giá tốt', '2026-05-09 03:04:36'),
(3, 'main_banner_desc', 'an toàn - giá rẻ', '2026-05-09 03:04:40'),
(4, 'promotion_text', 'N?p t? 100.000? t?ng thêm 10% giá tr?', '2026-05-08 10:01:53'),
(5, 'license_key', '$2y$10$8zK9vL5mPqR7tY2uI9oPqRsT5vW8xZ9aB2cD4eF6gH8iJ0kL2mN3o', '2026-05-08 10:01:53'),
(6, 'banner_image', 'data:image/jpeg;base64,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', '2026-05-09 02:40:43'),
(7, 'qr_image', 'https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=https://napthe.vn', '2026-05-08 10:34:11');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Chỉ mục cho bảng `game_packages`
--
ALTER TABLE `game_packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_game_name` (`game_name`(250));

--
-- Chỉ mục cho bảng `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `game_packages`
--
ALTER TABLE `game_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
