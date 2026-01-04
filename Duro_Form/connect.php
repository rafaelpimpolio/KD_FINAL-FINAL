<?php
date_default_timezone_set('Asia/Manila');


$host = 'localhost';
$username = 'root';
$password = '';
$database = 'kd_sportswear';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$logFile = __DIR__ . '/payment_log.txt';


function writeLog(
    $action,
    $payment_id = '',
    $method_of_payment = '',
    $total_amount = '',
    $downpayment = '',
    $balance = '',
    $status = '',
    $customer_id = '',
    $order_id = '',
    $employee_id = '',
    $details = ''
) {
    global $logFile;

    $timestamp = date('Y-m-d H:i:s');

    $logEntry = "[$timestamp] Action: $action | Payment ID: $payment_id | Method: $method_of_payment | Total: $total_amount | Downpayment: $downpayment | Balance: $balance | Status: $status | Customer ID: $customer_id | Order ID: $order_id | Employee ID: $employee_id | Details: $details\n";

    // Append to log file
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}
?>
