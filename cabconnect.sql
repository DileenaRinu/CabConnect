-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 20, 2025 at 09:33 AM
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
-- Database: `cabconnect`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `email`, `password`, `created_at`) VALUES
('ADM001', 'Admin', 'admin@cabconnect.com', '$2y$10$5o4qGJApKT/pKRED7X4JJ.5K6d/SnSjcNimRZokwi3vqiUTUEF776', '2025-07-09 17:21:19');

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `booking_id` int(11) NOT NULL,
  `customer_id` varchar(10) DEFAULT NULL,
  `driver_id` varchar(10) DEFAULT NULL,
  `pickup_location` varchar(255) NOT NULL,
  `drop_location` varchar(255) NOT NULL,
  `vehicle_type` varchar(50) NOT NULL,
  `booking_time` datetime DEFAULT current_timestamp(),
  `trip_status` enum('Pending','Accepted','Started','Completed','Cancelled') DEFAULT 'Pending',
  `fare` decimal(10,2) DEFAULT NULL,
  `rate_per_km` float DEFAULT NULL,
  `distance` float DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT 'Pending',
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `driver_rating` int(11) DEFAULT NULL,
  `expected_eta_driver` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `customer_id`, `driver_id`, `pickup_location`, `drop_location`, `vehicle_type`, `booking_time`, `trip_status`, `fare`, `rate_per_km`, `distance`, `payment_status`, `start_time`, `end_time`, `driver_rating`, `expected_eta_driver`) VALUES
(126, 'C592', 'D358', 'Cochin International Airport', 'Kottayam', 'car', '2025-09-20 12:06:54', 'Started', 1338.00, 15, 89.2, 'Pending', '2025-09-20 12:54:49', NULL, NULL, '5:30 pm'),
(127, 'C562', 'D348', 'Vallakkadavu,Kattappana', 'Tree Top Resort Thekkady', 'car', '2025-09-20 12:08:48', 'Started', 384.00, 16, 24, 'Pending', '2025-09-20 12:54:23', NULL, NULL, '12:15pm');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `name`, `email`, `phone`, `address`, `password`, `created_at`) VALUES
('C207', 'Anna G Padiyara', 'annapadiyara@gmail.com', '7894561235', 'Padiyara (H) Kumily', '$2y$10$D2tmiRZTXtqNQMb8vmb8oe055JSqmWXcmtn4uWvSQ2XymX1GEYPKO', '2025-07-30 03:20:26'),
('C215', 'Minta Shajan', 'mintashajan@gmail.com', '7894568651', 'Muckumkal (H) Kumily', '$2y$10$aZGUP3Jx4rRTrUcVviRD6e.iWky/79WJTBort5YYRhb3Sx475poJC', '2025-07-30 03:24:14'),
('C299', 'Don K Saji', 'donksaji@gmail.com', '9495037789', 'Kalapurackal(H) Attappallam P.O Kumily', '$2y$10$zm9Qd5UlLV1MOaBxM6bIVe/534ETmWFVLCyNk4DRjjVsBKzSjDRnW', '2025-08-22 05:36:26'),
('C562', 'Aida', 'aidavarghese123@gmail.com', '8234567890', 'Ayinadathu (h) kumily po kumily', '$2y$10$GVQSrfsaWEBPUfWEFldk2uHQ2L04vnbpqvBU.HlDoQeyO98t.iSsO', '2025-09-18 06:15:31'),
('C571', 'Dileena Rinu', 'dileenarinu@gmail.com', '7561041916', 'Pulickal(H) Attappallam P.O Kumily', '$2y$10$cIYvvW4TpF2SRnOxPRAOluQkwRafovLnnytXqjT.5ycCoMLqw9cde', '2025-09-18 06:54:39'),
('C592', 'Anjela Maria Reji', 'anjelamaria@gmail.com', '7894568789', 'Kallumackal(H) Kanjirappally', '$2y$10$OtZCPEYcw7E/XnH2/kk7.O1JadRkTC7vVs1Udy2YQCFdd3p6k/bFe', '2025-07-30 03:25:51');

-- --------------------------------------------------------

--
-- Table structure for table `driver`
--

