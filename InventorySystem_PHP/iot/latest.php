<?php
require_once(dirname(__FILE__) . '/../includes/load.php');
header('Content-Type: application/json');

global $db;

// Ensure table exists for first-time use
$db->query("CREATE TABLE IF NOT EXISTS dht11_readings (\n  id INT(11) NOT NULL AUTO_INCREMENT,\n  temperature FLOAT NULL,\n  humidity FLOAT NULL,\n  datetime DATETIME NULL DEFAULT CURRENT_TIMESTAMP,\n  PRIMARY KEY (id)\n) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$res = $db->query("SELECT id, temperature, humidity, datetime FROM dht11_readings ORDER BY id DESC LIMIT 1");
$row = $db->fetch_assoc($res);

echo json_encode(['success' => true, 'row' => $row]);
?>


