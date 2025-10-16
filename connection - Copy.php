<?php
// connection.php — central DB connection (MySQLi)
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';              // XAMPP default
$DB_NAME = 'asset_mgmt';    // create this DB in phpMyAdmin

$conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
  die('Database connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
