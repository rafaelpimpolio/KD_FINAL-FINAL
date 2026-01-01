<?php
date_default_timezone_set('Asia/Manila');

// Database connection configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'kd_database';   // change only if your DB name is different

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Log file path
$logFile = __DIR__ . '/payment_log.txt';

// Function to write to log file
function writeLog(
    $action,
    $payment_id,
    $transaction_id,
    $payment_reference,
    $details = ''
) {
    global $logFile;

    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] Action: $action | Payment ID: $payment_id | Transaction ID: $transaction_id | Reference: $payment_reference | $details\n";

    // Append to log file
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}
?>
