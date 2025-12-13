-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 11, 2025 at 10:42 PM
-- Server version: 10.11.15-MariaDB-log
-- PHP Version: 8.4.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `viakingv_englishlearning`
--

-- --------------------------------------------------------

--
-- Table structure for table `learning_history`
--

CREATE TABLE `learning_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `level_id` int(10) UNSIGNED DEFAULT NULL,
  `mode` enum('flashcard','practice','quiz') NOT NULL DEFAULT 'flashcard',
  `vocab_count` int(10) UNSIGNED DEFAULT 0,
  `correct_count` int(10) UNSIGNED DEFAULT 0,
  `total_questions` int(10) UNSIGNED DEFAULT 0,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `learning_history`
--

INSERT INTO `learning_history` (`id`, `user_id`, `level_id`, `mode`, `vocab_count`, `correct_count`, `total_questions`, `note`, `created_at`) VALUES
(1, 5, 1, 'practice', 10, 7, 10, 'Test dữ liệu thủ công', '2025-12-07 22:43:51'),
(2, 5, 1, 'practice', 1, 1, 1, NULL, '2025-12-07 23:24:46'),
(3, 5, 1, 'practice', 1, 1, 1, NULL, '2025-12-07 23:25:40'),
(4, 5, 1, 'practice', 1, 0, 1, NULL, '2025-12-07 23:25:43'),
(5, 5, 1, 'practice', 1, 1, 1, NULL, '2025-12-07 23:30:44'),
(6, 5, 1, 'practice', 1, 0, 1, NULL, '2025-12-07 23:30:56'),
(7, 4, 1, 'practice', 1, 1, 1, NULL, '2025-12-08 00:05:34'),
(8, 4, 1, 'practice', 1, 1, 1, NULL, '2025-12-08 02:01:42'),
(9, 4, 2, 'practice', 1, 1, 1, NULL, '2025-12-09 09:41:00'),
(10, 4, 2, 'practice', 1, 1, 1, NULL, '2025-12-09 09:41:10'),
(11, 4, 1, 'practice', 1, 1, 1, NULL, '2025-12-09 12:41:08'),
(12, 4, 1, 'practice', 1, 1, 1, NULL, '2025-12-09 23:49:29'),
(13, 4, 1, 'practice', 1, 0, 1, NULL, '2025-12-09 23:49:33'),
(14, 7, 2, 'practice', 1, 0, 1, NULL, '2025-12-10 09:26:09'),
(15, 7, 2, 'practice', 1, 0, 1, NULL, '2025-12-10 09:26:23'),
(16, 7, 2, 'practice', 1, 0, 1, NULL, '2025-12-10 09:26:37'),
(17, 7, 2, 'practice', 1, 1, 1, NULL, '2025-12-10 09:26:44'),
(18, 7, 2, 'practice', 1, 1, 1, NULL, '2025-12-10 09:26:51'),
(19, 7, 2, 'practice', 1, 0, 1, NULL, '2025-12-10 09:27:07'),
(20, 7, 2, 'practice', 1, 0, 1, NULL, '2025-12-10 09:27:51'),
(21, 7, 2, 'practice', 1, 0, 1, NULL, '2025-12-10 09:28:02'),
(22, 8, 1, 'practice', 1, 0, 1, NULL, '2025-12-10 11:44:02'),
(23, 8, 1, 'practice', 1, 1, 1, NULL, '2025-12-10 11:44:10'),
(24, 8, 1, 'practice', 1, 0, 1, NULL, '2025-12-10 11:44:15'),
(25, 4, 1, 'practice', 1, 0, 1, NULL, '2025-12-11 00:20:13'),
(26, 4, 1, 'practice', 1, 1, 1, NULL, '2025-12-11 00:20:31'),
(27, 1, 1, 'practice', 1, 1, 1, NULL, '2025-12-11 12:41:30'),
(28, 1, 1, 'practice', 1, 1, 1, NULL, '2025-12-11 12:41:42'),
(29, 4, 1, 'practice', 1, 1, 1, NULL, '2025-12-11 12:48:17'),
(30, 4, 1, 'practice', 1, 1, 1, NULL, '2025-12-11 12:48:41'),
(31, 4, 2, 'practice', 1, 0, 1, NULL, '2025-12-11 12:49:43'),
(32, 4, 2, 'practice', 1, 1, 1, NULL, '2025-12-11 12:50:10'),
(33, 4, 2, 'practice', 1, 1, 1, NULL, '2025-12-11 12:50:24'),
(34, 4, 2, 'practice', 1, 0, 1, NULL, '2025-12-11 12:51:01'),
(35, 9, 1, 'practice', 1, 1, 1, NULL, '2025-12-11 12:56:11'),
(36, 9, 1, 'practice', 1, 0, 1, NULL, '2025-12-11 12:56:25'),
(37, 9, 1, 'practice', 1, 1, 1, NULL, '2025-12-11 12:56:36');

-- --------------------------------------------------------

--
-- Table structure for table `learning_history_items`
--

CREATE TABLE `learning_history_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `history_id` int(10) UNSIGNED NOT NULL,
  `question_text` varchar(255) NOT NULL,
  `user_answer` varchar(255) DEFAULT NULL,
  `correct_answer` varchar(255) DEFAULT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `learning_history_items`
--

