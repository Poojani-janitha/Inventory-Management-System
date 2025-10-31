-- Database Backup
-- Generated on: 2025-10-25 19:41:21
-- Database: inventory_system

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


-- Table structure for table `categories`
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `c_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  PRIMARY KEY (`c_id`),
  UNIQUE KEY `category_name` (`category_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `categories`
INSERT INTO `categories` VALUES ('4','4','Antacid','Antacid');
INSERT INTO `categories` VALUES ('1','1','Antibiotic','Antibiotic');
INSERT INTO `categories` VALUES ('8','8','Antidiabetic','Antidiabetic');
INSERT INTO `categories` VALUES ('14','14','Antidiarrheal','Antidiarrheal');
INSERT INTO `categories` VALUES ('16','16','Antifungal','Antifungal');
INSERT INTO `categories` VALUES ('7','7','Antihistamine','Antihistamine');
INSERT INTO `categories` VALUES ('9','9','Antihypertensive','Antihypertensive');
INSERT INTO `categories` VALUES ('15','15','Antiparasitic','Antiparasitic');
INSERT INTO `categories` VALUES ('6','6','Antiseptic','Antiseptic');
INSERT INTO `categories` VALUES ('10','10','Cholesterol','Cholesterol');
INSERT INTO `categories` VALUES ('5','5','Cough Syrup','Cough Syrup');
INSERT INTO `categories` VALUES ('2','2','Painkiller','Painkiller');
INSERT INTO `categories` VALUES ('11','11','Respiratory','Respiratory');
INSERT INTO `categories` VALUES ('12','12','Steroid','Steroid');
INSERT INTO `categories` VALUES ('13','13','Supplement','Supplement');
INSERT INTO `categories` VALUES ('17','17','Topical Steroid','Topical Steroid');
INSERT INTO `categories` VALUES ('3','3','Vitamin','Vitamin');


