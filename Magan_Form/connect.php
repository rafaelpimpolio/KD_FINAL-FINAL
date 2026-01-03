<?php
date_default_timezone_set('Asia/Manila');

// Database connection configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'kd_sportswear';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Log file path
$logFile = __DIR__ . '/log.txt';

// Function to write to log file
function writeLog($action, $employee_id, $first_name, $last_name, $details = '') {
    global $logFile;

    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] Action: $action | Employee ID: $employee_id | Name: $first_name $last_name | $details\n";

    // Append to log file
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}
?>