INSERT INTO `learning_history_items` (`id`, `history_id`, `question_text`, `user_answer`, `correct_answer`, `is_correct`, `created_at`) VALUES
(1, 2, 'Nghĩa: Cơm / Gạo', 'Rice', 'Rice', 1, '2025-12-07 23:24:46'),
(2, 3, 'Điền từ vào câu: I want an orange juice.', 'orange', 'Orange', 1, '2025-12-07 23:25:40'),
(3, 4, 'Hình ảnh + nghĩa: Nước', 'sada', 'Water', 0, '2025-12-07 23:25:44'),
(4, 5, 'Nghĩa: Thịt', 'Meat', 'Meat', 1, '2025-12-07 23:30:44'),
(5, 6, 'Điền từ vào câu: He wears a hat.', 'áda', 'Hat', 0, '2025-12-07 23:30:56'),
(6, 7, 'Nghĩa: Màu hồng', 'Pink', 'Pink', 1, '2025-12-08 00:05:34'),
(7, 8, 'Nghĩa: Cửa sổ', 'Window', 'Window', 1, '2025-12-08 02:01:42'),
(8, 9, 'Nghĩa: Buồn', 'Sad', 'Sad', 1, '2025-12-09 09:41:00'),
(9, 10, 'Nghĩa: Cũ / Già', 'Old', 'Old', 1, '2025-12-09 09:41:10'),
(10, 11, 'Nghĩa: Mặt trời', 'Sun', 'Sun', 1, '2025-12-09 12:41:08'),
(11, 12, 'Điền từ vào câu: Lemons are sour.', 'Lemon', 'Lemon', 1, '2025-12-09 23:49:29'),
(12, 13, 'Hình ảnh + nghĩa: Màu trắng', 'milk', 'White', 0, '2025-12-09 23:49:33'),
(13, 14, 'Hình ảnh + nghĩa: Vui vẻ', 'clown', 'Happy', 0, '2025-12-10 09:26:09'),
(14, 15, 'Hình ảnh + nghĩa: Ngôi nhà', 'mansion', 'House', 0, '2025-12-10 09:26:23'),
(15, 16, 'Hình ảnh + nghĩa: Anh/Em trai', 'sign', 'Brother', 0, '2025-12-10 09:26:37'),
(16, 17, 'Nghĩa: Khát nước', 'Thirsty', 'Thirsty', 1, '2025-12-10 09:26:44'),
(17, 18, 'Điền từ vào câu: Ice is ___.', 'cold', 'Cold', 1, '2025-12-10 09:26:51'),
(18, 19, 'Hình ảnh + nghĩa: Cao', 'blosom', 'Tall', 0, '2025-12-10 09:27:07'),
(19, 20, 'Hình ảnh + nghĩa: Miệng', 'candy', 'Mouth', 0, '2025-12-10 09:27:51'),
(20, 21, 'Hình ảnh + nghĩa: Tóc', 'bread', 'Hair', 0, '2025-12-10 09:28:02'),
(21, 22, 'Hình ảnh + nghĩa: Con cá', 'z', 'Fish', 0, '2025-12-10 11:44:02'),
(22, 23, 'Nghĩa: Con cá', 'Fish', 'Fish', 1, '2025-12-10 11:44:10'),
(23, 24, 'Hình ảnh + nghĩa: Cái bút', 'z', 'Pen', 0, '2025-12-10 11:44:15'),
(24, 25, 'Hình ảnh + nghĩa: Bánh ngọt', 'cakes', 'Cake', 0, '2025-12-11 00:20:13'),
(25, 26, 'Nghĩa: Màu vàng', 'Yellow', 'Yellow', 1, '2025-12-11 00:20:31'),
(26, 27, 'Nghĩa: Màu vàng', 'Yellow', 'Yellow', 1, '2025-12-11 12:41:30'),
(27, 28, 'Điền từ vào câu: Would you like some ___?', 'tea', 'Tea', 1, '2025-12-11 12:41:42'),
(28, 29, 'Nghĩa: Thịt', 'Meat', 'Meat', 1, '2025-12-11 12:48:17'),
(29, 30, 'Điền từ vào câu: The bird is flying high.', 'bird', 'Bird', 1, '2025-12-11 12:48:41'),
(30, 31, 'Nghĩa: Lạnh', 'Hot', 'Cold', 0, '2025-12-11 12:49:43'),
(31, 32, 'Nghĩa: Tóc', 'Hair', 'Hair', 1, '2025-12-11 12:50:10'),
(32, 33, 'Nghĩa: Trái', 'Left', 'Left', 1, '2025-12-11 12:50:24'),
(33, 34, 'Hình ảnh + nghĩa: Xấu xí', 'basket ball', 'Ugly', 0, '2025-12-11 12:51:01'),
(34, 35, 'Nghĩa: Màu hồng', 'Pink', 'Pink', 1, '2025-12-11 12:56:11'),
(35, 36, 'Điền từ vào câu: I like to eat meat.', 'meet', 'Meat', 0, '2025-12-11 12:56:25'),
(36, 37, 'Nghĩa: Cửa sổ', 'Window', 'Window', 1, '2025-12-11 12:56:36');

-- --------------------------------------------------------

--
-- Table structure for table `levels`
--

CREATE TABLE `levels` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `levels`
--

INSERT INTO `levels` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Pre-Starter', 'Dành cho người mới bắt đầu hoàn toàn, làm quen với bảng chữ cái và phát âm cơ bản.', '2025-12-07 14:44:03'),
(2, 'Starters', 'Tiếng Anh thiếu nhi cấp độ 1: Từ vựng về màu sắc, con vật, gia đình và chào hỏi đơn giản.', '2025-12-07 14:44:03'),
(3, 'Movers', 'Tiếng Anh thiếu nhi cấp độ 2: Xây dựng câu đơn giản, nghe hiểu hội thoại ngắn hàng ngày.', '2025-12-07 14:44:03'),
(4, 'Flyers', 'Tiếng Anh thiếu nhi cấp độ 3: Đọc hiểu văn bản ngắn, viết đoạn văn miêu tả cơ bản.', '2025-12-07 14:44:03'),
(5, 'KET (A2)', 'Trình độ sơ cấp: Có thể giao tiếp trong các tình huống quen thuộc và đơn giản.', '2025-12-07 14:44:03'),
(6, 'PET (B1)', 'Trình độ trung cấp: Sử dụng tiếng Anh độc lập trong công việc, học tập và du lịch.', '2025-12-07 14:44:03'),
(7, 'IELTS 0 - 3.0', 'Xây dựng nền tảng: Lấy lại gốc ngữ pháp và từ vựng cho người mất căn bản.', '2025-12-07 14:44:03'),
(8, 'IELTS 3.0 - 5.0', 'Pre-IELTS: Làm quen với các dạng bài thi và củng cố 4 kỹ năng Nghe-Nói-Đọc-Viết.', '2025-12-07 14:44:03'),
(9, 'IELTS 5.0 - 6.5', 'IELTS Master: Luyện đề chuyên sâu và nâng cao kỹ năng tư duy phản biện.', '2025-12-07 14:44:03'),
(10, 'Giao tiếp căn bản', 'Luyện phản xạ nghe nói cho người đi làm, tập trung vào các chủ đề văn phòng.', '2025-12-07 14:44:03'),
(11, 'pre-pre-starters', '', '2025-12-11 02:55:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `full_name` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `status` enum('active','blocked') NOT NULL DEFAULT 'active',
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `full_name`, `email`, `avatar`, `phone`, `password`, `role`, `status`, `deleted_at`, `created_at`) VALUES
(1, 'admin', NULL, NULL, NULL, NULL, '$2y$12$C4md26.OGGCTUoyiZvSyrutSD8A53FZP10c9D2OOh36hzbtTJT32m', 'admin', 'active', NULL, '2025-12-07 04:30:29'),
(4, 'user', NULL, NULL, NULL, NULL, '$2y$12$cgT65GnOwlEZv.aZ.M27k.ek3NgMfEi3V4Iq31IP.o0fPXE1RwlOS', 'user', 'active', NULL, '2025-12-07 05:09:05'),
(5, 'nhattruong', 'Hoàng Nhật Trường', 'zayluon@gmail.com', 'uploads/avatars/avatar_5_1765119293_5758.jpg', '0364132169', '$2y$12$UJdNPleHZEmEMbB/Zujn8.ZZUJUwgQMthAaMQ2y37cKU5BMCRZyfm', 'user', 'active', NULL, '2025-12-07 21:26:53'),
(6, 'abc', NULL, NULL, NULL, NULL, '$2y$10$szxgbG0RrSgVRFsDwGyUAOESzTWGn7Buc1GDE80jQcc0qbHU99InO', 'user', 'active', NULL, '2025-12-08 00:11:17'),
(7, 'fffff', '', '', '', '', '$2y$10$Gt.nq1LBJ5y9Xw.EeCGcoOFRSMhUM.AnwmzRbhlYDCfabM2H0QsFC', 'user', 'active', NULL, '2025-12-10 09:21:38'),
(8, 'phucnetruong', NULL, NULL, NULL, NULL, '$2y$10$Ncqr2n7dK4sZ1NPkFrW5ue2QqoJZLQRbccTe8ZixKbOuEG6ZEm7P.', 'user', 'active', NULL, '2025-12-10 11:42:32'),
(9, 'yamate', NULL, NULL, NULL, NULL, '$2y$10$yN7tTV8q.yG4B23mT37Xw.grVZXW606K11xTSgvAghmL7Kb.xnkUW', 'user', 'blocked', NULL, '2025-12-11 12:54:19');

