-- --------------------------------------------------------
--
-- Table structure for table `suppliers`
--

CREATE TABLE IF NOT EXISTS `suppliers` (
`id` int(11) unsigned NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `email` varchar(100) NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `categorie_id` int(11) unsigned NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_name`, `contact_number`, `address`, `email`, `product_id`, `categorie_id`) VALUES
(1, 'ABC Suppliers Ltd', '+94771234567', '123 Main Street, Colombo', 'abc@suppliers.com', 1, 1),
(2, 'Global Box Co', '+94777654321', '456 Industrial Zone, Gampaha', 'info@globalbox.com', 2, 4),
(3, 'Grain Masters', '+94112345678', '789 Harvest Road, Kandy', 'sales@grainmasters.com', 3, 2),
(4, 'Timber Traders', '+94713456789', '321 Wood Lane, Matara', 'contact@timbertraders.com', 4, 2),
(5, 'Machinery World', '+94769876543', '654 Tech Park, Colombo', 'orders@machineryworld.com', 5, 5),
(6, 'Tool Suppliers Inc', '+94775556666', '987 Equipment Street, Negombo', 'info@toolsuppliers.com', 6, 5);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
`id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `supplier_id` int(11) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
 ADD PRIMARY KEY (`id`), ADD KEY `product_id` (`product_id`), ADD KEY `categorie_id` (`categorie_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
 ADD PRIMARY KEY (`id`), ADD KEY `product_id` (`product_id`), ADD KEY `supplier_id` (`supplier_id`);

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
ADD CONSTRAINT `FK_suppliers_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `FK_suppliers_categories` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
ADD CONSTRAINT `FK_orders_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `FK_orders_suppliers` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;