-- Table structure for table `product`
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `p_id` varchar(10) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `buying_price` decimal(10,2) NOT NULL COMMENT 'Price in LKR (Rs)',
  `selling_price` decimal(10,2) NOT NULL COMMENT 'Price in LKR (Rs)',
  `category_name` varchar(100) NOT NULL,
  `s_id` varchar(10) NOT NULL,
  `expire_date` date DEFAULT NULL,
  `recorded_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`p_id`),
  KEY `category_name` (`category_name`),
  KEY `s_id` (`s_id`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_name`) REFERENCES `categories` (`category_name`) ON UPDATE CASCADE,
  CONSTRAINT `product_ibfk_2` FOREIGN KEY (`s_id`) REFERENCES `supplier_info` (`s_id`) ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`p_id` like 'p%'),
  CONSTRAINT `CONSTRAINT_2` CHECK (`quantity` >= 0),
  CONSTRAINT `CONSTRAINT_3` CHECK (`selling_price` >= `buying_price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `product`
INSERT INTO `product` VALUES ('p001','p001','Amoxicillin 500mg','Amoxicillin 500mg','120','120','45.00','45.00','65.00','65.00','Antibiotic','Antibiotic','s01','s01','2026-08-15','2026-08-15','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p002','p002','Azithromycin 250mg','Azithromycin 250mg','90','90','65.00','65.00','85.00','85.00','Antibiotic','Antibiotic','s01','s01','2026-09-20','2026-09-20','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p003','p003','Paracetamol 500mg','Paracetamol 500mg','180','180','22.00','22.00','35.00','35.00','Painkiller','Painkiller','s01','s01','2027-02-18','2027-02-18','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p004','p004','Ibuprofen 200mg','Ibuprofen 200mg','150','150','35.00','35.00','50.00','50.00','Painkiller','Painkiller','s01','s01','2027-04-12','2027-04-12','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p005','p005','Vitamin C 1000mg','Vitamin C 1000mg','100','100','55.00','55.00','75.00','75.00','Vitamin','Vitamin','s01','s01','2027-01-30','2027-01-30','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p006','p006','Cefuroxime 250mg','Cefuroxime 250mg','70','70','80.00','80.00','105.00','105.00','Antibiotic','Antibiotic','s02','s02','2026-07-10','2026-07-10','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p007','p007','Ciprofloxacin 500mg','Ciprofloxacin 500mg','85','85','75.00','75.00','98.00','98.00','Antibiotic','Antibiotic','s02','s02','2026-11-05','2026-11-05','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p008','p008','Aspirin 100mg','Aspirin 100mg','160','160','28.00','28.00','42.00','42.00','Painkiller','Painkiller','s02','s02','2027-03-15','2027-03-15','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p009','p009','Multivitamin Tablet','Multivitamin Tablet','90','90','95.00','95.00','125.00','125.00','Vitamin','Vitamin','s02','s02','2027-11-20','2027-11-20','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p010','p010','Gaviscon 150ml','Gaviscon 150ml','75','75','125.00','125.00','165.00','165.00','Antacid','Antacid','s02','s02','2026-12-15','2026-12-15','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p011','p011','Benadryl 100ml','Benadryl 100ml','60','60','115.00','115.00','150.00','150.00','Cough Syrup','Cough Syrup','s03','s03','2026-09-15','2026-09-15','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p012','p012','Corex 100ml','Corex 100ml','55','55','110.00','110.00','145.00','145.00','Cough Syrup','Cough Syrup','s03','s03','2026-11-08','2026-11-08','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p013','p013','Dettol 100ml','Dettol 100ml','100','100','70.00','70.00','95.00','95.00','Antiseptic','Antiseptic','s03','s03','2027-06-20','2027-06-20','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p014','p014','Savlon 200ml','Savlon 200ml','85','85','90.00','90.00','120.00','120.00','Antiseptic','Antiseptic','s03','s03','2027-07-15','2027-07-15','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p015','p015','Panadol Extra','Panadol Extra','140','140','32.00','32.00','45.00','45.00','Painkiller','Painkiller','s03','s03','2026-11-25','2026-11-25','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p016','p016','Vitamin D3 1000IU','Vitamin D3 1000IU','80','80','70.00','70.00','95.00','95.00','Vitamin','Vitamin','s04','s04','2028-01-15','2028-01-15','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p017','p017','Zinc Tablet 50mg','Zinc Tablet 50mg','100','100','60.00','60.00','82.00','82.00','Vitamin','Vitamin','s04','s04','2027-10-30','2027-10-30','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p018','p018','Eno Sachet','Eno Sachet','150','150','25.00','25.00','35.00','35.00','Antacid','Antacid','s04','s04','2027-08-20','2027-08-20','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p019','p019','Digene Tablet','Digene Tablet','90','90','40.00','40.00','58.00','58.00','Antacid','Antacid','s04','s04','2027-09-10','2027-09-10','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p020','p020','Erythromycin 500mg','Erythromycin 500mg','70','70','85.00','85.00','110.00','110.00','Antibiotic','Antibiotic','s04','s04','2026-11-22','2026-11-22','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p021','p021','Amoxicillin 500mg','Amoxicillin 500mg','100','100','46.00','46.00','66.00','66.00','Antibiotic','Antibiotic','s05','s05','2026-08-15','2026-08-15','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p022','p022','Ibuprofen 200mg','Ibuprofen 200mg','129','129','33.00','33.00','48.00','48.00','Painkiller','Painkiller','s05','s05','2027-04-20','2027-04-20','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p023','p023','Diclofenac 50mg','Diclofenac 50mg','90','90','40.00','40.00','58.00','58.00','Painkiller','Painkiller','s05','s05','2027-03-25','2027-03-25','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p024','p024','Multivitamin Tablet','Multivitamin Tablet','100','100','90.00','90.00','120.00','120.00','Vitamin','Vitamin','s05','s05','2027-11-20','2027-11-20','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p025','p025','Tixylix 100ml','Tixylix 100ml','50','50','118.00','118.00','155.00','155.00','Cough Syrup','Cough Syrup','s05','s05','2026-09-30','2026-09-30','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p026','p026','Azithromycin 500mg','Azithromycin 500mg','80','80','70.00','70.00','92.00','92.00','Antibiotic','Antibiotic','s06','s06','2025-10-03','2025-10-03','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p027','p027','Dettol 200ml','Dettol 200ml','69','69','130.00','130.00','170.00','170.00','Antiseptic','Antiseptic','s06','s06','2027-05-25','2027-05-25','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p028','p028','Gaviscon 150ml','Gaviscon 150ml','60','60','120.00','120.00','160.00','160.00','Antacid','Antacid','s06','s06','2026-12-15','2026-12-15','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p029','p029','Aspirin 100mg','Aspirin 100mg','120','120','30.00','30.00','45.00','45.00','Painkiller','Painkiller','s06','s06','2026-12-01','2026-12-01','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p030','p030','Vitamin C 1000mg','Vitamin C 1000mg','90','90','54.00','54.00','75.00','75.00','Vitamin','Vitamin','s06','s06','2027-01-30','2027-01-30','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p031','p031','Ciprofloxacin 500mg','Ciprofloxacin 500mg','75','75','74.00','74.00','95.00','95.00','Antibiotic','Antibiotic','s07','s07','2026-11-05','2026-11-05','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p032','p032','Paracetamol 500mg','Paracetamol 500mg','150','150','21.50','21.50','35.00','35.00','Painkiller','Painkiller','s07','s07','2027-03-15','2027-03-15','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p033','p033','Ibuprofen 400mg','Ibuprofen 400mg','100','100','45.00','45.00','65.00','65.00','Painkiller','Painkiller','s07','s07','2027-06-18','2027-06-18','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p034','p034','Digene Tablet','Digene Tablet','79','79','38.00','38.00','55.00','55.00','Antacid','Antacid','s07','s07','2027-09-10','2027-09-10','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p035','p035','Vitamin D3 1000IU','Vitamin D3 1000IU','75','75','68.00','68.00','90.00','90.00','Vitamin','Vitamin','s07','s07','2028-01-15','2028-01-15','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p036','p036','Benadryl 100ml','Benadryl 100ml','55','55','117.00','117.00','155.00','155.00','Cough Syrup','Cough Syrup','s08','s08','2026-10-15','2026-10-15','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p037','p037','Corex 100ml','Corex 100ml','60','60','108.00','108.00','148.00','148.00','Cough Syrup','Cough Syrup','s08','s08','2026-11-08','2026-11-08','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p038','p038','Savlon 200ml','Savlon 200ml','84','84','89.00','89.00','120.00','120.00','Antiseptic','Antiseptic','s08','s08','2027-07-15','2027-07-15','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p039','p039','Panadol Extra','Panadol Extra','130','130','30.00','30.00','45.00','45.00','Painkiller','Painkiller','s08','s08','2026-11-25','2026-11-25','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p040','p040','Zinc Tablet 50mg','Zinc Tablet 50mg','90','90','58.00','58.00','80.00','80.00','Vitamin','Vitamin','s08','s08','2027-10-30','2027-10-30','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p041','p041','Cefuroxime 500mg','Cefuroxime 500mg','60','60','88.00','88.00','115.00','115.00','Antibiotic','Antibiotic','s09','s09','2026-08-25','2026-08-25','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p042','p042','Erythromycin 500mg','Erythromycin 500mg','70','70','84.00','84.00','110.00','110.00','Antibiotic','Antibiotic','s09','s09','2025-11-22','2025-11-22','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p043','p043','Eno Sachet','Eno Sachet','200','200','24.50','24.50','35.00','35.00','Antacid','Antacid','s09','s09','2027-08-20','2027-08-20','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p044','p044','Diclofenac 50mg','Diclofenac 50mg','90','90','39.00','39.00','58.00','58.00','Painkiller','Painkiller','s09','s09','2027-03-25','2027-03-25','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p045','p045','Multivitamin Tablet','Multivitamin Tablet','95','95','92.00','92.00','120.00','120.00','Vitamin','Vitamin','s09','s09','2027-11-20','2027-11-20','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p046','p046','Amoxicillin 500mg','Amoxicillin 500mg','120','120','47.00','47.00','68.00','68.00','Antibiotic','Antibiotic','s10','s10','2026-08-15','2026-08-15','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p047','p047','Cefuroxime 250mg','Cefuroxime 250mg','70','70','82.00','82.00','105.00','105.00','Antibiotic','Antibiotic','s10','s10','2026-07-10','2026-07-10','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p048','p048','Aspirin 100mg','Aspirin 100mg','140','140','29.00','29.00','42.00','42.00','Painkiller','Painkiller','s10','s10','2027-03-15','2027-03-15','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p049','p049','Dettol 100ml','Dettol 100ml','100','100','72.00','72.00','95.00','95.00','Antiseptic','Antiseptic','s10','s10','2027-06-20','2027-06-20','2025-10-23 23:10:32','2025-10-23 23:10:32');
INSERT INTO `product` VALUES ('p050','p050','Vitamin C 1000mg','Vitamin C 1000mg','95','95','56.00','56.00','75.00','75.00','Vitamin','Vitamin','s10','s10','2027-01-30','2027-01-30','2025-10-23 23:10:32','2025-10-23 23:10:32');


-- Table structure for table `purchase_order`
DROP TABLE IF EXISTS `purchase_order`;
CREATE TABLE `purchase_order` (
  `o_id` int(11) NOT NULL AUTO_INCREMENT,
  `s_id` varchar(10) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `price` decimal(10,2) NOT NULL CHECK (`price` >= 0),
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL,
  PRIMARY KEY (`o_id`),
  KEY `s_id` (`s_id`),
  KEY `category_name` (`category_name`),
  CONSTRAINT `purchase_order_ibfk_1` FOREIGN KEY (`s_id`) REFERENCES `supplier_info` (`s_id`) ON UPDATE CASCADE,
  CONSTRAINT `purchase_order_ibfk_2` FOREIGN KEY (`category_name`) REFERENCES `categories` (`category_name`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table structure for table `return_details`
DROP TABLE IF EXISTS `return_details`;
CREATE TABLE `return_details` (
  `return_id` int(11) NOT NULL AUTO_INCREMENT,
  `p_id` varchar(10) NOT NULL,
  `s_id` varchar(10) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `buying_price` decimal(10,2) NOT NULL,
  `return_quantity` int(11) NOT NULL,
  `return_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`return_id`),
  KEY `p_id` (`p_id`),
  KEY `s_id` (`s_id`),
  CONSTRAINT `return_details_ibfk_1` FOREIGN KEY (`p_id`) REFERENCES `product` (`p_id`) ON UPDATE CASCADE,
  CONSTRAINT `return_details_ibfk_2` FOREIGN KEY (`s_id`) REFERENCES `supplier_info` (`s_id`) ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`return_quantity` > 0),
  CONSTRAINT `CONSTRAINT_2` CHECK (`buying_price` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `return_details`
INSERT INTO `return_details` VALUES ('1','1','p001','p001','s01','s01','Amoxicillin 500mg','Amoxicillin 500mg','45.00','45.00','10','10','2025-10-30 10:15:00','2025-10-30 10:15:00');
INSERT INTO `return_details` VALUES ('2','2','p003','p003','s01','s01','Paracetamol 500mg','Paracetamol 500mg','22.00','22.00','12','12','2025-11-01 09:30:00','2025-11-01 09:30:00');
INSERT INTO `return_details` VALUES ('3','3','p021','p021','s05','s05','Amoxicillin 500mg','Amoxicillin 500mg','46.00','46.00','5','5','2025-10-28 14:45:00','2025-10-28 14:45:00');
INSERT INTO `return_details` VALUES ('4','4','p030','p030','s06','s06','Vitamin C 1000mg','Vitamin C 1000mg','54.00','54.00','8','8','2025-10-30 11:00:00','2025-10-30 11:00:00');
INSERT INTO `return_details` VALUES ('5','5','p049','p049','s10','s10','Dettol 100ml','Dettol 100ml','72.00','72.00','20','20','2025-11-03 16:20:00','2025-11-03 16:20:00');


-- Table structure for table `sales`
DROP TABLE IF EXISTS `sales`;
CREATE TABLE `sales` (
  `sales_id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_product_id` varchar(10) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `sale_selling_price` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `pNumber` varchar(10) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `invoice_number` varchar(20) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`sales_id`),
  KEY `sale_product_id` (`sale_product_id`),
  KEY `category_name` (`category_name`),
  KEY `idx_invoice_number` (`invoice_number`),
  CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`sale_product_id`) REFERENCES `product` (`p_id`) ON UPDATE CASCADE,
  CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`category_name`) REFERENCES `categories` (`category_name`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `sales`
INSERT INTO `sales` VALUES ('1','1','p034','p034','Antacid','Antacid','55.00','55.00','43.00','43.00','12.00','12.00','poojani janitha','poojani janitha','0767278456','0767278456','jayarathnahpj23@gmail.com','jayarathnahpj23@gmail.com','INV-2025-10-25-0001','INV-2025-10-25-0001','1','1','2025-10-25 23:04:32','2025-10-25 23:04:32');
INSERT INTO `sales` VALUES ('2','2','p027','p027','Antiseptic','Antiseptic','170.00','170.00','170.00','170.00','0.00','0.00','poojani janitha','poojani janitha','0767278456','0767278456','jayarathnahpj23@gmail.com','jayarathnahpj23@gmail.com','INV-2025-10-25-0002','INV-2025-10-25-0002','1','1','2025-10-25 23:05:25','2025-10-25 23:05:25');
INSERT INTO `sales` VALUES ('3','3','p022','p022','Painkiller','Painkiller','48.00','48.00','48.00','48.00','0.00','0.00','poojani janitha','poojani janitha','0767278456','0767278456','jayarathnahpj23@gmail.com','jayarathnahpj23@gmail.com','INV-2025-10-25-0002','INV-2025-10-25-0002','1','1','2025-10-25 23:05:25','2025-10-25 23:05:25');
INSERT INTO `sales` VALUES ('4','4','p038','p038','Antiseptic','Antiseptic','120.00','120.00','108.00','108.00','12.00','12.00','poojani janitha','poojani janitha','0767278456','0767278456','jayarathnahpj23@gmail.com','jayarathnahpj23@gmail.com','INV-2025-10-25-0003','INV-2025-10-25-0003','1','1','2025-10-25 23:09:42','2025-10-25 23:09:42');


-- Table structure for table `supplier_info`
DROP TABLE IF EXISTS `supplier_info`;
CREATE TABLE `supplier_info` (
  `s_id` varchar(10) NOT NULL,
  `s_name` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`s_id`),
  CONSTRAINT `CONSTRAINT_1` CHECK (`s_id` like 's%')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `supplier_info`
INSERT INTO `supplier_info` VALUES ('s01','s01','Anura Wickramasinghe','Anura Wickramasinghe','Colombo 05, Sri Lanka','Colombo 05, Sri Lanka','0771234567','0771234567','anura.wick@gmail.com','anura.wick@gmail.com');
INSERT INTO `supplier_info` VALUES ('s02','s02','Nimal Perera','Nimal Perera','Kandy, Sri Lanka','Kandy, Sri Lanka','0712345678','0712345678','nimalp@gmail.com','nimalp@gmail.com');
INSERT INTO `supplier_info` VALUES ('s03','s03','Kumari Weerasinghe','Kumari Weerasinghe','Galle, Sri Lanka','Galle, Sri Lanka','0756543210','0756543210','kumariw@hotmail.com','kumariw@hotmail.com');
INSERT INTO `supplier_info` VALUES ('s04','s04','Ranjith Jayasinghe','Ranjith Jayasinghe','Matara, Sri Lanka','Matara, Sri Lanka','0769876543','0769876543','ranjithj@yahoo.com','ranjithj@yahoo.com');
INSERT INTO `supplier_info` VALUES ('s05','s05','Shani Wijesinghe','Shani Wijesinghe','Nuwara Eliya, Sri Lanka','Nuwara Eliya, Sri Lanka','0725554321','0725554321','shani.vw@gmail.com','shani.vw@gmail.com');
INSERT INTO `supplier_info` VALUES ('s06','s06','Lakshan Senevirathna','Lakshan Senevirathna','Anuradhapura, Sri Lanka','Anuradhapura, Sri Lanka','0782345567','0782345567','lakshan.s@gmail.com','lakshan.s@gmail.com');
INSERT INTO `supplier_info` VALUES ('s07','s07','Aruna Dissanayake','Aruna Dissanayake','Kandy, Sri Lanka','Kandy, Sri Lanka','0701122334','0701122334','aruna.d@gmail.com','aruna.d@gmail.com');
INSERT INTO `supplier_info` VALUES ('s08','s08','Champika Kumarasinghe','Champika Kumarasinghe','Galle, Sri Lanka','Galle, Sri Lanka','0719988776','0719988776','champika.k@gmail.com','champika.k@gmail.com');
INSERT INTO `supplier_info` VALUES ('s09','s09','Niluka Perera','Niluka Perera','Kurunegala, Sri Lanka','Kurunegala, Sri Lanka','0753344556','0753344556','niluka.p@gmail.com','niluka.p@gmail.com');
INSERT INTO `supplier_info` VALUES ('s10','s10','Saman Ratnapriya','Saman Ratnapriya','Negombo, Sri Lanka','Negombo, Sri Lanka','0726677889','0726677889','saman.rp@gmail.com','saman.rp@gmail.com');
INSERT INTO `supplier_info` VALUES ('s11','s11','Thilina Jayawardena','Thilina Jayawardena','Colombo 03, Sri Lanka','Colombo 03, Sri Lanka','0779988776','0779988776','thilina.j@gmail.com','thilina.j@gmail.com');
INSERT INTO `supplier_info` VALUES ('s12','s12','Ruwan Abeykoon','Ruwan Abeykoon','Kandy, Sri Lanka','Kandy, Sri Lanka','0714569872','0714569872','ruwana@gmail.com','ruwana@gmail.com');
INSERT INTO `supplier_info` VALUES ('s13','s13','Sanduni Rajapaksha','Sanduni Rajapaksha','Matale, Sri Lanka','Matale, Sri Lanka','0751122443','0751122443','sanduni.r@gmail.com','sanduni.r@gmail.com');
INSERT INTO `supplier_info` VALUES ('s14','s14','Chathura Silva','Chathura Silva','Colombo 10, Sri Lanka','Colombo 10, Sri Lanka','0763344556','0763344556','chathura.s@gmail.com','chathura.s@gmail.com');
INSERT INTO `supplier_info` VALUES ('s15','s15','Gayani Fernando','Gayani Fernando','Panadura, Sri Lanka','Panadura, Sri Lanka','0724433221','0724433221','gayani.f@gmail.com','gayani.f@gmail.com');
INSERT INTO `supplier_info` VALUES ('s16','s16','Kasun Liyanage','Kasun Liyanage','Kalutara, Sri Lanka','Kalutara, Sri Lanka','0772211445','0772211445','kasun.l@gmail.com','kasun.l@gmail.com');
INSERT INTO `supplier_info` VALUES ('s17','s17','Isuru Bandara','Isuru Bandara','Kurunegala, Sri Lanka','Kurunegala, Sri Lanka','0717788990','0717788990','isurub@gmail.com','isurub@gmail.com');
INSERT INTO `supplier_info` VALUES ('s18','s18','Nadeesha Dissanayake','Nadeesha Dissanayake','Colombo 07, Sri Lanka','Colombo 07, Sri Lanka','0786655443','0786655443','nadeesha.d@gmail.com','nadeesha.d@gmail.com');
INSERT INTO `supplier_info` VALUES ('s19','s19','Chatura Perera','Chatura Perera','Ratnapura, Sri Lanka','Ratnapura, Sri Lanka','0759988775','0759988775','chatura.p@gmail.com','chatura.p@gmail.com');
INSERT INTO `supplier_info` VALUES ('s20','s20','Pradeep Senanayake','Pradeep Senanayake','Gampaha, Sri Lanka','Gampaha, Sri Lanka','0768877665','0768877665','pradeep.s@gmail.com','pradeep.s@gmail.com');
INSERT INTO `supplier_info` VALUES ('s21','s21','Madhavi Weerasekara','Madhavi Weerasekara','Kandy, Sri Lanka','Kandy, Sri Lanka','0729988774','0729988774','madhavi.w@gmail.com','madhavi.w@gmail.com');
INSERT INTO `supplier_info` VALUES ('s22','s22','Asela Samarasinghe','Asela Samarasinghe','Matara, Sri Lanka','Matara, Sri Lanka','0776655443','0776655443','asela.s@gmail.com','asela.s@gmail.com');
INSERT INTO `supplier_info` VALUES ('s23','s23','Suresh Rathnayake','Suresh Rathnayake','Colombo 02, Sri Lanka','Colombo 02, Sri Lanka','0715544332','0715544332','suresh.r@gmail.com','suresh.r@gmail.com');
INSERT INTO `supplier_info` VALUES ('s24','s24','Rashmi Perera','Rashmi Perera','Nugegoda, Sri Lanka','Nugegoda, Sri Lanka','0753322110','0753322110','rashmi.p@gmail.com','rashmi.p@gmail.com');
INSERT INTO `supplier_info` VALUES ('s25','s25','Sithara Jayawardana','Sithara Jayawardana','Negombo, Sri Lanka','Negombo, Sri Lanka','0784433221','0784433221','sithara.j@gmail.com','sithara.j@gmail.com');
INSERT INTO `supplier_info` VALUES ('s26','s26','Janith Wickramaratne','Janith Wickramaratne','Kandy, Sri Lanka','Kandy, Sri Lanka','0766677889','0766677889','janith.w@gmail.com','janith.w@gmail.com');
INSERT INTO `supplier_info` VALUES ('s27','s27','Dinusha Abeywardena','Dinusha Abeywardena','Colombo 08, Sri Lanka','Colombo 08, Sri Lanka','0718899776','0718899776','dinusha.a@gmail.com','dinusha.a@gmail.com');
INSERT INTO `supplier_info` VALUES ('s28','s28','Pubudu Silva','Pubudu Silva','Kalutara, Sri Lanka','Kalutara, Sri Lanka','0757766554','0757766554','pubudu.s@gmail.com','pubudu.s@gmail.com');
INSERT INTO `supplier_info` VALUES ('s29','s29','Nirosha Wijeratne','Nirosha Wijeratne','Galle, Sri Lanka','Galle, Sri Lanka','0789988776','0789988776','nirosha.w@gmail.com','nirosha.w@gmail.com');
INSERT INTO `supplier_info` VALUES ('s30','s30','Ruwanthi Fernando','Ruwanthi Fernando','Panadura, Sri Lanka','Panadura, Sri Lanka','0775544331','0775544331','ruwanthi.f@gmail.com','ruwanthi.f@gmail.com');
INSERT INTO `supplier_info` VALUES ('s31','s31','Harsha Karunaratne','Harsha Karunaratne','Colombo 04, Sri Lanka','Colombo 04, Sri Lanka','0726677885','0726677885','harsha.k@gmail.com','harsha.k@gmail.com');
INSERT INTO `supplier_info` VALUES ('s32','s32','Dinuka Samarasinghe','Dinuka Samarasinghe','Kegalle, Sri Lanka','Kegalle, Sri Lanka','0769988774','0769988774','dinuka.s@gmail.com','dinuka.s@gmail.com');
INSERT INTO `supplier_info` VALUES ('s33','s33','Tharindu Perera','Tharindu Perera','Kurunegala, Sri Lanka','Kurunegala, Sri Lanka','0783344552','0783344552','tharindu.p@gmail.com','tharindu.p@gmail.com');
INSERT INTO `supplier_info` VALUES ('s34','s34','Manori Jayasekara','Manori Jayasekara','Gampaha, Sri Lanka','Gampaha, Sri Lanka','0712233445','0712233445','manori.j@gmail.com','manori.j@gmail.com');
INSERT INTO `supplier_info` VALUES ('s35','s35','Pasindu Wickramanayake','Pasindu Wickramanayake','Kandy, Sri Lanka','Kandy, Sri Lanka','0774455667','0774455667','pasindu.w@gmail.com','pasindu.w@gmail.com');
INSERT INTO `supplier_info` VALUES ('s36','s36','Roshani Abeysinghe','Roshani Abeysinghe','Matara, Sri Lanka','Matara, Sri Lanka','0725544331','0725544331','roshani.a@gmail.com','roshani.a@gmail.com');
INSERT INTO `supplier_info` VALUES ('s37','s37','Chandana Bandara','Chandana Bandara','Kurunegala, Sri Lanka','Kurunegala, Sri Lanka','0758899776','0758899776','chandana.b@gmail.com','chandana.b@gmail.com');
INSERT INTO `supplier_info` VALUES ('s38','s38','Amali Senavirathna','Amali Senavirathna','Colombo 09, Sri Lanka','Colombo 09, Sri Lanka','0716655443','0716655443','amali.s@gmail.com','amali.s@gmail.com');
INSERT INTO `supplier_info` VALUES ('s39','s39','Roshan Jayasuriya','Roshan Jayasuriya','Negombo, Sri Lanka','Negombo, Sri Lanka','0787788990','0787788990','roshan.j@gmail.com','roshan.j@gmail.com');
INSERT INTO `supplier_info` VALUES ('s40','s40','Dilusha Perera','Dilusha Perera','Kandy, Sri Lanka','Kandy, Sri Lanka','0764433221','0764433221','dilusha.p@gmail.com','dilusha.p@gmail.com');
INSERT INTO `supplier_info` VALUES ('s41','s41','Sujith Kumara','Sujith Kumara','Galle, Sri Lanka','Galle, Sri Lanka','0776677889','0776677889','sujith.k@gmail.com','sujith.k@gmail.com');
INSERT INTO `supplier_info` VALUES ('s42','s42','Chathurika Rajapaksha','Chathurika Rajapaksha','Matale, Sri Lanka','Matale, Sri Lanka','0759988772','0759988772','chathurika.r@gmail.com','chathurika.r@gmail.com');
INSERT INTO `supplier_info` VALUES ('s43','s43','Rasika Ranasinghe','Rasika Ranasinghe','Colombo 06, Sri Lanka','Colombo 06, Sri Lanka','0715544330','0715544330','rasika.r@gmail.com','rasika.r@gmail.com');
INSERT INTO `supplier_info` VALUES ('s44','s44','Nimesha Dissanayake','Nimesha Dissanayake','Kandy, Sri Lanka','Kandy, Sri Lanka','0786655441','0786655441','nimesha.d@gmail.com','nimesha.d@gmail.com');
INSERT INTO `supplier_info` VALUES ('s45','s45','Pramod Weerakoon','Pramod Weerakoon','Gampaha, Sri Lanka','Gampaha, Sri Lanka','0729988773','0729988773','pramod.w@gmail.com','pramod.w@gmail.com');
INSERT INTO `supplier_info` VALUES ('s46','s46','Ruwangi Silva','Ruwangi Silva','Negombo, Sri Lanka','Negombo, Sri Lanka','0768877661','0768877661','ruwangi.s@gmail.com','ruwangi.s@gmail.com');
INSERT INTO `supplier_info` VALUES ('s47','s47','Supun Karunathilaka','Supun Karunathilaka','Matara, Sri Lanka','Matara, Sri Lanka','0775544335','0775544335','supun.k@gmail.com','supun.k@gmail.com');
INSERT INTO `supplier_info` VALUES ('s48','s48','Gayasha Perera','Gayasha Perera','Kalutara, Sri Lanka','Kalutara, Sri Lanka','0752233441','0752233441','gayasha.p@gmail.com','gayasha.p@gmail.com');
INSERT INTO `supplier_info` VALUES ('s49','s49','Chandima Jayasinghe','Chandima Jayasinghe','Kandy, Sri Lanka','Kandy, Sri Lanka','0717788992','0717788992','chandima.j@gmail.com','chandima.j@gmail.com');
INSERT INTO `supplier_info` VALUES ('s50','s50','Manjula Rathnayake','Manjula Rathnayake','Colombo 01, Sri Lanka','Colombo 01, Sri Lanka','0786655449','0786655449','manjula.r@gmail.com','manjula.r@gmail.com');


-- Table structure for table `supplier_product`
DROP TABLE IF EXISTS `supplier_product`;
CREATE TABLE `supplier_product` (
  `s_id` varchar(10) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`s_id`,`product_name`),
  KEY `category_name` (`category_name`),
  CONSTRAINT `supplier_product_ibfk_1` FOREIGN KEY (`category_name`) REFERENCES `categories` (`category_name`) ON UPDATE CASCADE,
  CONSTRAINT `supplier_product_ibfk_2` FOREIGN KEY (`s_id`) REFERENCES `supplier_info` (`s_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `supplier_product`
INSERT INTO `supplier_product` VALUES ('s01','s01','Antibiotic','Antibiotic','Amoxicillin 500mg','Amoxicillin 500mg','45.00','45.00');
INSERT INTO `supplier_product` VALUES ('s01','s01','Antibiotic','Antibiotic','Azithromycin 250mg','Azithromycin 250mg','65.00','65.00');
INSERT INTO `supplier_product` VALUES ('s01','s01','Painkiller','Painkiller','Ibuprofen 200mg','Ibuprofen 200mg','35.00','35.00');
INSERT INTO `supplier_product` VALUES ('s01','s01','Painkiller','Painkiller','Paracetamol 500mg','Paracetamol 500mg','22.00','22.00');
INSERT INTO `supplier_product` VALUES ('s01','s01','Vitamin','Vitamin','Vitamin C 1000mg','Vitamin C 1000mg','55.00','55.00');
INSERT INTO `supplier_product` VALUES ('s02','s02','Painkiller','Painkiller','Aspirin 100mg','Aspirin 100mg','28.00','28.00');
INSERT INTO `supplier_product` VALUES ('s02','s02','Antibiotic','Antibiotic','Cefuroxime 250mg','Cefuroxime 250mg','80.00','80.00');
INSERT INTO `supplier_product` VALUES ('s02','s02','Antibiotic','Antibiotic','Ciprofloxacin 500mg','Ciprofloxacin 500mg','75.00','75.00');
INSERT INTO `supplier_product` VALUES ('s02','s02','Antacid','Antacid','Gaviscon 150ml','Gaviscon 150ml','125.00','125.00');
INSERT INTO `supplier_product` VALUES ('s02','s02','Vitamin','Vitamin','Multivitamin Tablet','Multivitamin Tablet','95.00','95.00');
INSERT INTO `supplier_product` VALUES ('s03','s03','Cough Syrup','Cough Syrup','Benadryl 100ml','Benadryl 100ml','115.00','115.00');
INSERT INTO `supplier_product` VALUES ('s03','s03','Cough Syrup','Cough Syrup','Corex 100ml','Corex 100ml','110.00','110.00');
INSERT INTO `supplier_product` VALUES ('s03','s03','Antiseptic','Antiseptic','Dettol 100ml','Dettol 100ml','70.00','70.00');
INSERT INTO `supplier_product` VALUES ('s03','s03','Painkiller','Painkiller','Panadol Extra','Panadol Extra','32.00','32.00');
INSERT INTO `supplier_product` VALUES ('s03','s03','Antiseptic','Antiseptic','Savlon 200ml','Savlon 200ml','90.00','90.00');
INSERT INTO `supplier_product` VALUES ('s04','s04','Antacid','Antacid','Digene Tablet','Digene Tablet','40.00','40.00');
INSERT INTO `supplier_product` VALUES ('s04','s04','Antacid','Antacid','Eno Sachet','Eno Sachet','25.00','25.00');
INSERT INTO `supplier_product` VALUES ('s04','s04','Antibiotic','Antibiotic','Erythromycin 500mg','Erythromycin 500mg','85.00','85.00');
INSERT INTO `supplier_product` VALUES ('s04','s04','Vitamin','Vitamin','Vitamin D3 1000IU','Vitamin D3 1000IU','70.00','70.00');
INSERT INTO `supplier_product` VALUES ('s04','s04','Vitamin','Vitamin','Zinc Tablet 50mg','Zinc Tablet 50mg','60.00','60.00');
INSERT INTO `supplier_product` VALUES ('s05','s05','Antibiotic','Antibiotic','Amoxicillin 500mg','Amoxicillin 500mg','46.00','46.00');
INSERT INTO `supplier_product` VALUES ('s05','s05','Painkiller','Painkiller','Diclofenac 50mg','Diclofenac 50mg','40.00','40.00');
INSERT INTO `supplier_product` VALUES ('s05','s05','Painkiller','Painkiller','Ibuprofen 200mg','Ibuprofen 200mg','33.00','33.00');
INSERT INTO `supplier_product` VALUES ('s05','s05','Vitamin','Vitamin','Multivitamin Tablet','Multivitamin Tablet','90.00','90.00');
INSERT INTO `supplier_product` VALUES ('s05','s05','Cough Syrup','Cough Syrup','Tixylix 100ml','Tixylix 100ml','118.00','118.00');
INSERT INTO `supplier_product` VALUES ('s06','s06','Painkiller','Painkiller','Aspirin 100mg','Aspirin 100mg','30.00','30.00');
INSERT INTO `supplier_product` VALUES ('s06','s06','Antibiotic','Antibiotic','Azithromycin 500mg','Azithromycin 500mg','70.00','70.00');
INSERT INTO `supplier_product` VALUES ('s06','s06','Antiseptic','Antiseptic','Dettol 200ml','Dettol 200ml','130.00','130.00');
INSERT INTO `supplier_product` VALUES ('s06','s06','Antacid','Antacid','Gaviscon 150ml','Gaviscon 150ml','120.00','120.00');
INSERT INTO `supplier_product` VALUES ('s06','s06','Vitamin','Vitamin','Vitamin C 1000mg','Vitamin C 1000mg','54.00','54.00');
INSERT INTO `supplier_product` VALUES ('s07','s07','Antibiotic','Antibiotic','Ciprofloxacin 500mg','Ciprofloxacin 500mg','74.00','74.00');
INSERT INTO `supplier_product` VALUES ('s07','s07','Antacid','Antacid','Digene Tablet','Digene Tablet','38.00','38.00');
INSERT INTO `supplier_product` VALUES ('s07','s07','Painkiller','Painkiller','Ibuprofen 400mg','Ibuprofen 400mg','45.00','45.00');
INSERT INTO `supplier_product` VALUES ('s07','s07','Painkiller','Painkiller','Paracetamol 500mg','Paracetamol 500mg','21.50','21.50');
INSERT INTO `supplier_product` VALUES ('s07','s07','Vitamin','Vitamin','Vitamin D3 1000IU','Vitamin D3 1000IU','68.00','68.00');
INSERT INTO `supplier_product` VALUES ('s08','s08','Cough Syrup','Cough Syrup','Benadryl 100ml','Benadryl 100ml','117.00','117.00');
INSERT INTO `supplier_product` VALUES ('s08','s08','Cough Syrup','Cough Syrup','Corex 100ml','Corex 100ml','108.00','108.00');
INSERT INTO `supplier_product` VALUES ('s08','s08','Painkiller','Painkiller','Panadol Extra','Panadol Extra','30.00','30.00');
INSERT INTO `supplier_product` VALUES ('s08','s08','Antiseptic','Antiseptic','Savlon 200ml','Savlon 200ml','89.00','89.00');
INSERT INTO `supplier_product` VALUES ('s08','s08','Vitamin','Vitamin','Zinc Tablet 50mg','Zinc Tablet 50mg','58.00','58.00');
INSERT INTO `supplier_product` VALUES ('s09','s09','Antibiotic','Antibiotic','Cefuroxime 500mg','Cefuroxime 500mg','88.00','88.00');
INSERT INTO `supplier_product` VALUES ('s09','s09','Painkiller','Painkiller','Diclofenac 50mg','Diclofenac 50mg','39.00','39.00');
INSERT INTO `supplier_product` VALUES ('s09','s09','Antacid','Antacid','Eno Sachet','Eno Sachet','24.50','24.50');
INSERT INTO `supplier_product` VALUES ('s09','s09','Antibiotic','Antibiotic','Erythromycin 500mg','Erythromycin 500mg','84.00','84.00');
INSERT INTO `supplier_product` VALUES ('s09','s09','Vitamin','Vitamin','Multivitamin Tablet','Multivitamin Tablet','92.00','92.00');
INSERT INTO `supplier_product` VALUES ('s10','s10','Antibiotic','Antibiotic','Amoxicillin 500mg','Amoxicillin 500mg','47.00','47.00');
INSERT INTO `supplier_product` VALUES ('s10','s10','Painkiller','Painkiller','Aspirin 100mg','Aspirin 100mg','29.00','29.00');
INSERT INTO `supplier_product` VALUES ('s10','s10','Antibiotic','Antibiotic','Cefuroxime 250mg','Cefuroxime 250mg','82.00','82.00');
INSERT INTO `supplier_product` VALUES ('s10','s10','Antiseptic','Antiseptic','Dettol 100ml','Dettol 100ml','72.00','72.00');
INSERT INTO `supplier_product` VALUES ('s10','s10','Vitamin','Vitamin','Vitamin C 1000mg','Vitamin C 1000mg','56.00','56.00');
INSERT INTO `supplier_product` VALUES ('s11','s11','Antibiotic','Antibiotic','Ciprofloxacin 250mg','Ciprofloxacin 250mg','69.00','69.00');
INSERT INTO `supplier_product` VALUES ('s11','s11','Antacid','Antacid','Eno Sachet','Eno Sachet','26.00','26.00');
INSERT INTO `supplier_product` VALUES ('s11','s11','Painkiller','Painkiller','Ibuprofen 400mg','Ibuprofen 400mg','42.00','42.00');
INSERT INTO `supplier_product` VALUES ('s11','s11','Painkiller','Painkiller','Paracetamol 500mg','Paracetamol 500mg','23.00','23.00');
INSERT INTO `supplier_product` VALUES ('s11','s11','Vitamin','Vitamin','Vitamin D3 1000IU','Vitamin D3 1000IU','62.00','62.00');
INSERT INTO `supplier_product` VALUES ('s12','s12','Antibiotic','Antibiotic','Cefixime 200mg','Cefixime 200mg','75.00','75.00');
INSERT INTO `supplier_product` VALUES ('s12','s12','Cough Syrup','Cough Syrup','Corex 100ml','Corex 100ml','109.00','109.00');
INSERT INTO `supplier_product` VALUES ('s12','s12','Antacid','Antacid','Digene Tablet','Digene Tablet','41.00','41.00');
INSERT INTO `supplier_product` VALUES ('s12','s12','Vitamin','Vitamin','Multivitamin Tablet','Multivitamin Tablet','91.00','91.00');
INSERT INTO `supplier_product` VALUES ('s12','s12','Painkiller','Painkiller','Panadol Extra','Panadol Extra','34.00','34.00');
INSERT INTO `supplier_product` VALUES ('s13','s13','Antibiotic','Antibiotic','Amoxicillin 250mg','Amoxicillin 250mg','43.00','43.00');
INSERT INTO `supplier_product` VALUES ('s13','s13','Cough Syrup','Cough Syrup','Benadryl 100ml','Benadryl 100ml','116.00','116.00');
INSERT INTO `supplier_product` VALUES ('s13','s13','Painkiller','Painkiller','Diclofenac 50mg','Diclofenac 50mg','37.00','37.00');
INSERT INTO `supplier_product` VALUES ('s13','s13','Antacid','Antacid','Gaviscon 150ml','Gaviscon 150ml','124.00','124.00');
INSERT INTO `supplier_product` VALUES ('s13','s13','Vitamin','Vitamin','Zinc Tablet 50mg','Zinc Tablet 50mg','59.00','59.00');
INSERT INTO `supplier_product` VALUES ('s14','s14','Painkiller','Painkiller','Aspirin 75mg','Aspirin 75mg','26.00','26.00');
INSERT INTO `supplier_product` VALUES ('s14','s14','Antacid','Antacid','Eno Sachet','Eno Sachet','23.50','23.50');
INSERT INTO `supplier_product` VALUES ('s14','s14','Antibiotic','Antibiotic','Erythromycin 250mg','Erythromycin 250mg','68.00','68.00');
INSERT INTO `supplier_product` VALUES ('s14','s14','Antiseptic','Antiseptic','Savlon 200ml','Savlon 200ml','91.00','91.00');
INSERT INTO `supplier_product` VALUES ('s14','s14','Vitamin','Vitamin','Vitamin C 1000mg','Vitamin C 1000mg','57.00','57.00');
INSERT INTO `supplier_product` VALUES ('s15','s15','Cough Syrup','Cough Syrup','Benadryl 100ml','Benadryl 100ml','112.00','112.00');
INSERT INTO `supplier_product` VALUES ('s15','s15','Antacid','Antacid','Digene Tablet','Digene Tablet','42.00','42.00');
INSERT INTO `supplier_product` VALUES ('s15','s15','Vitamin','Vitamin','Multivitamin Tablet','Multivitamin Tablet','88.00','88.00');
INSERT INTO `supplier_product` VALUES ('s15','s15','Painkiller','Painkiller','Paracetamol 500mg','Paracetamol 500mg','22.50','22.50');
INSERT INTO `supplier_product` VALUES ('s15','s15','Cough Syrup','Cough Syrup','Tixylix 100ml','Tixylix 100ml','119.00','119.00');
INSERT INTO `supplier_product` VALUES ('s16','s16','Antibiotic','Antibiotic','Azithromycin 500mg','Azithromycin 500mg','72.00','72.00');
INSERT INTO `supplier_product` VALUES ('s16','s16','Cough Syrup','Cough Syrup','Corex 100ml','Corex 100ml','111.00','111.00');
INSERT INTO `supplier_product` VALUES ('s16','s16','Antacid','Antacid','Gaviscon 200ml','Gaviscon 200ml','122.00','122.00');
INSERT INTO `supplier_product` VALUES ('s16','s16','Painkiller','Painkiller','Ibuprofen 400mg','Ibuprofen 400mg','46.00','46.00');
INSERT INTO `supplier_product` VALUES ('s16','s16','Vitamin','Vitamin','Vitamin D3 1000IU','Vitamin D3 1000IU','69.00','69.00');
INSERT INTO `supplier_product` VALUES ('s17','s17','Antibiotic','Antibiotic','Cefuroxime 500mg','Cefuroxime 500mg','87.00','87.00');
INSERT INTO `supplier_product` VALUES ('s17','s17','Antacid','Antacid','Eno Sachet','Eno Sachet','25.00','25.00');
INSERT INTO `supplier_product` VALUES ('s17','s17','Painkiller','Painkiller','Panadol Extra','Panadol Extra','33.00','33.00');
INSERT INTO `supplier_product` VALUES ('s17','s17','Cough Syrup','Cough Syrup','Tixylix 100ml','Tixylix 100ml','120.00','120.00');
INSERT INTO `supplier_product` VALUES ('s17','s17','Vitamin','Vitamin','Zinc Tablet 50mg','Zinc Tablet 50mg','61.00','61.00');
INSERT INTO `supplier_product` VALUES ('s18','s18','Antibiotic','Antibiotic','Ciprofloxacin 500mg','Ciprofloxacin 500mg','77.00','77.00');
INSERT INTO `supplier_product` VALUES ('s18','s18','Antiseptic','Antiseptic','Dettol 100ml','Dettol 100ml','74.00','74.00');
INSERT INTO `supplier_product` VALUES ('s18','s18','Antacid','Antacid','Digene Tablet','Digene Tablet','39.50','39.50');
INSERT INTO `supplier_product` VALUES ('s18','s18','Painkiller','Painkiller','Ibuprofen 200mg','Ibuprofen 200mg','36.00','36.00');
INSERT INTO `supplier_product` VALUES ('s18','s18','Vitamin','Vitamin','Vitamin C 1000mg','Vitamin C 1000mg','53.00','53.00');
INSERT INTO `supplier_product` VALUES ('s19','s19','Antibiotic','Antibiotic','Amoxicillin 500mg','Amoxicillin 500mg','45.50','45.50');
INSERT INTO `supplier_product` VALUES ('s19','s19','Painkiller','Painkiller','Aspirin 75mg','Aspirin 75mg','27.00','27.00');
INSERT INTO `supplier_product` VALUES ('s19','s19','Cough Syrup','Cough Syrup','Benadryl 100ml','Benadryl 100ml','113.00','113.00');
INSERT INTO `supplier_product` VALUES ('s19','s19','Antacid','Antacid','Eno Sachet','Eno Sachet','24.00','24.00');
INSERT INTO `supplier_product` VALUES ('s19','s19','Vitamin','Vitamin','Multivitamin Tablet','Multivitamin Tablet','89.00','89.00');
INSERT INTO `supplier_product` VALUES ('s20','s20','Antibiotic','Antibiotic','Cefuroxime 250mg','Cefuroxime 250mg','81.00','81.00');
INSERT INTO `supplier_product` VALUES ('s20','s20','Antibiotic','Antibiotic','Erythromycin 500mg','Erythromycin 500mg','86.00','86.00');
INSERT INTO `supplier_product` VALUES ('s20','s20','Antacid','Antacid','Gaviscon 150ml','Gaviscon 150ml','121.00','121.00');
INSERT INTO `supplier_product` VALUES ('s20','s20','Painkiller','Painkiller','Ibuprofen 400mg','Ibuprofen 400mg','44.00','44.00');
INSERT INTO `supplier_product` VALUES ('s20','s20','Vitamin','Vitamin','Vitamin D3 1000IU','Vitamin D3 1000IU','71.00','71.00');
INSERT INTO `supplier_product` VALUES ('s21','s21','Antibiotic','Antibiotic','Azithromycin 250mg','Azithromycin 250mg','64.00','64.00');
INSERT INTO `supplier_product` VALUES ('s21','s21','Antacid','Antacid','Eno Sachet','Eno Sachet','25.50','25.50');
INSERT INTO `supplier_product` VALUES ('s21','s21','Painkiller','Painkiller','Panadol Extra','Panadol Extra','31.00','31.00');
INSERT INTO `supplier_product` VALUES ('s21','s21','Painkiller','Painkiller','Paracetamol 500mg','Paracetamol 500mg','21.00','21.00');
INSERT INTO `supplier_product` VALUES ('s21','s21','Vitamin','Vitamin','Zinc Tablet 50mg','Zinc Tablet 50mg','60.00','60.00');
INSERT INTO `supplier_product` VALUES ('s22','s22','Cough Syrup','Cough Syrup','Corex 100ml','Corex 100ml','112.00','112.00');
INSERT INTO `supplier_product` VALUES ('s22','s22','Antiseptic','Antiseptic','Dettol 100ml','Dettol 100ml','71.00','71.00');
INSERT INTO `supplier_product` VALUES ('s22','s22','Painkiller','Painkiller','Ibuprofen 200mg','Ibuprofen 200mg','34.00','34.00');
INSERT INTO `supplier_product` VALUES ('s22','s22','Cough Syrup','Cough Syrup','Tixylix 100ml','Tixylix 100ml','120.00','120.00');
INSERT INTO `supplier_product` VALUES ('s22','s22','Vitamin','Vitamin','Vitamin C 1000mg','Vitamin C 1000mg','52.00','52.00');


-- Table structure for table `user_groups`
DROP TABLE IF EXISTS `user_groups`;
CREATE TABLE `user_groups` (
  `id` int(11) NOT NULL,
  `group_name` varchar(150) NOT NULL,
  `group_level` int(11) NOT NULL,
  `group_status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `user_groups`
INSERT INTO `user_groups` VALUES ('1','1','Admin','Admin','1','1','1','1');
INSERT INTO `user_groups` VALUES ('2','2','Staff','Staff','2','2','1','1');
INSERT INTO `user_groups` VALUES ('3','3','finance_DEP','finance_DEP','3','3','1','1');


-- Table structure for table `users`
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_level` int(11) NOT NULL,
  `image` varchar(255) DEFAULT 'no_image.jpg',
  `status` int(1) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `users`
INSERT INTO `users` VALUES ('1','1','Kavindu Perera','Kavindu Perera','admin','admin','d033e22ae348aeb5660fc2140aec35850c4da997','d033e22ae348aeb5660fc2140aec35850c4da997','1','1','no_image.png','no_image.png','1','1','2025-10-25 15:31:11','2025-10-25 15:31:11');
INSERT INTO `users` VALUES ('2','2','Nimesh Lakshan','Nimesh Lakshan','staff_1','staff_1','ba36b97a41e7faf742ab09bf88405ac04f99599a','ba36b97a41e7faf742ab09bf88405ac04f99599a','2','2','no_image.png','no_image.png','1','1','2025-04-04 19:53:26','2025-04-04 19:53:26');
INSERT INTO `users` VALUES ('3','3','Sanduni Madushani','Sanduni Madushani','staff_2','staff_2','12dea96fec20593566ab75692c9949596833adc9','12dea96fec20593566ab75692c9949596833adc9','2','2','no_image.png','no_image.png','1','1','2025-04-04 19:54:46','2025-04-04 19:54:46');
INSERT INTO `users` VALUES ('4','4','Tharushi Senadheera','Tharushi Senadheera','salesman','salesman','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8','3','3','no_image.png','no_image.png','1','1','2025-04-04 19:54:46','2025-04-04 19:54:46');

COMMIT;