-- --------------------------------------------------------

--
-- Table structure for table `user_level_progress`
--

CREATE TABLE `user_level_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL DEFAULT 0,
  `answered_questions` int(11) NOT NULL DEFAULT 0,
  `correct_answers` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_level_progress`
--

INSERT INTO `user_level_progress` (`id`, `user_id`, `level_id`, `total_questions`, `answered_questions`, `correct_answers`, `updated_at`) VALUES
(1, 4, 1, 50, 18, 10, '2025-12-11 05:48:41'),
(2, 4, 3, 10, 1, 0, '2025-12-07 09:54:27'),
(3, 4, 10, 40, 1, 0, '2025-12-07 11:16:33'),
(4, 4, 2, 50, 7, 5, '2025-12-11 05:51:01'),
(5, 5, 1, 50, 13, 8, '2025-12-07 16:30:56'),
(6, 5, 2, 50, 2, 1, '2025-12-07 15:50:28'),
(7, 5, 10, 40, 1, 1, '2025-12-07 16:01:09'),
(8, 7, 2, 50, 8, 2, '2025-12-10 02:28:02'),
(9, 8, 1, 50, 3, 1, '2025-12-10 04:44:15'),
(10, 1, 1, 50, 2, 2, '2025-12-11 05:41:42'),
(11, 9, 1, 50, 3, 2, '2025-12-11 05:56:36');

-- --------------------------------------------------------

--
-- Table structure for table `vocabularies`
--

