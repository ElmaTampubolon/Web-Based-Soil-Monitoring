-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 19, 2025 at 05:14 PM
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
-- Database: `db_iot`
--

-- --------------------------------------------------------

--
-- Table structure for table `data`
--

CREATE TABLE `data` (
  `id` int(11) NOT NULL,
  `serial_number` varchar(8) NOT NULL,
  `name` varchar(25) NOT NULL,
  `value` varchar(10) NOT NULL,
  `time` datetime NOT NULL DEFAULT current_timestamp(),
  `mqtt_topic` text NOT NULL,
  `sensor_actuator` enum('sensor','actuator') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `data`
--

INSERT INTO `data` (`id`, `serial_number`, `name`, `value`, `time`, `mqtt_topic`, `sensor_actuator`) VALUES
(8, '12345678', 'ph', '11.3', '2025-06-08 09:27:52', 'coba-coba/12345678/ph', 'sensor'),
(9, '12345678', 'turbidity', '106', '2025-06-08 09:27:53', 'coba-coba/12345678/turbidity', 'sensor'),
(11, '12345678', 'tds', '1', '2025-06-08 09:27:58', 'coba-coba/12345678/tds', 'sensor'),
(12, '12345678', 'ec', '0.03', '2025-06-08 09:27:58', 'coba-coba/12345678/ec', 'sensor'),
(13, '12345678', 'ph', '11.4', '2025-06-08 09:28:02', 'coba-coba/12345678/ph', 'sensor'),
(14, '12345678', 'tds', '0', '2025-06-08 09:28:03', 'coba-coba/12345678/tds', 'sensor'),
(15, '12345678', 'ec', '0.00', '2025-06-08 09:28:03', 'coba-coba/12345678/ec', 'sensor'),
-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `serial_number` varchar(8) NOT NULL,
  `mcu_type` varchar(15) NOT NULL,
  `location` text NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `protocol` varchar(50) DEFAULT NULL,
  `created_time` datetime NOT NULL DEFAULT current_timestamp(),
  `active` enum('Yes','No') NOT NULL DEFAULT 'Yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`serial_number`, `mcu_type`, `location`, `latitude`, `longitude`, `role`, `protocol`, `created_time`, `active`) VALUES
('12345678', 'ESP32', 'Rumah', -8.62580011, 115.16768735, 'Master', 'MQTT', '2025-05-10 05:38:13', 'Yes');

-- --------------------------------------------------------

--
-- Table structure for table `device_sensors`
--

CREATE TABLE `device_sensors` (
  `id` int(11) NOT NULL,
  `device_serial_number` varchar(8) NOT NULL,
  `sensor_name` varchar(50) NOT NULL,
  `sensor_display_name` varchar(100) NOT NULL,
  `sensor_unit` varchar(20) DEFAULT NULL,
  `sensor_category` varchar(50) DEFAULT NULL,
  `sensor_type` varchar(50) DEFAULT NULL,
  `communication_protocol` varchar(50) DEFAULT NULL,
  `data_source` varchar(20) DEFAULT 'Master',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_update` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `device_sensors`
--

INSERT INTO `device_sensors` (`id`, `device_serial_number`, `sensor_name`, `sensor_display_name`, `sensor_unit`, `sensor_category`, `sensor_type`, `communication_protocol`, `data_source`, `is_active`, `last_update`, `created_at`) VALUES
(2, '12345678', 'suhu_udara', 'BME280', 'C', 'Lingkungan', 'I2C', 'Digital Pin', 'Master', 1, '2025-08-08 02:01:56', '2025-07-06 00:13:43'),
(4, '12345678', 'kelembaban', 'BME280', '%', 'Lingkungan', 'I2C', 'Digital Pin', 'Master', 1, '2025-08-08 02:00:51', '2025-07-15 03:41:46'),
(5, '12345678', 'ketinggian', 'HY-SRF05', 'cm', 'Lingkungan', 'Digital', 'Digital Pin', 'Master', 1, '2025-08-08 02:03:26', '2025-07-19 11:41:25'),
(6, '12345678', 'amonia', 'MQ-137', 'ppm', 'Kualitas Air', 'Analog', 'Analog Pin', 'Slave', 1, '2025-08-08 00:53:45', '2025-08-05 15:27:43'),
(7, '12345678', 'dissolved', 'Dissolved Oxygen', 'mg/L', 'Kualitas Air', 'Analog', 'Analog Pin', 'Slave', 1, '2025-08-08 02:02:46', '2025-08-05 15:31:00'),
(8, '12345678', 'suhu_air', 'DS18B20', 'C', 'Kualitas Air', 'OneWire', 'Digital Pin', 'Slave', 1, '2025-08-08 01:52:30', '2025-08-05 15:31:51'),
(9, '12345678', 'turbidity', 'Turbidity', 'NTU', 'Kualitas Air', 'Analog', 'Analog Pin', 'Slave', 1, '2025-08-08 01:51:20', '2025-08-05 15:33:46'),
(10, '12345678', 'ph', 'Ph DFRobot', 'ph', 'Kualitas Air', 'Analog', 'Analog Pin', 'Slave', 1, '2025-08-08 02:03:31', '2025-08-05 15:35:18'),
(11, '12345678', 'tds', 'TDS', 'ppm', 'Kualitas Air', 'Analog', 'Analog Pin', 'Slave', 1, '2025-08-08 01:59:36', '2025-08-05 15:36:24'),
(12, '12345678', 'angin', 'Anemometer', 'm/s', 'Lingkungan', 'Digital', 'Digital Pin', 'Slave', 1, '2025-08-08 00:53:45', '2025-08-05 15:37:16'),
(13, '12345678', 'ec', 'EC DFRobot', 'µS/cm', 'Kualitas Air', 'Analog', 'Analog Pin', 'Slave', 1, '2025-08-08 02:03:31', '2025-08-05 15:38:10');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `username` varchar(30) NOT NULL,
  `password` text NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `role` enum('Admin','User') NOT NULL DEFAULT 'User',
  `active` enum('Yes','No') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`username`, `password`, `fullname`, `role`, `active`) VALUES
('Dragon', '$2y$10$wSpmqgvIcQOqzIgrRHUJkedgrZ96y0XInH1X4DtrXu/LSj3AzjEJm', 'Rahmat', 'User', 'Yes'),
('Magnuver', '$2y$10$45a/Yey5Z612baXmMTgpB.XtWO9rbacSm9iQ5K7sqP6Pl.jsL9Qsm', 'Moh Prayogi', 'Admin', 'Yes');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data`
--
ALTER TABLE `data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `serial_number` (`serial_number`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`serial_number`);

--
-- Indexes for table `device_sensors`
--
ALTER TABLE `device_sensors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `device_sensor_unique` (`device_serial_number`,`sensor_name`),
  ADD KEY `fk_device_sn_idx` (`device_serial_number`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data`
--
ALTER TABLE `data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5511;

--
-- AUTO_INCREMENT for table `device_sensors`
--
ALTER TABLE `device_sensors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `data`
--
ALTER TABLE `data`
  ADD CONSTRAINT `data_ibfk_1` FOREIGN KEY (`serial_number`) REFERENCES `devices` (`serial_number`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `device_sensors`
--
ALTER TABLE `device_sensors`
  ADD CONSTRAINT `fk_device_sn` FOREIGN KEY (`device_serial_number`) REFERENCES `devices` (`serial_number`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
