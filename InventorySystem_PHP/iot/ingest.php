<?php
// Simple ingestion endpoint for ESP8266 DHT11 readings
// Accepts GET (or JSON) payloads: temp (Celsius), hum (percent)
// Example: /iot/ingest.php?temp=25.4&hum=60.2

require_once(dirname(__FILE__) . '/../includes/load.php');

header('Content-Type: application/json');

// Allow both query params and JSON body
$raw = file_get_contents('php://input');
$json = null;
if ($raw) {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $json = $decoded;
    }
}

$temp = null;
$hum = null;

if (isset($_GET['temp'])) {
    $temp = $_GET['temp'];
}
if (isset($_GET['hum'])) {
    $hum = $_GET['hum'];
}

if ($json) {
    if ($temp === null && isset($json['temp'])) $temp = $json['temp'];
    if ($hum === null && isset($json['hum'])) $hum = $json['hum'];
}

// Basic validation
if ($temp === null || $hum === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing temp or hum']);
    exit;
}

$temp = floatval($temp);
$hum = floatval($hum);

// Insert into database
global $db;

// Ensure table exists (lightweight check)
$db->query("CREATE TABLE IF NOT EXISTS dht11_readings (\n  id INT(11) NOT NULL AUTO_INCREMENT,\n  temperature FLOAT NULL,\n  humidity FLOAT NULL,\n  datetime DATETIME NULL DEFAULT CURRENT_TIMESTAMP,\n  PRIMARY KEY (id)\n) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$sql  = "INSERT INTO dht11_readings (temperature, humidity) VALUES (";
$sql .= "'" . $db->escape($temp) . "', '" . $db->escape($hum) . "')";
$db->query($sql);

echo json_encode(['success' => true, 'temp' => $temp, 'hum' => $hum]);

?>


