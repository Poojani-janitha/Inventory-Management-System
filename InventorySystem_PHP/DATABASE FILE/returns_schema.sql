-- Returns Management System Database Schema
-- This file contains the database schema for the return management system

-- Create returns table
CREATE TABLE IF NOT EXISTS `returns` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `return_reason` enum('Expired','Damaged','Customer Mistake','Defective','Wrong Item','Quality Issue','Recall','Other') NOT NULL,
  `return_date` datetime NOT NULL,
  `processed_by` int(11) unsigned NOT NULL,
  `status` enum('Pending','Approved','Rejected','Processed') DEFAULT 'Pending',
  `notes` text,
  `supplier_email` varchar(255) DEFAULT NULL,
  `refund_amount` decimal(25,2) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `sale_price` decimal(25,2) NOT NULL,
  `buying_price` decimal(25,2) NOT NULL,
  `supplier_id` int(11) unsigned DEFAULT NULL,
  `supplier_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `processed_by` (`processed_by`),
  KEY `return_date` (`return_date`),
  KEY `status` (`status`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `FK_returns_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_returns_user` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_returns_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create return alerts table for frequent returns tracking
CREATE TABLE IF NOT EXISTS `return_alerts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `alert_type` enum('Frequent Returns','Expiry Alert','Quality Issue','Recall Alert') NOT NULL,
  `alert_message` text NOT NULL,
  `alert_date` datetime NOT NULL,
  `is_resolved` tinyint(1) DEFAULT 0,
  `resolved_by` int(11) unsigned DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `alert_type` (`alert_type`),
  KEY `is_resolved` (`is_resolved`),
  CONSTRAINT `FK_alerts_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_alerts_user` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create suppliers table for return management
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text,
  `contact_person` varchar(255) DEFAULT NULL,
  `return_policy` text,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Add expiry date column to products table if it doesn't exist
ALTER TABLE `products` 
ADD COLUMN IF NOT EXISTS `expiry_date` date DEFAULT NULL AFTER `sale_price`,
ADD COLUMN IF NOT EXISTS `supplier_id` int(11) unsigned DEFAULT NULL AFTER `expiry_date`,
ADD KEY IF NOT EXISTS `supplier_id` (`supplier_id`),
ADD CONSTRAINT IF NOT EXISTS `FK_products_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Insert sample suppliers
INSERT INTO `suppliers` (`name`, `email`, `phone`, `address`, `contact_person`, `return_policy`) VALUES
('MedSupply Co.', 'returns@medsupply.com', '+1-555-0123', '123 Medical Ave, Health City', 'John Smith', '30-day return policy for expired products'),
('PharmaDirect', 'support@pharmadirect.com', '+1-555-0456', '456 Pharma Street, Drug Town', 'Sarah Johnson', '15-day return policy for damaged goods'),
('HealthCorp', 'info@healthcorp.com', '+1-555-0789', '789 Wellness Blvd, Care City', 'Mike Davis', '45-day return policy for quality issues');

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_returns_product_date` ON `returns` (`product_id`, `return_date`);
CREATE INDEX IF NOT EXISTS `idx_returns_status_date` ON `returns` (`status`, `return_date`);
CREATE INDEX IF NOT EXISTS `idx_alerts_product_type` ON `return_alerts` (`product_id`, `alert_type`);
CREATE INDEX IF NOT EXISTS `idx_products_expiry` ON `products` (`expiry_date`);
