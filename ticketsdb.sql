-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 07, 2025 at 05:04 PM
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
-- Database: `ticketsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin123!');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(255) NOT NULL,
  `userid` int(255) NOT NULL,
  `flight_id` int(11) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` time(6) NOT NULL,
  `class` varchar(255) NOT NULL,
  `passengers` int(255) NOT NULL,
  `adults` int(255) NOT NULL,
  `children` int(255) NOT NULL,
  `infants` int(255) NOT NULL,
  `price` int(11) NOT NULL,
  `time_created` timestamp(6) NOT NULL DEFAULT current_timestamp(6) ON UPDATE current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `userid`, `flight_id`, `destination`, `date`, `time`, `class`, `passengers`, `adults`, `children`, `infants`, `price`, `time_created`) VALUES
(1, 3, 0, '', '0000-00-00', '00:00:00.000000', '', 0, 0, 0, 0, 0, '2025-05-26 11:21:19.150051'),
(2, 3, 0, 'destination', '2025-06-26', '05:57:00.000000', 'first', 0, 4, 3, 2, 0, '2025-05-25 06:54:15.124290'),
(3, 3, 0, 'destination', '2025-05-30', '07:08:00.000000', 'business', 0, 4, 3, 2, 0, '2025-05-25 07:04:40.503917'),
(4, 3, 0, 'destination', '2025-05-30', '07:08:00.000000', 'business', 0, 4, 3, 2, 0, '2025-05-25 07:04:40.518962'),
(5, 3, 0, 'usa', '2025-05-20', '07:22:00.000000', 'business', 0, 5, 1, 0, 0, '2025-05-25 07:07:37.994336'),
(6, 3, 0, 'usa', '2025-05-20', '07:22:00.000000', 'business', 0, 5, 1, 0, 0, '2025-05-25 07:07:38.009729'),
(7, 4, 0, 'south_korea', '2025-05-13', '05:11:00.000000', 'business', 0, 4, 1, 1, 0, '2025-05-25 07:09:32.451097'),
(8, 5, 0, 'thailand', '2025-05-29', '21:02:00.000000', 'economy', 0, 3, 2, 2, 0, '2025-05-26 13:14:27.639821'),
(9, 6, 0, 'south_korea', '2025-07-04', '11:34:00.000000', 'business', 0, 3, 0, 0, 0, '2025-06-01 15:20:00.233199'),
(10, 6, 0, 'south_korea', '2025-06-05', '10:50:00.000000', 'first', 0, 3, 2, 0, 0, '2025-06-01 13:48:39.827489'),
(51, 10, 19, 'france', '2025-06-30', '22:23:00.000000', 'first', 10, 10, 0, 0, 0, '2025-06-05 17:19:52.701613'),
(52, 10, 20, 'france', '2025-07-05', '09:00:00.000000', 'business', 1, 1, 0, 0, 1240, '2025-06-06 16:03:04.487274'),
(53, 10, 21, 'australia', '2025-06-27', '07:00:00.000000', 'first', 8, 1, 6, 1, 112750, '2025-06-06 16:08:24.244918');

-- --------------------------------------------------------

--
-- Table structure for table `flights`
--

CREATE TABLE `flights` (
  `id` int(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` time(6) NOT NULL,
  `first_class_seats` int(11) NOT NULL,
  `business_seats` int(11) NOT NULL,
  `economy_seats` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flights`
--

INSERT INTO `flights` (`id`, `destination`, `date`, `time`, `first_class_seats`, `business_seats`, `economy_seats`) VALUES
(15, 'australia', '2025-07-01', '21:02:00.000000', 10, 30, 74),
(16, 'australia', '2025-06-07', '07:00:00.000000', 10, 30, 88),
(17, 'france', '2025-06-08', '09:00:00.000000', 10, 30, 100),
(18, 'canada', '2025-06-08', '08:00:00.000000', 10, 30, 100),
(19, 'france', '2025-06-30', '22:23:00.000000', 0, 30, 100),
(20, 'france', '2025-07-05', '09:00:00.000000', 10, 29, 100),
(21, 'australia', '2025-06-27', '07:00:00.000000', 2, 30, 100),
(22, 'usa', '2025-07-05', '16:00:00.000000', 10, 30, 100);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(200) NOT NULL,
  `fname` varchar(30) NOT NULL,
  `lname` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `sex` varchar(11) NOT NULL,
  `phonenumber` varchar(11) NOT NULL,
  `time_created` timestamp(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `username`, `password`, `birthday`, `sex`, `phonenumber`, `time_created`) VALUES
(6, 'a', 'a', 'a@gmail.com', 'test1', '$2y$10$JBlTGwty8BG8pFyx.zt.W.YstCG3XF66y4rDQ/Ap9DUq7WHlhwQRa', '2004-02-20', '', '12345678911', '2025-06-01 09:50:08.637864'),
(7, 'a', 'a', 'a2@gmail.com', 'test2', '$2y$10$/S6L3wTkaWRrNJwcU4NDgeTvGLt16hb6pu4UoMVHkaZbJRwDii9Yy', '2004-02-20', 'other', '12345678911', '2025-06-01 09:51:46.709160'),
(8, 'a', 'a', 'a3@gmail.com', 'test3', '$2y$10$9y9miY94WfTXc0YTzySnzeMZy8D17CjgSNIy5IDrgj4jir9nPzDBq', '2004-02-20', 'female', '12345678911', '2025-06-01 09:51:56.243491'),
(9, 'testing', 'four', 'test4@gmail.com', 'test4', '$2y$10$o8ZN6m4DMVlmHKfUmlkEPOVxQBqNw2.l1wSDfLIjGQFSD8RAAwJ1i', '2000-06-07', 'female', '12345678911', '2025-06-01 15:10:06.259457'),
(10, 'test', 'five', 'test5@gmail.com', 'test5', '$2y$10$OaHPrfUIpNsjwtEvf2dojOgH3r3TD4w8h8qJ8Mb6SVv.neFoIY9am', '1999-11-17', 'female', '12345678911', '2025-06-01 16:01:27.614443');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `flights`
--
ALTER TABLE `flights`
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
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `flights`
--
ALTER TABLE `flights`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