CREATE TABLE `driver` (
  `driver_id` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `vehicle_type` varchar(50) NOT NULL,
  `vehicle_model` varchar(100) NOT NULL,
  `vehicle_number` varchar(20) NOT NULL,
  `vehicle_photo` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `license_file` varchar(255) DEFAULT NULL,
  `id_proof` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `approval_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `availability_status` enum('Available','Not Available') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver`
--

INSERT INTO `driver` (`driver_id`, `name`, `email`, `phone`, `address`, `license_number`, `vehicle_type`, `vehicle_model`, `vehicle_number`, `vehicle_photo`, `photo`, `license_file`, `id_proof`, `password`, `approval_status`, `created_at`, `availability_status`) VALUES
('D139', 'Adeep Baby', 'adeep@gmail.com', '7894568623', 'Kadamapuzha(H),puthakuzhy', 'KL34 20220000678', 'AutoRickshaw', 'Ape', 'KL 34 J 5556', 'uploads/drivers/688998b51beb0_1753847989.png', 'uploads/drivers/688998b51b960_1753847989.webp', 'uploads/drivers/688998b51aabd_1753847989.jpg', 'uploads/drivers/688998b51c272_1753847989.pdf', '$2y$10$RjdJHE7uZ3464wGj/Cqdieg6kAAoHK23xAMrjrXuTlxXoZ32zCYjW', 'Approved', '2025-07-30 03:59:49', 'Available'),
('D291', 'Ayub Hassan', 'ayubhassan@gmail.com', '7561045612', 'Balkeez Mansil (H) Erattupetta P.O. Erattupetta', '1234-4476-4469-3218', 'TwoWheeler', 'Splendor', 'KL 37 C 6582', 'uploads/drivers/68cbae080cf53_1758178824.jpg', 'uploads/drivers/68cbae080cd99_1758178824.webp', 'uploads/drivers/68cbae080ca60_1758178824.jpg', 'uploads/drivers/68cbae080d81b_1758178824.pdf', '$2y$10$RkQiD9/HP9x8VBHc7xSEUue/cQd8AoVjkfCN0Z3W.LIfQEe5RZspS', 'Pending', '2025-09-18 07:00:24', 'Not Available'),
('D348', 'Ouseppachan Jojo', 'ouseppachan@gmail.com', '7894561964', 'Neervelil(H) Kanjirappally', 'KL37 202500008421', 'Car', 'Swift dzire', 'KL 34 H 5678', 'uploads/drivers/688991b7b461c_1753846199.webp', 'uploads/drivers/688991b7b41cc_1753846199.webp', 'uploads/drivers/688991b7b3b17_1753846199.jpg', 'uploads/drivers/688991b7b4a78_1753846199.pdf', '$2y$10$pHx9u/CU124owH/vMDJbkeuVfmSEjmj1QG/F/BP.UolTC07UMf4E2', 'Approved', '2025-07-30 03:29:59', 'Not Available'),
('D358', 'Midhun M Mathai', 'midhun@gmail.com', '7894565225', 'Margattukkunnel(H),Kumily', 'KL37 201800008749', 'Car', 'Innova', 'KL 37 G 7678', 'uploads/drivers/6889949fd6d72_1753846943.webp', 'uploads/drivers/6889949fd69be_1753846943.webp', 'uploads/drivers/6889949fd65c7_1753846943.jpg', 'uploads/drivers/6889949fd71ce_1753846943.pdf', '$2y$10$h86P198Ua3myDGilcObhB.C3IQD.BaviKENZtJ0CsRggch8vPbC1C', 'Approved', '2025-07-30 03:42:24', 'Not Available'),
('D415', 'Jacob Oomen', 'jacob@gmail.com', '7894567225', 'Kollamkudi(H),Kattappana', 'KL37 202000007141', 'TwoWheeler', 'Splender', 'KL 06 G 7678', 'uploads/drivers/688995b4c4be9_1753847220.png', 'uploads/drivers/688995b4c4471_1753847220.webp', 'uploads/drivers/688995b4c3d03_1753847220.jpg', 'uploads/drivers/688995b4c5244_1753847220.pdf', '$2y$10$mCJ.JsULnm43xCXCm/amdeHkGA8wrMw5uf/NJeWaTye4IA5fQ9rT2', 'Approved', '2025-07-30 03:47:00', 'Available'),
('D769', 'Ajo Joseph', 'ajojoseph@gmail.com', '7894561124', 'Kallukunnel(H) Ponkunnam', 'KL37 201900001234', 'AutoRickshaw', 'Ape', 'KL 34 H 5895', 'uploads/drivers/6889924de48e6_1753846349.webp', 'uploads/drivers/6889924de44f1_1753846349.webp', 'uploads/drivers/6889924de3fed_1753846349.jpg', 'uploads/drivers/6889924de4cba_1753846349.pdf', '$2y$10$Fnx4amTU5emehPZNyE90.eKfkanS.D8whrbaUb2mrNQZgXDAQvkFq', 'Approved', '2025-07-30 03:32:30', 'Available'),
('D954', 'Alby Jeetho', 'alby@gmail.com', '7894527657', 'Thottupuram(H),Kanjirappally', 'KL37 201800008745', 'AutoRickshaw', 'Ape', 'KL 34 H 2754', 'uploads/drivers/68899b6da5ea2_1753848685.webp', 'uploads/drivers/68899b6da5a0a_1753848685.webp', 'uploads/drivers/68899b6da552a_1753848685.jpg', 'uploads/drivers/68899b6da64fe_1753848685.pdf', '$2y$10$/9lLVjuc5pzQplmLnF23zuE10udbPeyBVPoMGqVzbeLepOtJM8hLq', 'Approved', '2025-07-30 04:11:25', 'Available'),
('D994', 'Nikhil Sunil', 'nikhil@gmail.com', '7894901235', 'Kollamkulam(H),Mundakkayam', 'KL 34 202100008885', 'TwoWheeler', 'Activa', 'KL 34 G 9495', 'uploads/drivers/6889972a5811a_1753847594.webp', 'uploads/drivers/6889972a57ce3_1753847594.webp', 'uploads/drivers/6889972a57804_1753847594.jpg', 'uploads/drivers/6889972a585d2_1753847594.pdf', '$2y$10$TOLeDxA4ulzEQ6pXkeXcTe2OnuMYFkk0Sn7Gakwz1IAAz31jyvziO', 'Approved', '2025-07-30 03:53:14', 'Available');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `driver`
--
ALTER TABLE `driver`
  ADD PRIMARY KEY (`driver_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `driver` (`driver_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
