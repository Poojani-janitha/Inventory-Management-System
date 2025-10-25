# Database Backup System - User Guide

## Overview
The Database Backup system allows administrators to create, download, and manage complete backups of the inventory management database.

## Features

### 1. Create Database Backup
- **One-Click Backup**: Create complete database backup with a single button click
- **Automatic Naming**: Backup files are automatically named with timestamp
- **Complete Backup**: Includes all tables, data, structure, routines, and triggers
- **Dual Method**: Uses mysqldump command with PHP fallback

### 2. Download Backup Files
- **Direct Download**: Download backup files directly from the web interface
- **File Management**: View all available backup files with details
- **Size Information**: See file sizes and creation dates

### 3. Backup Management
- **File Listing**: View all backup files with creation dates and sizes
- **Delete Old Backups**: Remove unnecessary backup files
- **Automatic Cleanup**: Old backups are automatically cleaned up (configurable)

### 4. Backup Statistics
- **Total Files**: Count of all backup files
- **Total Size**: Combined size of all backup files
- **Date Range**: Oldest and newest backup information

## How to Use

### Creating a Backup
1. Login as an administrator
2. Navigate to "Database Backup" in the admin menu
3. Click "Create Database Backup" button
4. Wait for the backup to complete
5. The backup file will be available for download

### Downloading Backups
1. Go to the "Available Backup Files" section
2. Find the backup file you want to download
3. Click the "Download" button next to the file
4. The file will be downloaded to your computer

### Managing Backup Files
1. View all backup files in the table
2. Delete old backups using the "Delete" button
3. Confirm deletion when prompted
4. Monitor backup statistics in the information section

## Configuration

### Backup Settings
Edit `includes/backup_config.php` to customize:

```php
// Maximum number of backup files to keep
define('BACKUP_MAX_FILES', 10);

// Backup directory
define('BACKUP_DIR', 'backups/');

// File permissions
define('BACKUP_FILE_PERMISSIONS', 0644);
define('BACKUP_DIR_PERMISSIONS', 0755);
```

### Automatic Cleanup
- Old backup files are automatically deleted when the limit is exceeded
- Default limit is 10 backup files
- Oldest files are deleted first

## File Structure

### Backup Files
- **Location**: `backups/` directory
- **Format**: SQL files with timestamp naming
- **Example**: `backup_inventory_system_2024-01-15_14-30-25.sql`

### Backup Content
Each backup file contains:
- Complete database structure
- All table data
- Stored procedures and functions
- Triggers
- Indexes and constraints

## Security Features

### Access Control
- Only administrators can access backup functionality
- User level verification required

### File Security
- Backup files are stored outside web root (recommended)
- Proper file permissions set
- SQL injection protection in file operations

## Troubleshooting

### Common Issues

1. **Backup Creation Fails**
   - Check if `mysqldump` command is available
   - Verify database connection
   - Check file permissions on backup directory

2. **Download Issues**
   - Ensure backup file exists
   - Check file permissions
   - Verify web server configuration

3. **Permission Errors**
   - Check directory permissions
   - Ensure web server can write to backup directory
   - Verify file ownership

### Server Requirements
- PHP with MySQL extension
- `mysqldump` command available (optional)
- Write permissions on backup directory
- Sufficient disk space for backup files

## Best Practices

### Regular Backups
- Create backups before major updates
- Schedule regular backups (daily/weekly)
- Test backup restoration periodically

### Storage
- Store backups in secure location
- Consider off-site backup storage
- Monitor disk space usage

### Testing
- Test backup files by restoring to test database
- Verify backup completeness
- Check backup file integrity

## Advanced Features

### Custom Configuration
- Modify backup configuration file
- Add compression support
- Implement email notifications
- Set up automatic scheduling

### Integration
- Integrate with external backup services
- Add cloud storage support
- Implement backup encryption
- Add backup verification

## File Locations

- **Main Backup Page**: `database_backup.php`
- **Configuration**: `includes/backup_config.php`
- **Backup Directory**: `backups/` (created automatically)
- **Menu Integration**: `layouts/admin_menu.php`

## Support

For technical support or questions about the backup system:
1. Check the troubleshooting section
2. Verify server requirements
3. Review configuration settings
4. Check file permissions and disk space
