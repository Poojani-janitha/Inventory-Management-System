# Database Connectivity Guide for Return Management System

## 🔧 **Database Connection Setup**

### 1. **Check Database Configuration**

First, verify your database configuration in `includes/config.php`:

```php
define('DB_HOST', 'localhost:3306');    // Database host
define('DB_USER', 'root');             // Database username  
define('DB_PASS', '');                 // Database password
define('DB_NAME', 'inventory_system'); // Database name
```

### 2. **Test Database Connection**

Run the database connection test:
```
http://your-domain/InventorySystem_PHP/test_database_connection.php
```

This will show you:
- ✅ Database connection status
- ✅ Products table structure
- ✅ Available products
- ✅ Function availability

### 3. **Fix Database Issues**

If you encounter issues, run the fix script:
```
http://your-domain/InventorySystem_PHP/fix_database_connection.php
```

This comprehensive test will:
- Check database connection
- Verify table structure
- Test all functions
- Provide specific error messages
- Give recommendations

### 4. **Add Sample Products**

If your products table is empty, run:
```
http://your-domain/InventorySystem_PHP/add_sample_products.php
```

This will add sample products for testing.

## 🗂️ **Database Structure Requirements**

### 1. **Products Table Structure**

Your `products` table should have these columns:

```sql
CREATE TABLE `products` (
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

### 2. **Required Functions**

Make sure these functions are available:
- `find_all('products')` - Fetches all products
- `find_by_id('products', $id)` - Fetches single product
- `$db->query($sql)` - Executes SQL queries
- `$db->while_loop($result)` - Processes query results

## 🔍 **Troubleshooting Common Issues**

### Issue 1: "No products found in database"

**Solution:**
1. Check if products table exists
2. Verify table has data
3. Run the add sample products script
4. Check database permissions

### Issue 2: "Database connection failed"

**Solution:**
1. Verify database server is running
2. Check host, username, password in config.php
3. Ensure database name exists
4. Check firewall settings

### Issue 3: "find_all() function not found"

**Solution:**
1. Check if `sql.php` is included in `load.php`
2. Verify function exists in `includes/sql.php`
3. Check file permissions

### Issue 4: "Dropdown shows no options"

**Solution:**
1. Run database connection test
2. Check if products exist
3. Verify PHP code in add_return.php
4. Check for JavaScript errors

## 📋 **Step-by-Step Setup**

### Step 1: Database Server
```bash
# Start MySQL/MariaDB service
sudo systemctl start mysql
# or
sudo systemctl start mariadb
```

### Step 2: Create Database
```sql
CREATE DATABASE inventory_system;
USE inventory_system;
```

### Step 3: Import Database Schema
```bash
mysql -u root -p inventory_system < "DATABASE FILE/inventory_system.sql"
```

### Step 4: Add Sample Data
```sql
INSERT INTO products (name, quantity, buy_price, sale_price, categorie_id, date) VALUES
('Sample Product 1', '100', '10.00', '15.00', 1, NOW()),
('Sample Product 2', '50', '20.00', '30.00', 1, NOW()),
('Sample Product 3', '75', '5.00', '8.00', 1, NOW());
```

### Step 5: Test Connection
Visit: `http://your-domain/InventorySystem_PHP/test_database_connection.php`

### Step 6: Test Return Form
Visit: `http://your-domain/InventorySystem_PHP/add_return.php`

## 🎯 **Expected Results**

### 1. **Database Connection Test**
- ✅ Database connection successful
- ✅ Products table exists
- ✅ Products found: X products
- ✅ find_all() function works
- ✅ Dropdown data generated

### 2. **Add Return Form**
- ✅ Dropdown shows all products
- ✅ Product selection works
- ✅ Auto-fill functionality works
- ✅ Form submission works

### 3. **Return Processing**
- ✅ Return record created
- ✅ Stock updated
- ✅ Alerts generated (if applicable)

## 🚨 **Common Error Messages**

### "Database connection failed"
- Check MySQL service is running
- Verify credentials in config.php
- Check database name exists

### "No products found in database"
- Run add_sample_products.php
- Check products table exists
- Verify data in products table

### "find_all() function not found"
- Check includes/sql.php is loaded
- Verify function exists
- Check file permissions

### "Dropdown shows no options"
- Check JavaScript console for errors
- Verify PHP generates options
- Test database connection

## 📞 **Support**

If you continue to have issues:

1. **Run the comprehensive test**: `fix_database_connection.php`
2. **Check error logs**: Look in your web server error logs
3. **Verify file permissions**: Ensure PHP can read all files
4. **Test database directly**: Use phpMyAdmin or MySQL command line

## 🔧 **Quick Fix Commands**

### Reset Database Connection
```php
// In config.php, verify these settings:
define('DB_HOST', 'localhost:3306');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'inventory_system');
```

### Add Missing Columns
```sql
ALTER TABLE products 
ADD COLUMN IF NOT EXISTS expiry_date date DEFAULT NULL,
ADD COLUMN IF NOT EXISTS supplier_id int(11) unsigned DEFAULT NULL;
```

### Create Sample Data
```sql
INSERT INTO products (name, quantity, buy_price, sale_price, categorie_id, date) VALUES
('Test Product 1', '100', '10.00', '15.00', 1, NOW()),
('Test Product 2', '50', '20.00', '30.00', 1, NOW());
```

---

**The database connectivity is now properly configured and the dropdown will show all products from your products table!**
