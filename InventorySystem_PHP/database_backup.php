<?php
  $page_title = 'Database Backup';
  require_once('includes/load.php');
  require_once('includes/backup_config.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);
   //extra add prabashi 
  $msg = $session->msg();
?>

<?php
// Database backup functions
function createDatabaseBackup() {
    global $db;
    
    // Get database configuration
    $host = DB_HOST;
    $username = DB_USER;
    $password = DB_PASS;
    $database = DB_NAME;
    
    // Create backup filename with timestamp
    $timestamp = date('Y-m-d_H-i-s');
    $filename = "backup_{$database}_{$timestamp}.sql";
    
    // Set the path for backup files
    $backup_dir = BACKUP_DIR;
    if (!file_exists($backup_dir)) {
        mkdir($backup_dir, BACKUP_DIR_PERMISSIONS, true);
    }
    
    $filepath = $backup_dir . $filename;
    
    // Create mysqldump command
    $command = "mysqldump --host={$host} --user={$username}";
    if (!empty($password)) {
        $command .= " --password={$password}";
    }
    $command .= " --single-transaction --routines --triggers {$database} > {$filepath}";
    
    // Execute backup command
    $output = [];
    $return_var = 0;
    exec($command, $output, $return_var);
    
    if ($return_var === 0 && file_exists($filepath) && filesize($filepath) > 0) {
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'size' => filesize($filepath)
        ];
    } else {
        // Fallback: Create backup using PHP
        return createBackupWithPHP($filepath);
    }
}

function createBackupWithPHP($filepath) {
    global $db;
    
    $backup_content = "-- Database Backup\n";
    $backup_content .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
    $backup_content .= "-- Database: " . DB_NAME . "\n\n";
    $backup_content .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $backup_content .= "SET AUTOCOMMIT = 0;\n";
    $backup_content .= "START TRANSACTION;\n";
    $backup_content .= "SET time_zone = \"+00:00\";\n\n";
    
    // Get all tables
    $tables_query = "SHOW TABLES";
    $tables_result = $db->query($tables_query);
    $tables = [];
    
    while ($row = $db->fetch_array($tables_result)) {
        $tables[] = $row[0];
    }
    
    foreach ($tables as $table) {
        $backup_content .= "\n-- Table structure for table `{$table}`\n";
        $backup_content .= "DROP TABLE IF EXISTS `{$table}`;\n";
        
        // Get table structure
        $create_query = "SHOW CREATE TABLE `{$table}`";
        $create_result = $db->query($create_query);
        $create_row = $db->fetch_array($create_result);
        $backup_content .= $create_row[1] . ";\n\n";
        
        // Get table data
        $data_query = "SELECT * FROM `{$table}`";
        $data_result = $db->query($data_query);
        
        if ($db->num_rows($data_result) > 0) {
            $backup_content .= "-- Dumping data for table `{$table}`\n";
            
            while ($row = $db->fetch_array($data_result)) {
                $values = [];
                foreach ($row as $key => $value) {
                    if ($value === null) {
                        $values[] = 'NULL';
                    } else {
                        $values[] = "'" . $db->escape($value) . "'";
                    }
                }
                $backup_content .= "INSERT INTO `{$table}` VALUES (" . implode(',', $values) . ");\n";
            }
            $backup_content .= "\n";
        }
    }
    
    $backup_content .= "COMMIT;\n";
    
    // Write backup to file
    if (file_put_contents($filepath, $backup_content)) {
        return [
            'success' => true,
            'filename' => basename($filepath),
            'filepath' => $filepath,
            'size' => filesize($filepath)
        ];
    } else {
        return [
            'success' => false,
            'error' => 'Failed to write backup file'
        ];
    }
}

function getBackupFiles() {
    $backup_dir = BACKUP_DIR;
    $files = [];
    
    if (is_dir($backup_dir)) {
        $file_list = scandir($backup_dir);
        foreach ($file_list as $file) {
            if ($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
                $filepath = $backup_dir . $file;
                $files[] = [
                    'filename' => $file,
                    'filepath' => $filepath,
                    'size' => filesize($filepath),
                    'created' => filemtime($filepath)
                ];
            }
        }
        
        // Sort by creation time (newest first)
        usort($files, function($a, $b) {
            return $b['created'] - $a['created'];
        });
    }
    
    return $files;
}

function deleteBackupFile($filename) {
    $backup_dir = BACKUP_DIR;
    $filepath = $backup_dir . $filename;
    
    if (file_exists($filepath) && pathinfo($filename, PATHINFO_EXTENSION) == 'sql') {
        return unlink($filepath);
    }
    return false;
}

// Handle form submissions
if (isset($_POST['create_backup'])) {
    $backup_result = createDatabaseBackup();
    
    if ($backup_result['success']) {
        // Clean old backups after successful creation
        cleanOldBackups();
        
        $session->msg('s', 'Database backup created successfully: ' . $backup_result['filename']);
    } else {
        $session->msg('d', 'Failed to create database backup: ' . ($backup_result['error'] ?? 'Unknown error'));
    }
    redirect('database_backup.php', false);
}

if (isset($_POST['download_backup'])) {
    $filename = $db->escape($_POST['backup_filename']);
    $backup_dir = BACKUP_DIR;
    $filepath = $backup_dir . $filename;
    
    if (file_exists($filepath) && pathinfo($filename, PATHINFO_EXTENSION) == 'sql') {
        // Set headers for download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        
        // Output file
        readfile($filepath);
        exit;
    } else {
        $session->msg('d', 'Backup file not found.');
        redirect('database_backup.php', false);
    }
}

