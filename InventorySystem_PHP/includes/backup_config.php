<?php
/*
|--------------------------------------------------------------------------
| Database Backup Configuration
|--------------------------------------------------------------------------
| Configuration settings for database backup functionality
|
*/

// Backup Configuration
define('BACKUP_DIR', 'backups/');
define('BACKUP_MAX_FILES', 10); // Maximum number of backup files to keep
define('BACKUP_COMPRESS', false); // Set to true to compress backup files

// Backup file naming
define('BACKUP_PREFIX', 'backup_');
define('BACKUP_SUFFIX', '.sql');

// Automatic backup settings
define('AUTO_BACKUP_ENABLED', false); // Set to true to enable automatic backups
define('AUTO_BACKUP_INTERVAL', 24); // Hours between automatic backups
define('AUTO_BACKUP_TIME', '02:00'); // Time to run automatic backup (24-hour format)

// Email notification for backups
define('BACKUP_EMAIL_NOTIFICATION', false); // Set to true to email backup notifications
define('BACKUP_EMAIL_RECIPIENT', 'admin@localhost'); // Email to send backup notifications

// Backup file permissions
define('BACKUP_FILE_PERMISSIONS', 0644); // File permissions for backup files
define('BACKUP_DIR_PERMISSIONS', 0755); // Directory permissions for backup directory

// Function to clean old backup files
function cleanOldBackups() {
    $backup_dir = BACKUP_DIR;
    $max_files = BACKUP_MAX_FILES;
    
    if (!is_dir($backup_dir)) {
        return false;
    }
    
    $files = [];
    $file_list = scandir($backup_dir);
    
    foreach ($file_list as $file) {
        if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
            $filepath = $backup_dir . $file;
            $files[] = [
                'filename' => $file,
                'filepath' => $filepath,
                'created' => filemtime($filepath)
            ];
        }
    }
    
    // Sort by creation time (oldest first)
    usort($files, function($a, $b) {
        return $a['created'] - $b['created'];
    });
    
    // Remove oldest files if we exceed the limit
    while (count($files) > $max_files) {
        $oldest_file = array_shift($files);
        if (file_exists($oldest_file['filepath'])) {
            unlink($oldest_file['filepath']);
        }
    }
    
    return true;
}

// Function to get backup statistics
function getBackupStats() {
    $backup_dir = BACKUP_DIR;
    $stats = [
        'total_files' => 0,
        'total_size' => 0,
        'oldest_backup' => null,
        'newest_backup' => null
    ];
    
    if (!is_dir($backup_dir)) {
        return $stats;
    }
    
    $file_list = scandir($backup_dir);
    $files = [];
    
    foreach ($file_list as $file) {
        if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
            $filepath = $backup_dir . $file;
            $file_info = [
                'filename' => $file,
                'filepath' => $filepath,
                'size' => filesize($filepath),
                'created' => filemtime($filepath)
            ];
            $files[] = $file_info;
            
            $stats['total_size'] += $file_info['size'];
        }
    }
    
    $stats['total_files'] = count($files);
    
    if (!empty($files)) {
        // Sort by creation time
        usort($files, function($a, $b) {
            return $a['created'] - $b['created'];
        });
        
        $stats['oldest_backup'] = $files[0]['created'];
        $stats['newest_backup'] = end($files)['created'];
    }
    
    return $stats;
}

?>
