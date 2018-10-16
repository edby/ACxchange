-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 2018-10-12 17:22:56
-- 服务器版本： 5.6.41
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `testacx`
--

-- --------------------------------------------------------

--
-- 表的结构 `kyc_reject_lists`
--

CREATE TABLE `kyc_reject_lists` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `reason_ids` varchar(255) NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `kyc_reject_lists`
--

INSERT INTO `kyc_reject_lists` (`id`, `user_id`, `reason_ids`, `created_at`, `updated_at`) VALUES
(1, 128, '[\"2\",\"3\",\"4\"]', '2018-10-12 09:21:41', '2018-10-12 09:21:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kyc_reject_lists`
--
ALTER TABLE `kyc_reject_lists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kyc_reject_lists_user_id_index` (`user_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `kyc_reject_lists`
--
ALTER TABLE `kyc_reject_lists`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