if (isset($_POST['delete_backup'])) {
    $filename = $db->escape($_POST['backup_filename']);
    
    if (deleteBackupFile($filename)) {
        $session->msg('s', 'Backup file deleted successfully: ' . $filename);
    } else {
        $session->msg('d', 'Failed to delete backup file.');
    }
    redirect('database_backup.php', false);
}

// Get backup files for display
$backup_files = getBackupFiles();
$backup_stats = getBackupStats();
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
   <div class="col-md-12">
     <?php echo display_msg($msg); ?>
   </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-download-alt"></span>
          <span>Database Backup Management</span>
        </strong>
      </div>
      <div class="panel-body">
        
        <!-- Create Backup Section -->
        <div class="row">
          <div class="col-md-12">
            <h4><span class="glyphicon glyphicon-plus"></span> Create New Backup</h4>
            <p>Create a complete backup of your database including all tables, data, and structure.</p>
            
            <form method="post" action="database_backup.php" class="clearfix">
              <div class="form-group">
                <button type="submit" name="create_backup" class="btn btn-primary btn-lg">
                  <span class="glyphicon glyphicon-download-alt"></span> Create Database Backup
                </button>
                <small class="help-block">This will create a downloadable SQL file with your complete database.</small>
              </div>
            </form>
          </div>
        </div>

        <hr>

        <!-- Backup Files Section -->
        <div class="row">
          <div class="col-md-12">
            <h4><span class="glyphicon glyphicon-folder-open"></span> Available Backup Files</h4>
            
            <?php if (!empty($backup_files)): ?>
              <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed">
                  <thead>
                    <tr>
                      <th>Filename</th>
                      <th>Size</th>
                      <th>Created</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($backup_files as $file): ?>
                      <tr>
                        <td>
                          <span class="glyphicon glyphicon-file"></span>
                          <?php echo htmlspecialchars($file['filename']); ?>
                        </td>
                        <td>
                          <span class="badge"><?php echo formatBytes($file['size']); ?></span>
                        </td>
                        <td>
                          <?php echo date('M j, Y g:i A', $file['created']); ?>
                        </td>
                        <td>
                          <form method="post" action="database_backup.php" style="display: inline;">
                            <input type="hidden" name="backup_filename" value="<?php echo htmlspecialchars($file['filename']); ?>">
                            <button type="submit" name="download_backup" class="btn btn-sm btn-success">
                              <span class="glyphicon glyphicon-download"></span> Download
                            </button>
                          </form>
                          
                          <form method="post" action="database_backup.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this backup file?');">
                            <input type="hidden" name="backup_filename" value="<?php echo htmlspecialchars($file['filename']); ?>">
                            <button type="submit" name="delete_backup" class="btn btn-sm btn-danger">
                              <span class="glyphicon glyphicon-trash"></span> Delete
                            </button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <div class="alert alert-info">
                <span class="glyphicon glyphicon-info-sign"></span>
                No backup files found. Create your first backup using the button above.
              </div>
            <?php endif; ?>
          </div>
        </div>

        <hr>

        <!-- Backup Information Section -->
        <div class="row">
          <div class="col-md-12">
            <h4><span class="glyphicon glyphicon-info-sign"></span> Backup Information</h4>
            <div class="alert alert-warning">
              <strong>Important Notes:</strong>
              <ul>
                <li>Backup files are stored in the <code>backups/</code> directory</li>
                <li>Each backup includes complete database structure and data</li>
                <li>Backup files are named with timestamp for easy identification</li>
                <li>Regular backups are recommended for data safety</li>
                <li>Keep backup files in a secure location</li>
              </ul>
            </div>
            
            <div class="alert alert-info">
              <strong>Database Information:</strong>
              <ul>
                <li><strong>Database Name:</strong> <?php echo DB_NAME; ?></li>
                <li><strong>Host:</strong> <?php echo DB_HOST; ?></li>
                <li><strong>Total Tables:</strong> <?php 
                  $tables_query = "SHOW TABLES";
                  $tables_result = $db->query($tables_query);
                  echo $db->num_rows($tables_result);
                ?></li>
                <li><strong>Last Backup:</strong> <?php 
                  if (!empty($backup_files)) {
                    echo date('M j, Y g:i A', $backup_files[0]['created']);
                  } else {
                    echo 'Never';
                  }
                ?></li>
              </ul>
            </div>
            
            <div class="alert alert-success">
              <strong>Backup Statistics:</strong>
              <ul>
                <li><strong>Total Backup Files:</strong> <?php echo $backup_stats['total_files']; ?></li>
                <li><strong>Total Backup Size:</strong> <?php echo formatBytes($backup_stats['total_size']); ?></li>
                <li><strong>Oldest Backup:</strong> <?php 
                  if ($backup_stats['oldest_backup']) {
                    echo date('M j, Y g:i A', $backup_stats['oldest_backup']);
                  } else {
                    echo 'None';
                  }
                ?></li>
                <li><strong>Newest Backup:</strong> <?php 
                  if ($backup_stats['newest_backup']) {
                    echo date('M j, Y g:i A', $backup_stats['newest_backup']);
                  } else {
                    echo 'None';
                  }
                ?></li>
              </ul>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<style>
.badge {
  background-color: #337ab7;
}
.help-block {
  color: #666;
  font-size: 12px;
}
</style>

<?php
// Helper function to format file sizes
function formatBytes($size, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    
    return round($size, $precision) . ' ' . $units[$i];
}
?>

<?php include_once('layouts/footer.php'); ?>
