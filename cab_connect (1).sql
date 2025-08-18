-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 30, 2025 at 06:32 AM
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
-- Database: `cab_connect`
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
  `trip_status` enum('Pending','Accepted','Completed','Cancelled') DEFAULT 'Pending',
  `driver_eta` varchar(50) DEFAULT NULL,
  `fare` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `availability_status` enum('Available','Not Available') DEFAULT 'Not Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver`
--

INSERT INTO `driver` (`driver_id`, `name`, `email`, `phone`, `address`, `license_number`, `vehicle_type`, `vehicle_model`, `vehicle_number`, `vehicle_photo`, `photo`, `license_file`, `id_proof`, `password`, `approval_status`, `availability_status`, `created_at`) VALUES
('D139', 'Adeep Baby', 'adeep@123', '7894568623', 'Kadamapuzha(H),puthakuzhy', '7896-5232-7522-7823', 'Auto-Rickshaw', 'Ape', 'KL 34 J 5556', 'uploads/drivers/688998b51beb0_1753847989.png', 'uploads/drivers/688998b51b960_1753847989.webp', 'uploads/drivers/688998b51aabd_1753847989.jpg', 'uploads/drivers/688998b51c272_1753847989.pdf', '$2y$10$RjdJHE7uZ3464wGj/Cqdieg6kAAoHK23xAMrjrXuTlxXoZ32zCYjW', 'Pending', 'Not Available', '2025-07-30 03:59:49'),
('D331', 'Alex Shelji', 'alex123@gmail.com', '256645454', 'Pullatte, Kattappana', '1234-4476-4469-8562', 'Car', 'Ertiga', 'KL 37 G 8816', NULL, NULL, NULL, NULL, '$2y$10$1FBWjbkyM9EUcFz5RMvA1.ONzxJWWGqnENWPXsOtxqgWaR2izzleu', 'Pending', 'Not Available', '2025-07-23 03:40:52'),
('D348', 'Ouseppachan Jojo', 'ouseppachan@gmail.com', '7894561964', 'Neervelil(H) Kanjirappally', '7896-5422-7522-7896', 'Car', 'Swift dzire', 'KL 34 H 5678', 'uploads/drivers/688991b7b461c_1753846199.webp', 'uploads/drivers/688991b7b41cc_1753846199.webp', 'uploads/drivers/688991b7b3b17_1753846199.jpg', 'uploads/drivers/688991b7b4a78_1753846199.pdf', '$2y$10$pHx9u/CU124owH/vMDJbkeuVfmSEjmj1QG/F/BP.UolTC07UMf4E2', 'Pending', 'Not Available', '2025-07-30 03:29:59'),
('D358', 'Midhun M Mathai', 'midhun@gmail.com', '7894565225', 'Margattukkunnel(H),Kumily', '7896-5422-7522-2255', 'Car', 'Innova', 'KL 37 G 7678', 'uploads/drivers/6889949fd6d72_1753846943.webp', 'uploads/drivers/6889949fd69be_1753846943.webp', 'uploads/drivers/6889949fd65c7_1753846943.jpg', 'uploads/drivers/6889949fd71ce_1753846943.pdf', '$2y$10$h86P198Ua3myDGilcObhB.C3IQD.BaviKENZtJ0CsRggch8vPbC1C', 'Pending', 'Not Available', '2025-07-30 03:42:24'),
('D415', 'Jacob Oomen', 'jacob@gmail.com', '7894567225', 'Kollamkudi(H),Kattappana', '7896-5422-7522-4355', 'Two-Wheeler', 'Splender', 'KL 06 G 7678', 'uploads/drivers/688995b4c4be9_1753847220.png', 'uploads/drivers/688995b4c4471_1753847220.webp', 'uploads/drivers/688995b4c3d03_1753847220.jpg', 'uploads/drivers/688995b4c5244_1753847220.pdf', '$2y$10$mCJ.JsULnm43xCXCm/amdeHkGA8wrMw5uf/NJeWaTye4IA5fQ9rT2', 'Pending', 'Not Available', '2025-07-30 03:47:00'),
('D564', 'Don K Saji', 'donsajikumily@gmail.com', '7561239967', 'Kalapurackal House Kumily', '1234-7894-4469-5477', 'Two-Wheeler', 'Triumph', 'KL 37 C 8956', NULL, NULL, NULL, NULL, '$2y$10$Ern7ITr8F/1xM9TjoCqfJeEekNT7sc2Xb.es4lHc36O1gjobkKYUO', 'Approved', 'Not Available', '2025-07-17 04:19:34'),
('D769', 'Ajo Joseph', 'ajojoseph@gmail.com', '7894561124', 'Kallukunnel(H) Ponkunnam', '7896-5422-7522-7823', 'Auto-Rickshaw', 'Ape', 'KL 34 H 5895', 'uploads/drivers/6889924de48e6_1753846349.webp', 'uploads/drivers/6889924de44f1_1753846349.webp', 'uploads/drivers/6889924de3fed_1753846349.jpg', 'uploads/drivers/6889924de4cba_1753846349.pdf', '$2y$10$Fnx4amTU5emehPZNyE90.eKfkanS.D8whrbaUb2mrNQZgXDAQvkFq', 'Pending', 'Not Available', '2025-07-30 03:32:30'),
('D954', 'Alby Jeetho', 'alby@gmail.com', '7894527657', 'Thottupuram(H),Kanjirappally', '7896-5422-3452-4355', 'Auto-Rickshaw', 'Ape', 'KL 34 H 2754', 'uploads/drivers/68899b6da5ea2_1753848685.webp', 'uploads/drivers/68899b6da5a0a_1753848685.webp', 'uploads/drivers/68899b6da552a_1753848685.jpg', 'uploads/drivers/68899b6da64fe_1753848685.pdf', '$2y$10$/9lLVjuc5pzQplmLnF23zuE10udbPeyBVPoMGqVzbeLepOtJM8hLq', 'Pending', 'Not Available', '2025-07-30 04:11:25'),
('D994', 'Nikhil Sunil', 'nikhil@gmail.com', '7894901235', 'Kollamkulam(H),Mundakkayam', '7896-5422-7592-2255', 'Two-Wheeler', 'Activa', 'KL 34 G 9495', 'uploads/drivers/6889972a5811a_1753847594.webp', 'uploads/drivers/6889972a57ce3_1753847594.webp', 'uploads/drivers/6889972a57804_1753847594.jpg', 'uploads/drivers/6889972a585d2_1753847594.pdf', '$2y$10$TOLeDxA4ulzEQ6pXkeXcTe2OnuMYFkk0Sn7Gakwz1IAAz31jyvziO', 'Pending', 'Not Available', '2025-07-30 03:53:14');

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
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

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
