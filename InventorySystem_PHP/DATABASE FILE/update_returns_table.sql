-- Update existing returns table to add new columns
-- Run this if you already have a returns table

-- Add new columns to returns table
ALTER TABLE `returns` 
ADD COLUMN IF NOT EXISTS `product_name` varchar(255) NOT NULL AFTER `refund_amount`,
ADD COLUMN IF NOT EXISTS `sale_price` decimal(25,2) NOT NULL AFTER `product_name`,
ADD COLUMN IF NOT EXISTS `buying_price` decimal(25,2) NOT NULL AFTER `sale_price`,
ADD COLUMN IF NOT EXISTS `supplier_id` int(11) unsigned DEFAULT NULL AFTER `buying_price`,
ADD COLUMN IF NOT EXISTS `supplier_name` varchar(255) DEFAULT NULL AFTER `supplier_id`;

-- Add foreign key constraint for supplier_id
ALTER TABLE `returns` 
ADD CONSTRAINT IF NOT EXISTS `FK_returns_supplier` 
FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Add index for supplier_id
ALTER TABLE `returns` 
ADD INDEX IF NOT EXISTS `supplier_id` (`supplier_id`);

-- Update existing returns with product information (if any exist)
UPDATE `returns` r 
LEFT JOIN `products` p ON r.product_id = p.id 
LEFT JOIN `suppliers` s ON p.supplier_id = s.id
SET 
  r.product_name = p.name,
  r.sale_price = p.sale_price,
  r.buying_price = p.buy_price,
  r.supplier_id = p.supplier_id,
  r.supplier_name = s.name
WHERE r.product_name = '' OR r.product_name IS NULL;
