# Return Management System Setup Guide

## Overview
This return management system provides comprehensive functionality for handling product returns, including alerts, auto-expiry detection, supplier integration, and detailed reporting.

## Features Implemented

### 1. Return Processing
- **Return Form**: Staff can add returns with product validation
- **Auto-fill**: JavaScript auto-fills product information when ID is entered
- **Validation**: Prevents returns exceeding available stock
- **Processing**: Approve/reject returns with automatic stock updates

### 2. Return Alerts
- **Frequent Returns**: Alerts when same product returned 3+ times
- **Auto-expiry**: Detects expired medicines and suggests supplier returns
- **Quality Issues**: Tracks return reasons for analytics

### 3. Return Reports & Analytics
- **Monthly Reports**: Total returns per month
- **Most Returned**: Products with highest return rates
- **Reason Analytics**: Top return reasons (Expired, Damaged, Recall)
- **Export Options**: PDF, Excel, CSV formats

### 4. Stock Integration
- **Auto-sync**: Automatically updates stock when returns are processed
- **Real-time**: Stock levels reflect immediately after return processing
- **Validation**: Prevents invalid return quantities

## Database Setup

### 1. Run the Database Schema
Execute the SQL file to create the necessary tables:
```sql
-- Run this file in your MySQL database
InventorySystem_PHP/DATABASE FILE/returns_schema.sql
```

### 2. Tables Created
- `returns` - Main returns table
- `return_alerts` - Alert system
- `suppliers` - Supplier information
- Updated `products` table with expiry dates and supplier links

## File Structure

### New Files Created
```
InventorySystem_PHP/
├── add_return.php              # Return form
├── returns.php                 # Returns management
├── process_return.php          # Return processing
├── return_reports.php          # Reports & analytics
├── auto_expiry_checker.php     # Expiry detection
├── export_returns.php         # Export functionality
└── DATABASE FILE/
    └── returns_schema.sql      # Database schema
```

### Modified Files
```
InventorySystem_PHP/
├── admin.php                   # Added returns dashboard
├── layouts/admin_menu.php      # Added return menu
├── includes/functions.php     # Added return functions
├── ajax.php                   # Added AJAX handlers
└── libs/css/main.css          # Added return styling
```

## Setup Instructions

### 1. Database Setup
1. Import the database schema:
   ```bash
   mysql -u root -p inventory_system < "DATABASE FILE/returns_schema.sql"
   ```

### 2. Configure Suppliers
Add supplier information to the `suppliers` table:
```sql
INSERT INTO suppliers (name, email, phone, address, contact_person, return_policy) 
VALUES ('Your Supplier', 'supplier@email.com', '+1-555-0123', 'Address', 'Contact Person', 'Return Policy');
```

### 3. Update Products
Add expiry dates and supplier links to products:
```sql
UPDATE products SET expiry_date = '2024-12-31', supplier_id = 1 WHERE id = 1;
```

### 4. Set Up Auto-Expiry Checker
Add to your crontab for daily checks:
```bash
# Run daily at 9 AM
0 9 * * * /usr/bin/php /path/to/InventorySystem_PHP/auto_expiry_checker.php
```

## Usage Guide

### 1. Adding a Return
1. Navigate to **Return Management > Add Return**
2. Enter Product ID (auto-fills product info)
3. Select return reason from dropdown
4. Enter quantity and notes
5. Click "Process Return"

### 2. Processing Returns
1. Go to **Return Management > Manage Returns**
2. View pending returns
3. Click "Approve" or "Reject"
4. System automatically updates stock

### 3. Viewing Reports
1. Navigate to **Return Management > Return Reports**
2. View statistics and analytics
3. Export reports in various formats

### 4. Managing Alerts
1. Alerts appear on dashboard and returns page
2. Click "Resolve" to mark alerts as resolved
3. System tracks alert history

## Configuration Options

### 1. Alert Thresholds
Modify in `includes/functions.php`:
```php
// Change frequent return threshold
if($count >= 3) { // Change this number
```

### 2. Expiry Notifications
Configure in `auto_expiry_checker.php`:
```php
$admin_email = "admin@yourcompany.com"; // Set your email
```

### 3. Return Reasons
Add new reasons in `add_return.php`:
```html
<option value="New Reason">New Reason</option>
```

## API Endpoints

### AJAX Endpoints
- `ajax.php?action=get_product_info&id={id}` - Get product info
- `ajax.php?action=get_alerts` - Get active alerts
- `ajax.php?action=resolve_alert&id={id}` - Resolve alert

### Export Endpoints
- `export_returns.php?format=csv` - CSV export
- `export_returns.php?format=excel` - Excel export
- `export_returns.php?format=pdf` - PDF export

## Security Features

### 1. Input Validation
- All inputs are sanitized and validated
- SQL injection protection
- XSS prevention

### 2. Access Control
- User level permissions
- Session management
- Secure redirects

### 3. Data Integrity
- Foreign key constraints
- Transaction handling
- Error logging

## Troubleshooting

### Common Issues

1. **Database Connection Errors**
   - Check database credentials in `includes/config.php`
   - Ensure MySQL service is running

2. **Permission Errors**
   - Check file permissions (755 for directories, 644 for files)
   - Ensure web server can write to uploads directory

3. **AJAX Not Working**
   - Check browser console for JavaScript errors
   - Verify AJAX endpoints are accessible

4. **Auto-expiry Not Running**
   - Check cron job configuration
   - Verify PHP path in cron job
   - Check file permissions

### Debug Mode
Enable debug mode in `includes/database.php`:
```php
// Change this line:
die("Error on this Query :<pre> " . $sql ."</pre>");
```

## Performance Optimization

### 1. Database Indexes
The schema includes optimized indexes for:
- Product lookups
- Date filtering
- Status queries

### 2. Caching
Consider implementing:
- Redis for session storage
- Memcached for query results
- File caching for reports

### 3. Monitoring
Set up monitoring for:
- Database performance
- Alert frequency
- Return patterns

## Future Enhancements

### Planned Features
1. **Email Notifications**: Send alerts via email
2. **SMS Integration**: Text alerts for critical issues
3. **Mobile App**: Return processing on mobile devices
4. **API Integration**: Connect with external systems
5. **Machine Learning**: Predict return patterns

### Integration Options
1. **ERP Systems**: SAP, Oracle integration
2. **E-commerce**: Shopify, WooCommerce
3. **Accounting**: QuickBooks, Xero
4. **CRM**: Salesforce, HubSpot

## Support

For technical support:
1. Check the troubleshooting section
2. Review error logs
3. Test with sample data
4. Contact system administrator

## Version History

### v1.0.0 (Current)
- Initial release
- Basic return management
- Alert system
- Reporting functionality
- Auto-expiry detection

---

**Note**: This system is designed to integrate seamlessly with your existing inventory management system. All modifications are backward compatible and won't affect existing functionality.
