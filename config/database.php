<?php
// Load application constants
require_once __DIR__ . '/constants.php';

// Establish MySQLi connection
$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for connection errors
if ($connection->connect_errno) {
    die('Database connection failed: ' . $connection->connect_error);
}
