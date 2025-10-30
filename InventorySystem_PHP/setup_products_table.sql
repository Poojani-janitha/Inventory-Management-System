-- Setup script for products table with all necessary columns
-- Run this if you need to add missing columns to the products table

-- Check if products table exists, if not create it
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `quantity` varchar(50) DEFAULT NULL,
  `buy_price` decimal(25,2) DEFAULT NULL,
  `sale_price` decimal(25,2) NOT NULL,
  `categorie_id` int(11) unsigned NOT NULL,
  `media_id` int(11) DEFAULT '0',
  `date` datetime NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `supplier_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `categorie_id` (`categorie_id`),
  KEY `media_id` (`media_id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Add missing columns if they don't exist
ALTER TABLE `products` 
ADD COLUMN IF NOT EXISTS `expiry_date` date DEFAULT NULL AFTER `sale_price`,
ADD COLUMN IF NOT EXISTS `supplier_id` int(11) unsigned DEFAULT NULL AFTER `expiry_date`;

-- Add foreign key constraint for supplier_id if suppliers table exists
-- ALTER TABLE `products` 
-- ADD CONSTRAINT IF NOT EXISTS `FK_products_supplier` 
-- FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Insert sample products if table is empty
INSERT IGNORE INTO `products` (`name`, `quantity`, `buy_price`, `sale_price`, `categorie_id`, `date`) VALUES
('Sample Product 1', '100', '10.00', '15.00', 1, NOW()),
('Sample Product 2', '50', '20.00', '30.00', 1, NOW()),
('Sample Product 3', '75', '5.00', '8.00', 1, NOW());

-- Update products with sample supplier IDs if suppliers table exists
-- UPDATE `products` SET `supplier_id` = 1 WHERE `id` = 1;
-- UPDATE `products` SET `supplier_id` = 2 WHERE `id` = 2;
-- UPDATE `products` SET `supplier_id` = 1 WHERE `id` = 3;
