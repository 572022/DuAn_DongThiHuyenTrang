-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2025 at 03:51 PM
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
-- Database: `db_ordergogi`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_account`
--

CREATE TABLE `tbl_account` (
  `id_account` int(11) NOT NULL,
  `username` varchar(10) NOT NULL,
  `password` varchar(50) NOT NULL,
  `phone` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_account`
--

INSERT INTO `tbl_account` (`id_account`, `username`, `password`, `phone`) VALUES
(1, 'vnkhanh', 'abc@123', '0792672850'),
(2, 'dttht', 'efg@123', '0786372670');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bill`
--

CREATE TABLE `tbl_bill` (
  `id_bill` int(11) NOT NULL,
  `id_table` int(11) NOT NULL,
  `date_check_in` datetime NOT NULL,
  `date_check_out` datetime NOT NULL,
  `id_account` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `total_amount` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_bill`
--

INSERT INTO `tbl_bill` (`id_bill`, `id_table`, `date_check_in`, `date_check_out`, `id_account`, `status`, `total_amount`) VALUES
(57, 6, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bill_info`
--

CREATE TABLE `tbl_bill_info` (
  `id_bill_info` int(11) NOT NULL,
  `id_bill` int(11) NOT NULL,
  `id_food` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_food`
--

CREATE TABLE `tbl_food` (
  `id_food` int(11) NOT NULL,
  `food_name` varchar(50) NOT NULL,
  `id_category` int(11) NOT NULL,
  `status` bit(1) NOT NULL,
  `image` varchar(50) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_food`
--

INSERT INTO `tbl_food` (`id_food`, `food_name`, `id_category`, `status`, `image`, `price`) VALUES
(1, 'Salad mùa xuân', 1, b'1', '60018682_Salad_mua_xuan_1.jpg', 79000.00),
(2, 'Há cảo truyền thống Hàn Quốc', 1, b'1', '60000131_ha_cao_tt_1.jpg', 79000.00),
(3, 'Set kimbap (ALC)', 1, b'1', '60000127_kimbap_chien_1.jpg', 59000.00),
(4, 'Toboki xào hải sản', 1, b'1', '60000147_Tokboki_xao_hai_san_1.jpg', 109000.00),
(5, 'Dê quân cờ', 2, b'1', '60017517_decatquanco_1_1.jpg', 169000.00),
(6, 'Dê nướng tảng', 2, b'1', '60017516_de_nuong_tang_1_1.jpg', 169000.00),
(7, 'Diềm bụng bò Mỹ/ Canada Tươi/ sốt OBT 200g', 2, b'1', '60010635_diem_bung_tuoi_200_1.jpg', 379000.00),
(8, 'Diềm bụng bò Mỹ/ Canada Tươi/ sốt OBT 100g', 2, b'1', '60010634_diem_bung_tuoi_100_1.jpg', 199000.00),
(9, 'Sườn heo gabi', 3, b'1', '60000137_suon_heo_galbi_1.jpg', 139000.00),
(10, 'Má heo Mỹ tươi/sốt obathan', 3, b'1', '60000080_ma_heo_obathan_1.jpg', 149000.00),
(11, 'Nạc vai heo Mỹ sốt OBT/tươi', 3, b'1', '60000046_nac_vai_cay_1_1.jpg', 109000.00),
(12, 'Sườn heo Mỹ sốt Obathan ALC', 3, b'1', '60000031_Suon_heo_sot_Gabil_1_1.jpg', 149000.00),
(12, 'Mỳ tương đen', 4, b'1', '60017526_mituongden_1_1.jpg', 89000.00),
(13, 'Cơm rang kim chi', 4, b'1', '60013787_comrang_kimchi-min_1_1.jpg', 79000.00),
(14, 'Canh rong biển thịt', 4, b'1', '60000100_Canh_rong_bien_thit_1.jpg', 109000.00),
(15, 'Lẩu dê', 5, b'1', '60017527_laude_1.jpg', 389000.00),
(16, 'Lẩu bull gogi (cỡ lớn)', 5, b'1', '60000114_lau_bulgogi_1.jpg', 309000.00),
(17, 'Tôm nướng Gogi', 6, b'1', '60012739_tom_alc_1.jpg', 229000.00),
(18, 'Bào ngư', 6, b'1', '60008388_bao_ngu_1.jpg', 309000.00),
(19, 'Kem Caramen Flan cake', 7, b'1', '60000165_Caramel_1_1.jpg', 15000.00),
(20, 'Kem tươi vị Chocolate', 7, b'1', '60000154_kem_scl_1_1.jpg', 15000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_food_category`
--

CREATE TABLE `tbl_food_category` (
  `id_category` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_food_category`
--

INSERT INTO `tbl_food_category` (`id_category`, `category_name`) VALUES
(1, 'Khai vị & Ăn kèm'),
(2, 'Thịt bò'),
(3, 'Thịt heo'),
(4, 'Cơm & Canh & Mỳ'),
(5, 'Lẩu'),
(6, 'Hải sản'),
(7, 'Tráng miệng');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_table`
--

CREATE TABLE `tbl_table` (
  `id_table` int(11) NOT NULL,
  `name_table` varchar(20) NOT NULL,
  `status` bit(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_table`
--

INSERT INTO `tbl_table` (`id_table`, `name_table`, `status`) VALUES
(1, 'Bàn 1', b'1'),
(2, 'Bàn 2', b'1'),
(3, 'Bàn 3', b'1'),
(4, 'Bàn 4', b'0'),
(5, 'Bàn 5', b'1'),
(6, 'Bàn 6', b'1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_account`
--
ALTER TABLE `tbl_account`
  ADD PRIMARY KEY (`id_account`);

--
-- Indexes for table `tbl_bill`
--
ALTER TABLE `tbl_bill`
  ADD PRIMARY KEY (`id_bill`),
  ADD KEY `id_account` (`id_account`);

--
-- Indexes for table `tbl_bill_info`
--
ALTER TABLE `tbl_bill_info`
  ADD PRIMARY KEY (`id_bill_info`,`id_bill`),
  ADD KEY `id_bill` (`id_bill`),
  ADD KEY `id_food` (`id_food`);

--
-- Indexes for table `tbl_food`
--
ALTER TABLE `tbl_food`
  ADD PRIMARY KEY (`id_food`,`id_category`),
  ADD KEY `id_category` (`id_category`);

--
-- Indexes for table `tbl_food_category`
--
ALTER TABLE `tbl_food_category`
  ADD PRIMARY KEY (`id_category`);

--
-- Indexes for table `tbl_table`
--
ALTER TABLE `tbl_table`
  ADD PRIMARY KEY (`id_table`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_account`
--
ALTER TABLE `tbl_account`
  MODIFY `id_account` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_bill`
--
ALTER TABLE `tbl_bill`
  MODIFY `id_bill` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `tbl_bill_info`
--
ALTER TABLE `tbl_bill_info`
  MODIFY `id_bill_info` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_food`
--
ALTER TABLE `tbl_food`
  MODIFY `id_food` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbl_food_category`
--
ALTER TABLE `tbl_food_category`
  MODIFY `id_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_table`
--
ALTER TABLE `tbl_table`
  MODIFY `id_table` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_bill`
--
ALTER TABLE `tbl_bill`
  ADD CONSTRAINT `tbl_bill_ibfk_1` FOREIGN KEY (`id_account`) REFERENCES `tbl_account` (`id_account`);

--
-- Constraints for table `tbl_bill_info`
--
ALTER TABLE `tbl_bill_info`
  ADD CONSTRAINT `tbl_bill_info_ibfk_1` FOREIGN KEY (`id_bill`) REFERENCES `tbl_bill` (`id_bill`),
  ADD CONSTRAINT `tbl_bill_info_ibfk_2` FOREIGN KEY (`id_food`) REFERENCES `tbl_food` (`id_food`);

--
-- Constraints for table `tbl_food`
--
ALTER TABLE `tbl_food`
  ADD CONSTRAINT `tbl_food_ibfk_1` FOREIGN KEY (`id_category`) REFERENCES `tbl_food_category` (`id_category`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