CREATE TABLE `vocabularies` (
  `id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `word` varchar(255) NOT NULL,
  `meaning` varchar(255) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `audio_url` varchar(255) DEFAULT NULL,
  `type` enum('flashcard','fill_gap','mixed') DEFAULT 'flashcard',
  `example_sentence` text DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vocabularies`
--

INSERT INTO `vocabularies` (`id`, `level_id`, `word`, `meaning`, `image_url`, `audio_url`, `type`, `example_sentence`, `deleted_at`) VALUES
(1, 1, 'Dog', 'Con chó', 'https://loremflickr.com/400/300/dog', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/dog--_gb_1.mp3', 'flashcard', 'The dog is barking.', NULL),
(2, 1, 'Cat', 'Con mèo', 'https://loremflickr.com/400/300/cat', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/cat--_gb_1.mp3', 'flashcard', 'The cat loves milk.', NULL),
(3, 1, 'Bird', 'Con chim', 'https://loremflickr.com/400/300/bird', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/bird--_gb_1.mp3', 'mixed', 'The bird is flying high.', NULL),
(4, 1, 'Fish', 'Con cá', 'https://loremflickr.com/400/300/fish', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/fish--_gb_1.mp3', 'flashcard', 'Fish swim in the water.', NULL),
(5, 1, 'Lion', 'Sư tử', 'https://loremflickr.com/400/300/lion', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/lion--_gb_1.mp3', 'fill_gap', 'The ___ is the king of the jungle.', NULL),
(6, 1, 'Tiger', 'Con hổ', 'https://loremflickr.com/400/300/tiger', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/tiger--_gb_1.mp3', 'mixed', 'Tigers have stripes.', NULL),
(7, 1, 'Monkey', 'Con khỉ', 'https://loremflickr.com/400/300/monkey', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/monkey--_gb_1.mp3', 'flashcard', 'The monkey eats a banana.', NULL),
(8, 1, 'Elephant', 'Con voi', 'https://loremflickr.com/400/300/elephant', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/elephant--_gb_1.mp3', 'fill_gap', 'The ___ has a long nose.', NULL),
(9, 1, 'Zebra', 'Ngựa vằn', 'https://loremflickr.com/400/300/zebra', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/zebra--_gb_1.mp3', 'flashcard', 'A zebra is black and white.', NULL),
(10, 1, 'Rabbit', 'Con thỏ', 'https://loremflickr.com/400/300/rabbit', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/rabbit--_gb_1.mp3', 'mixed', 'Rabbits can jump fast.', NULL),
(11, 1, 'Apple', 'Quả táo', 'https://loremflickr.com/400/300/apple', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/apple--_gb_1.mp3', 'flashcard', 'An apple a day keeps the doctor away.', NULL),
(12, 1, 'Banana', 'Quả chuối', 'https://loremflickr.com/400/300/banana', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/banana--_gb_1.mp3', 'flashcard', 'Monkeys love to eat a banana.', NULL),
(13, 1, 'Orange', 'Quả cam', 'https://loremflickr.com/400/300/orange', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/orange--_gb_1.mp3', 'mixed', 'I want an orange juice.', NULL),
(14, 1, 'Grape', 'Quả nho', 'https://loremflickr.com/400/300/grape', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/grape--_gb_1.mp3', 'fill_gap', 'This ___ is very sweet.', NULL),
(15, 1, 'Lemon', 'Quả chanh vàng', 'https://loremflickr.com/400/300/lemon', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/lemon--_gb_1.mp3', 'flashcard', 'Lemons are sour.', NULL),
(16, 1, 'Melon', 'Dưa lưới', 'https://loremflickr.com/400/300/melon', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/melon--_gb_1.mp3', 'mixed', 'Watermelon is a type of melon.', NULL),
(17, 1, 'Strawberry', 'Dâu tây', 'https://loremflickr.com/400/300/strawberry', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/strawberry--_gb_1.mp3', 'flashcard', 'I like strawberry ice cream.', NULL),
(18, 1, 'Bread', 'Bánh mì', 'https://loremflickr.com/400/300/bread', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/bread--_gb_1.mp3', 'fill_gap', 'I eat ___ for breakfast.', NULL),
(19, 1, 'Rice', 'Cơm / Gạo', 'https://loremflickr.com/400/300/rice', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/rice--_gb_1.mp3', 'mixed', 'Asians eat a lot of rice.', NULL),
(20, 1, 'Meat', 'Thịt', 'https://loremflickr.com/400/300/meat', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/meat--_gb_1.mp3', 'flashcard', 'I like to eat meat.', NULL),
(21, 1, 'Chicken', 'Thịt gà / Con gà', 'https://loremflickr.com/400/300/chicken', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/chicken--_gb_1.mp3', 'flashcard', 'Fried chicken is delicious.', NULL),
(22, 1, 'Egg', 'Quả trứng', 'https://loremflickr.com/400/300/egg', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/egg--_gb_1.mp3', 'fill_gap', 'I boil an ___ every morning.', NULL),
(23, 1, 'Milk', 'Sữa', 'https://loremflickr.com/400/300/milk', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/milk--_gb_1.mp3', 'mixed', 'Cats like milk.', NULL),
(24, 1, 'Water', 'Nước', 'https://loremflickr.com/400/300/water', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/water--_gb_1.mp3', 'flashcard', 'Please give me some water.', NULL),
(25, 1, 'Coffee', 'Cà phê', 'https://loremflickr.com/400/300/coffee', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/coffee--_gb_1.mp3', 'flashcard', 'My dad drinks coffee.', NULL),
(26, 1, 'Tea', 'Trà', 'https://loremflickr.com/400/300/tea', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/tea--_gb_1.mp3', 'fill_gap', 'Would you like some ___?', NULL),
(27, 1, 'Juice', 'Nước ép', 'https://loremflickr.com/400/300/juice', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/juice--_gb_1.mp3', 'mixed', 'Orange juice is healthy.', NULL),
(28, 1, 'Cake', 'Bánh ngọt', 'https://loremflickr.com/400/300/cake', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/cake--_gb_1.mp3', 'flashcard', 'Happy birthday cake.', NULL),
(29, 1, 'Red', 'Màu đỏ', 'https://loremflickr.com/400/300/red', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/red--_gb_1.mp3', 'flashcard', 'The apple is red.', NULL),
(30, 1, 'Blue', 'Màu xanh dương', 'https://loremflickr.com/400/300/blue', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/blue--_gb_1.mp3', 'fill_gap', 'The sky is ___.', NULL),
(31, 1, 'Green', 'Màu xanh lá', 'https://loremflickr.com/400/300/green', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/green--_gb_1.mp3', 'mixed', 'The grass is green.', NULL),
(32, 1, 'Yellow', 'Màu vàng', 'https://loremflickr.com/400/300/yellow', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/yellow--_gb_1.mp3', 'flashcard', 'The sun is yellow.', NULL),
(33, 1, 'Black', 'Màu đen', 'https://loremflickr.com/400/300/black', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/black--_gb_1.mp3', 'flashcard', 'My hair is black.', NULL),
(34, 1, 'White', 'Màu trắng', 'https://loremflickr.com/400/300/white', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/white--_gb_1.mp3', 'fill_gap', 'Snow is ___.', NULL),
(35, 1, 'Pink', 'Màu hồng', 'https://loremflickr.com/400/300/pink', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/pink--_gb_1.mp3', 'mixed', 'She likes pink flowers.', NULL),
(36, 1, 'Table', 'Cái bàn', 'https://loremflickr.com/400/300/table', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/table--_gb_1.mp3', 'flashcard', 'The book is on the table.', NULL),
(37, 1, 'Chair', 'Cái ghế', 'https://loremflickr.com/400/300/chair', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/chair--_gb_1.mp3', 'fill_gap', 'Sit on the ___.', NULL),
(38, 1, 'Bed', 'Cái giường', 'https://loremflickr.com/400/300/bed', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/bed--_gb_1.mp3', 'flashcard', 'I sleep in my bed.', NULL),
(39, 1, 'Door', 'Cửa ra vào', 'https://loremflickr.com/400/300/door', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/door--_gb_1.mp3', 'mixed', 'Open the door, please.', NULL),
(40, 1, 'Window', 'Cửa sổ', 'https://loremflickr.com/400/300/window', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/window--_gb_1.mp3', 'flashcard', 'Look out the window.', NULL),
(41, 1, 'Pen', 'Cái bút', 'https://loremflickr.com/400/300/pen', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/pen--_gb_1.mp3', 'fill_gap', 'I write with a ___.', NULL),
(42, 1, 'Bag', 'Cặp sách', 'https://loremflickr.com/400/300/bag', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/bag--_gb_1.mp3', 'mixed', 'Put your books in the bag.', NULL),
(43, 1, 'Hat', 'Cái mũ', 'https://loremflickr.com/400/300/hat', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/hat--_gb_1.mp3', 'flashcard', 'He wears a hat.', NULL),
(44, 1, 'Shoe', 'Giày', 'https://loremflickr.com/400/300/shoe', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/shoe--_gb_1.mp3', 'fill_gap', 'Tie your ___ laces.', NULL),
(45, 1, 'Shirt', 'Áo sơ mi', 'https://loremflickr.com/400/300/shirt', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/shirt--_gb_1.mp3', 'flashcard', 'A white shirt.', NULL),
(46, 1, 'Dress', 'Váy', 'https://loremflickr.com/400/300/dress', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/dress--_gb_1.mp3', 'mixed', 'A beautiful dress.', NULL),
(47, 1, 'Sun', 'Mặt trời', 'https://loremflickr.com/400/300/sun', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/sun--_gb_1.mp3', 'flashcard', 'The sun is hot.', NULL),
(48, 1, 'Moon', 'Mặt trăng', 'https://loremflickr.com/400/300/moon', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/moon--_gb_1.mp3', 'fill_gap', 'The ___ shines at night.', NULL),
(49, 1, 'Star', 'Ngôi sao', 'https://loremflickr.com/400/300/star', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/star--_gb_1.mp3', 'flashcard', 'Twinkle twinkle little star.', NULL),
(50, 1, 'Rain', 'Mưa', 'https://loremflickr.com/400/300/rain', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/rain--_gb_1.mp3', 'mixed', 'I do not like rain.', NULL),
(51, 2, 'Head', 'Đầu', 'https://loremflickr.com/400/300/face', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/head--_gb_1.mp3', 'flashcard', 'Nod your head.', NULL),
(52, 2, 'Eye', 'Mắt', 'https://loremflickr.com/400/300/eye', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/eye--_gb_1.mp3', 'flashcard', 'Close your eyes.', NULL),
(53, 2, 'Nose', 'Mũi', 'https://loremflickr.com/400/300/nose', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/nose--_gb_1.mp3', 'fill_gap', 'Touch your ___.', NULL),
(54, 2, 'Mouth', 'Miệng', 'https://loremflickr.com/400/300/mouth', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/mouth--_gb_1.mp3', 'mixed', 'Open your mouth.', NULL),
(55, 2, 'Ear', 'Tai', 'https://loremflickr.com/400/300/ear', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/ear--_gb_1.mp3', 'flashcard', 'Listen with your ears.', NULL),
(56, 2, 'Hand', 'Bàn tay', 'https://loremflickr.com/400/300/hand', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/hand--_gb_1.mp3', 'fill_gap', 'Clap your ___.', NULL),
(57, 2, 'Arm', 'Cánh tay', 'https://loremflickr.com/400/300/arm', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/arm--_gb_1.mp3', 'flashcard', 'Raise your arm.', NULL),
(58, 2, 'Leg', 'Chân', 'https://loremflickr.com/400/300/leg', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/leg--_gb_1.mp3', 'mixed', 'Run with your legs.', NULL),
(59, 2, 'Foot', 'Bàn chân', 'https://loremflickr.com/400/300/foot', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/foot--_gb_1.mp3', 'flashcard', 'Stomp your foot.', NULL),
(60, 2, 'Hair', 'Tóc', 'https://loremflickr.com/400/300/hair', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/hair--_gb_1.mp3', 'fill_gap', 'She has long ___.', NULL),
(61, 2, 'Face', 'Khuôn mặt', 'https://loremflickr.com/400/300/face', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/face--_gb_1.mp3', 'mixed', 'Wash your face.', NULL),
(62, 2, 'Mother', 'Mẹ', 'https://loremflickr.com/400/300/mother', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/mother--_gb_1.mp3', 'flashcard', 'I love my mother.', NULL),
(63, 2, 'Father', 'Bố', 'https://loremflickr.com/400/300/father', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/father--_gb_1.mp3', 'flashcard', 'My father is tall.', NULL),
(64, 2, 'Sister', 'Chị/Em gái', 'https://loremflickr.com/400/300/sister', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/sister--_gb_1.mp3', 'fill_gap', 'My ___ plays with dolls.', NULL),
(65, 2, 'Brother', 'Anh/Em trai', 'https://loremflickr.com/400/300/brother', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/brother--_gb_1.mp3', 'mixed', 'My brother likes cars.', NULL),
(66, 2, 'Baby', 'Em bé', 'https://loremflickr.com/400/300/baby', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/baby--_gb_1.mp3', 'flashcard', 'The baby is crying.', NULL),
(67, 2, 'Grandmother', 'Bà', 'https://loremflickr.com/400/300/grandmother', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/grandmother--_gb_1.mp3', 'fill_gap', 'My ___ is old.', NULL),
(68, 2, 'Grandfather', 'Ông', 'https://loremflickr.com/400/300/grandfather', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/grandfather--_gb_1.mp3', 'mixed', 'My grandfather tells stories.', NULL),
(69, 2, 'Family', 'Gia đình', 'https://loremflickr.com/400/300/family', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/family--_gb_1.mp3', 'flashcard', 'My family is happy.', NULL),
(70, 2, 'House', 'Ngôi nhà', 'https://loremflickr.com/400/300/house', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/house--_gb_1.mp3', 'flashcard', 'This is my house.', NULL),
(71, 2, 'Garden', 'Khu vườn', 'https://loremflickr.com/400/300/garden', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/garden--_gb_1.mp3', 'fill_gap', 'Flowers grow in the ___.', NULL),
(72, 2, 'Kitchen', 'Nhà bếp', 'https://loremflickr.com/400/300/kitchen', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/kitchen--_gb_1.mp3', 'mixed', 'Mom cooks in the kitchen.', NULL),
(73, 2, 'Bedroom', 'Phòng ngủ', 'https://loremflickr.com/400/300/bedroom', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/bedroom--_gb_1.mp3', 'flashcard', 'I sleep in the bedroom.', NULL),
(74, 2, 'Bathroom', 'Phòng tắm', 'https://loremflickr.com/400/300/bathroom', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/bathroom--_gb_1.mp3', 'flashcard', 'I wash in the bathroom.', NULL),
(75, 2, 'Happy', 'Vui vẻ', 'https://loremflickr.com/400/300/happy', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/happy--_gb_1.mp3', 'flashcard', 'I am happy.', NULL),
(76, 2, 'Sad', 'Buồn', 'https://loremflickr.com/400/300/sad', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/sad--_gb_1.mp3', 'fill_gap', 'Why are you ___?', NULL),
(77, 2, 'Angry', 'Tức giận', 'https://loremflickr.com/400/300/angry', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/angry--_gb_1.mp3', 'mixed', 'Do not be angry.', NULL),
(78, 2, 'Tired', 'Mệt mỏi', 'https://loremflickr.com/400/300/tired', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/tired--_gb_1.mp3', 'flashcard', 'I am tired after running.', NULL),
(79, 2, 'Hungry', 'Đói', 'https://loremflickr.com/400/300/hungry', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/hungry--_gb_1.mp3', 'flashcard', 'I am hungry, let us eat.', NULL),
(80, 2, 'Thirsty', 'Khát nước', 'https://loremflickr.com/400/300/water', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/thirsty--_gb_1.mp3', 'fill_gap', 'I am ___, give me water.', NULL),
(81, 2, 'Big', 'To lớn', 'https://loremflickr.com/400/300/elephant', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/big--_gb_1.mp3', 'mixed', 'An elephant is big.', NULL),
(82, 2, 'Small', 'Nhỏ bé', 'https://loremflickr.com/400/300/mouse', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/small--_gb_1.mp3', 'flashcard', 'A mouse is small.', NULL),
(83, 2, 'Hot', 'Nóng', 'https://loremflickr.com/400/300/fire', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/hot--_gb_1.mp3', 'flashcard', 'The tea is hot.', NULL),
(84, 2, 'Cold', 'Lạnh', 'https://loremflickr.com/400/300/ice', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/cold--_gb_1.mp3', 'fill_gap', 'Ice is ___.', NULL),
(85, 2, 'Fast', 'Nhanh', 'https://loremflickr.com/400/300/car', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/fast--_gb_1.mp3', 'mixed', 'A car is fast.', NULL),
(86, 2, 'Slow', 'Chậm', 'https://loremflickr.com/400/300/turtle', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/slow--_gb_1.mp3', 'flashcard', 'A turtle is slow.', NULL),
(87, 2, 'Tall', 'Cao', 'https://loremflickr.com/400/300/tree', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/tall--_gb_1.mp3', 'flashcard', 'The tree is tall.', NULL),
(88, 2, 'Short', 'Thấp / Ngắn', 'https://loremflickr.com/400/300/grass', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/short--_gb_1.mp3', 'fill_gap', 'The grass is ___.', NULL),
(89, 2, 'Good', 'Tốt', 'https://loremflickr.com/400/300/good', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/good--_gb_1.mp3', 'mixed', 'Very good job.', NULL),
(90, 2, 'Bad', 'Xấu / Tệ', 'https://loremflickr.com/400/300/bad', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/bad--_gb_1.mp3', 'flashcard', 'That is a bad idea.', NULL),
(91, 2, 'New', 'Mới', 'https://loremflickr.com/400/300/phone', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/new--_gb_1.mp3', 'flashcard', 'I have a new phone.', NULL),
(92, 2, 'Old', 'Cũ / Già', 'https://loremflickr.com/400/300/man', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/old--_gb_1.mp3', 'fill_gap', 'My grandpa is ___.', NULL),
(93, 2, 'Clean', 'Sạch sẽ', 'https://loremflickr.com/400/300/room', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/clean--_gb_1.mp3', 'mixed', 'My room is clean.', NULL),
(94, 2, 'Dirty', 'Bẩn', 'https://loremflickr.com/400/300/mud', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/dirty--_gb_1.mp3', 'flashcard', 'Your hands are dirty.', NULL),
(95, 2, 'Beautiful', 'Xinh đẹp', 'https://loremflickr.com/400/300/flower', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/beautiful--_gb_1.mp3', 'flashcard', 'A beautiful flower.', NULL),
(96, 2, 'Ugly', 'Xấu xí', 'https://loremflickr.com/400/300/monster', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/ugly--_gb_1.mp3', 'fill_gap', 'An ___ monster.', NULL),
(97, 2, 'Right', 'Đúng / Phải', 'https://loremflickr.com/400/300/arrow', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/right--_gb_1.mp3', 'mixed', 'Turn right.', NULL),
(98, 2, 'Left', 'Trái', 'https://loremflickr.com/400/300/arrow', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/left--_gb_1.mp3', 'flashcard', 'Turn left.', NULL),
(99, 2, 'Hard', 'Cứng / Khó', 'https://loremflickr.com/400/300/rock', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/hard--_gb_1.mp3', 'flashcard', 'The rock is hard.', NULL),
(100, 2, 'Soft', 'Mềm', 'https://loremflickr.com/400/300/pillow', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/soft--_gb_1.mp3', 'fill_gap', 'The pillow is ___.', NULL),
(101, 3, 'Run', 'Chạy', 'https://loremflickr.com/400/300/run', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/run--_gb_1.mp3', 'flashcard', 'Run fast.', NULL),
(102, 3, 'Walk', 'Đi bộ', 'https://loremflickr.com/400/300/walk', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/walk--_gb_1.mp3', 'flashcard', 'I walk to school.', NULL),
(103, 3, 'Jump', 'Nhảy', 'https://loremflickr.com/400/300/jump', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/jump--_gb_1.mp3', 'mixed', 'Jump high.', NULL),
(104, 3, 'Swim', 'Bơi', 'https://loremflickr.com/400/300/swim', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/swim--_gb_1.mp3', 'fill_gap', 'I can ___ in the pool.', NULL),
(105, 3, 'Sleep', 'Ngủ', 'https://loremflickr.com/400/300/sleep', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/sleep--_gb_1.mp3', 'flashcard', 'Go to sleep.', NULL),
(106, 3, 'Eat', 'Ăn', 'https://loremflickr.com/400/300/eat', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/eat--_gb_1.mp3', 'mixed', 'Eat your food.', NULL),
(107, 3, 'Drink', 'Uống', 'https://loremflickr.com/400/300/drink', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/drink--_gb_1.mp3', 'flashcard', 'Drink some water.', NULL),
(108, 3, 'Read', 'Đọc', 'https://loremflickr.com/400/300/read', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/read--_gb_1.mp3', 'fill_gap', '___ a book.', NULL),
(109, 3, 'Write', 'Viết', 'https://loremflickr.com/400/300/write', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/write--_gb_1.mp3', 'flashcard', 'Write your name.', NULL),
(110, 3, 'Listen', 'Nghe', 'https://loremflickr.com/400/300/listen', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/listen--_gb_1.mp3', 'mixed', 'Listen to music.', NULL),
(111, 3, 'Speak', 'Nói', 'https://loremflickr.com/400/300/speak', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/speak--_gb_1.mp3', 'flashcard', 'Speak English.', NULL),
(112, 3, 'Sing', 'Hát', 'https://loremflickr.com/400/300/sing', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/sing--_gb_1.mp3', 'fill_gap', '___ a song.', NULL),
(113, 3, 'Dance', 'Nhảy múa', 'https://loremflickr.com/400/300/dance', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/dance--_gb_1.mp3', 'flashcard', 'They like to dance.', NULL),
(114, 3, 'Play', 'Chơi', 'https://loremflickr.com/400/300/play', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/play--_gb_1.mp3', 'mixed', 'Play football.', NULL),
(115, 3, 'Cook', 'Nấu ăn', 'https://loremflickr.com/400/300/cook', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/cook--_gb_1.mp3', 'flashcard', 'Mom cooks dinner.', NULL),
(116, 3, 'Drive', 'Lái xe', 'https://loremflickr.com/400/300/drive', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/drive--_gb_1.mp3', 'fill_gap', '___ a car.', NULL),
(117, 3, 'Fly', 'Bay', 'https://loremflickr.com/400/300/fly', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/fly--_gb_1.mp3', 'flashcard', 'Birds fly in the sky.', NULL),
(118, 3, 'City', 'Thành phố', 'https://loremflickr.com/400/300/city', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/city--_gb_1.mp3', 'mixed', 'I live in a city.', NULL),
(119, 3, 'Park', 'Công viên', 'https://loremflickr.com/400/300/park', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/park--_gb_1.mp3', 'flashcard', 'Let us go to the park.', NULL),
(120, 3, 'School', 'Trường học', 'https://loremflickr.com/400/300/school', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/school--_gb_1.mp3', 'fill_gap', 'I learn at ___.', NULL),
(121, 3, 'Hospital', 'Bệnh viện', 'https://loremflickr.com/400/300/hospital', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/hospital--_gb_1.mp3', 'flashcard', 'Doctors work in a hospital.', NULL),
(122, 3, 'Market', 'Chợ', 'https://loremflickr.com/400/300/market', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/market--_gb_1.mp3', 'mixed', 'Buy food at the market.', NULL),
(123, 3, 'Shop', 'Cửa hàng', 'https://loremflickr.com/400/300/shop', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/shop--_gb_1.mp3', 'flashcard', 'A toy shop.', NULL),
(124, 3, 'Bank', 'Ngân hàng', 'https://loremflickr.com/400/300/bank', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/bank--_gb_1.mp3', 'fill_gap', 'Get money from the ___.', NULL),
(125, 3, 'Cinema', 'Rạp chiếu phim', 'https://loremflickr.com/400/300/cinema', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/cinema--_gb_1.mp3', 'flashcard', 'Watch a movie at the cinema.', NULL),
(126, 3, 'Hotel', 'Khách sạn', 'https://loremflickr.com/400/300/hotel', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/hotel--_gb_1.mp3', 'mixed', 'Stay in a hotel.', NULL),
(127, 3, 'Airport', 'Sân bay', 'https://loremflickr.com/400/300/airport', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/airport--_gb_1.mp3', 'flashcard', 'Planes at the airport.', NULL),
(128, 3, 'Station', 'Nhà ga', 'https://loremflickr.com/400/300/train', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/station--_gb_1.mp3', 'fill_gap', 'Train ___ .', NULL),
(129, 3, 'Library', 'Thư viện', 'https://loremflickr.com/400/300/library', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/library--_gb_1.mp3', 'flashcard', 'Quiet in the library.', NULL),
(130, 3, 'Mountain', 'Núi', 'https://loremflickr.com/400/300/mountain', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/mountain--_gb_1.mp3', 'mixed', 'Climb a mountain.', NULL),
(131, 3, 'River', 'Dòng sông', 'https://loremflickr.com/400/300/river', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/river--_gb_1.mp3', 'flashcard', 'A long river.', NULL),
(132, 3, 'Lake', 'Hồ nước', 'https://loremflickr.com/400/300/lake', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/lake--_gb_1.mp3', 'fill_gap', 'Fish in the ___.', NULL),
(133, 3, 'Sea', 'Biển', 'https://loremflickr.com/400/300/sea', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/sea--_gb_1.mp3', 'flashcard', 'The sea is blue.', NULL),
(134, 3, 'Beach', 'Bãi biển', 'https://loremflickr.com/400/300/beach', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/beach--_gb_1.mp3', 'mixed', 'Play on the beach.', NULL),
(135, 3, 'Forest', 'Rừng', 'https://loremflickr.com/400/300/forest', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/forest--_gb_1.mp3', 'flashcard', 'Trees in the forest.', NULL),
(136, 3, 'Flower', 'Bông hoa', 'https://loremflickr.com/400/300/flower', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/flower--_gb_1.mp3', 'fill_gap', 'A pretty ___.', NULL),
(137, 3, 'Tree', 'Cây', 'https://loremflickr.com/400/300/tree', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/tree--_gb_1.mp3', 'flashcard', 'A green tree.', NULL),
(138, 3, 'Grass', 'Cỏ', 'https://loremflickr.com/400/300/grass', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/grass--_gb_1.mp3', 'mixed', 'Sit on the grass.', NULL),
(139, 3, 'Sky', 'Bầu trời', 'https://loremflickr.com/400/300/sky', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/sky--_gb_1.mp3', 'flashcard', 'Look at the sky.', NULL),
(140, 3, 'Road', 'Con đường', 'https://loremflickr.com/400/300/road', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/road--_gb_1.mp3', 'fill_gap', 'Cross the ___.', NULL),
(141, 3, 'Car', 'Xe hơi', 'https://loremflickr.com/400/300/car', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/car--_gb_1.mp3', 'flashcard', 'Drive a car.', NULL),
(142, 3, 'Bus', 'Xe buýt', 'https://loremflickr.com/400/300/bus', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/bus--_gb_1.mp3', 'mixed', 'Take the bus.', NULL),
(143, 3, 'Bike', 'Xe đạp', 'https://loremflickr.com/400/300/bicycle', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/bike--_gb_1.mp3', 'flashcard', 'Ride a bike.', NULL),
(144, 3, 'Train', 'Tàu hỏa', 'https://loremflickr.com/400/300/train', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/train--_gb_1.mp3', 'fill_gap', 'A fast ___.', NULL),
(145, 3, 'Plane', 'Máy bay', 'https://loremflickr.com/400/300/airplane', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/plane--_gb_1.mp3', 'flashcard', 'Fly in a plane.', NULL),
(146, 3, 'Boat', 'Thuyền', 'https://loremflickr.com/400/300/boat', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/boat--_gb_1.mp3', 'mixed', 'Sail a boat.', NULL),
(147, 3, 'Morning', 'Buổi sáng', 'https://loremflickr.com/400/300/sunrise', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/morning--_gb_1.mp3', 'flashcard', 'Good morning.', NULL),
(148, 3, 'Night', 'Buổi đêm', 'https://loremflickr.com/400/300/night', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/night--_gb_1.mp3', 'fill_gap', 'Sleep at ___.', NULL),
(149, 3, 'Day', 'Ban ngày', 'https://loremflickr.com/400/300/day', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/day--_gb_1.mp3', 'flashcard', 'A sunny day.', NULL),
(150, 3, 'Time', 'Thời gian', 'https://loremflickr.com/400/300/clock', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/time--_gb_1.mp3', 'mixed', 'What time is it?', NULL),
(151, 10, 'Business', 'Kinh doanh', 'https://loremflickr.com/400/300/business', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/business--_gb_1.mp3', 'flashcard', 'Business is good.', NULL),
(152, 10, 'Office', 'Văn phòng', 'https://loremflickr.com/400/300/office', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/office--_gb_1.mp3', 'flashcard', 'I work in an office.', NULL),
(153, 10, 'Company', 'Công ty', 'https://loremflickr.com/400/300/company', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/company--_gb_1.mp3', 'fill_gap', 'A big software ___.', NULL),
(154, 10, 'Meeting', 'Cuộc họp', 'https://loremflickr.com/400/300/meeting', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/meeting--_gb_1.mp3', 'mixed', 'Attend a meeting.', NULL),
(155, 10, 'Manager', 'Quản lý', 'https://loremflickr.com/400/300/manager', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/manager--_gb_1.mp3', 'flashcard', 'The manager is busy.', NULL),
(156, 10, 'Staff', 'Nhân viên', 'https://loremflickr.com/400/300/staff', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/staff--_gb_1.mp3', 'flashcard', 'Helpful staff.', NULL),
(157, 10, 'Boss', 'Ông chủ', 'https://loremflickr.com/400/300/boss', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/boss--_gb_1.mp3', 'fill_gap', 'Ask the ___.', NULL),
(158, 10, 'Client', 'Khách hàng', 'https://loremflickr.com/400/300/client', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/client--_gb_1.mp3', 'mixed', 'Meet the client.', NULL),
(159, 10, 'Money', 'Tiền', 'https://loremflickr.com/400/300/money', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/money--_gb_1.mp3', 'flashcard', 'Save money.', NULL),
(160, 10, 'Bank', 'Ngân hàng', 'https://loremflickr.com/400/300/bank', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/bank--_gb_1.mp3', 'flashcard', 'Deposit in the bank.', NULL),
(161, 10, 'Price', 'Giá cả', 'https://loremflickr.com/400/300/price', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/price--_gb_1.mp3', 'fill_gap', 'The ___ is high.', NULL),
(162, 10, 'Cost', 'Chi phí', 'https://loremflickr.com/400/300/invoice', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/cost--_gb_1.mp3', 'mixed', 'Low cost.', NULL),
(163, 10, 'Pay', 'Thanh toán', 'https://loremflickr.com/400/300/pay', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/pay--_gb_1.mp3', 'flashcard', 'Pay the bill.', NULL),
(164, 10, 'Buy', 'Mua', 'https://loremflickr.com/400/300/buy', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/buy--_gb_1.mp3', 'flashcard', 'Buy shares.', NULL),
(165, 10, 'Sell', 'Bán', 'https://loremflickr.com/400/300/sell', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/sell--_gb_1.mp3', 'fill_gap', '___ products.', NULL),
(166, 10, 'Market', 'Thị trường', 'https://loremflickr.com/400/300/market', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/market--_gb_1.mp3', 'mixed', 'Stock market.', NULL),
(167, 10, 'Job', 'Công việc', 'https://loremflickr.com/400/300/job', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/job--_gb_1.mp3', 'flashcard', 'I love my job.', NULL),
(168, 10, 'Work', 'Làm việc', 'https://loremflickr.com/400/300/work', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/work--_gb_1.mp3', 'flashcard', 'Work hard.', NULL),
(169, 10, 'Goal', 'Mục tiêu', 'https://loremflickr.com/400/300/target', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/goal--_gb_1.mp3', 'fill_gap', 'Reach your ___.', NULL),
(170, 10, 'Plan', 'Kế hoạch', 'https://loremflickr.com/400/300/plan', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/plan--_gb_1.mp3', 'mixed', 'Make a plan.', NULL),
(171, 10, 'Project', 'Dự án', 'https://loremflickr.com/400/300/project', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/project--_gb_1.mp3', 'flashcard', 'New project.', NULL),
(172, 10, 'Team', 'Đội nhóm', 'https://loremflickr.com/400/300/team', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/team--_gb_1.mp3', 'flashcard', 'Teamwork is key.', NULL),
(173, 10, 'Report', 'Báo cáo', 'https://loremflickr.com/400/300/report', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/report--_gb_1.mp3', 'fill_gap', 'Write a ___.', NULL),
(174, 10, 'Data', 'Dữ liệu', 'https://loremflickr.com/400/300/data', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/data--_gb_1.mp3', 'mixed', 'Analyze data.', NULL),
(175, 10, 'Computer', 'Máy tính', 'https://loremflickr.com/400/300/computer', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/computer--_gb_1.mp3', 'flashcard', 'Laptop computer.', NULL),
(176, 10, 'Internet', 'Mạng', 'https://loremflickr.com/400/300/internet', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/internet--_gb_1.mp3', 'flashcard', 'Surf the internet.', NULL),
(177, 10, 'Email', 'Thư điện tử', 'https://loremflickr.com/400/300/email', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/email--_gb_1.mp3', 'fill_gap', 'Send an ___.', NULL),
(178, 10, 'Phone', 'Điện thoại', 'https://loremflickr.com/400/300/phone', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/phone--_gb_1.mp3', 'mixed', 'Answer the phone.', NULL),
(179, 10, 'Contract', 'Hợp đồng', 'https://loremflickr.com/400/300/contract', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/contract--_gb_1.mp3', 'flashcard', 'Sign the contract.', NULL),
(180, 10, 'Deal', 'Thỏa thuận', 'https://loremflickr.com/400/300/handshake', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/deal--_gb_1.mp3', 'flashcard', 'It is a deal.', NULL),
(181, 10, 'Risk', 'Rủi ro', 'https://loremflickr.com/400/300/risk', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/risk--_gb_1.mp3', 'fill_gap', 'High ___.', NULL),
(182, 10, 'Idea', 'Ý tưởng', 'https://loremflickr.com/400/300/idea', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/idea--_gb_1.mp3', 'mixed', 'Great idea.', NULL),
(183, 10, 'Success', 'Thành công', 'https://loremflickr.com/400/300/success', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/success--_gb_1.mp3', 'flashcard', 'Wish you success.', NULL),
(184, 10, 'Failure', 'Thất bại', 'https://loremflickr.com/400/300/fail', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/failure--_gb_1.mp3', 'flashcard', 'Learn from failure.', NULL),
(185, 10, 'Growth', 'Tăng trưởng', 'https://loremflickr.com/400/300/growth', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/growth--_gb_1.mp3', 'fill_gap', 'Business ___.', NULL),
(186, 10, 'Profit', 'Lợi nhuận', 'https://loremflickr.com/400/300/profit', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/profit--_gb_1.mp3', 'mixed', 'Make a profit.', NULL),
(187, 10, 'Loss', 'Thua lỗ', 'https://loremflickr.com/400/300/loss', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/loss--_gb_1.mp3', 'flashcard', 'Avoid loss.', NULL),
(188, 10, 'Tax', 'Thuế', 'https://loremflickr.com/400/300/tax', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/tax--_gb_1.mp3', 'flashcard', 'Pay tax.', NULL),
(189, 10, 'Law', 'Luật pháp', 'https://loremflickr.com/400/300/law', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/law--_gb_1.mp3', 'fill_gap', 'Follow the ___.', NULL),
(190, 10, 'Skill', 'Kỹ năng', 'https://loremflickr.com/400/300/skill', 'https://ssl.gstatic.com/dictionary/static/sounds/oxford/skill--_gb_1.mp3', 'mixed', 'Improve your skill.', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `learning_history`
--
ALTER TABLE `learning_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_history_user` (`user_id`),
  ADD KEY `idx_history_level` (`level_id`);

--
-- Indexes for table `learning_history_items`
--
ALTER TABLE `learning_history_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_hist_item_history` (`history_id`);

--
-- Indexes for table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_level_progress`
--
ALTER TABLE `user_level_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_level` (`user_id`,`level_id`);

--
-- Indexes for table `vocabularies`
--
ALTER TABLE `vocabularies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_level` (`level_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `learning_history`
--
ALTER TABLE `learning_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `learning_history_items`
--
ALTER TABLE `learning_history_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `levels`
--
ALTER TABLE `levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_level_progress`
--
ALTER TABLE `user_level_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `vocabularies`
--
ALTER TABLE `vocabularies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `learning_history_items`
--
ALTER TABLE `learning_history_items`
  ADD CONSTRAINT `fk_hist_item_history` FOREIGN KEY (`history_id`) REFERENCES `learning_history` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vocabularies`
--
ALTER TABLE `vocabularies`
  ADD CONSTRAINT `vocabularies_ibfk_1` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
