<?php
// inc/config.php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = 'Khushi@1498'; // set your MySQL password
$DB_NAME = 'inventory_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS,$DB_NAME);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
