<?php
require "dbconnect.php";

$pdo = Database::Connection();
Database::WritePost($_POST);

$func_name = $_POST['func_name'] ?? '';

if ($func_name && function_exists($func_name)) {
    echo call_user_func($func_name);
} else {
    $msg = "Function '" . $func_name . "' not found";
    Database::WriteLog($msg);
    echo json_encode($msg);
}

/* ---------- CRUD FUNCTIONS ---------- */

function DisplayPayment() {
    $pdo = $GLOBALS['pdo'];
    $sql = "SELECT * FROM payment ORDER BY payment_id DESC";
    return json_encode(Database::GetAllData($pdo, $sql));
}

function AddPayment() {
    $pdo = $GLOBALS['pdo'];

    $sql = "INSERT INTO payment (
                transaction_id,
                payment_reference,
                transaction_status,
                method_payment,
                payment_date,
                amount,
                balance
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $params = [
        $_POST['transactionID'] ?? '',
        $_POST['paymentReference'] ?? '',
        $_POST['transactionStatus'] ?? '',
        $_POST['methodPayment'] ?? '',
        $_POST['paymentDate'] ?? '',
        $_POST['amount'] ?? 0,
        $_POST['balance'] ?? 0
    ];

    try {
        Database::ManageRecord($pdo, $sql, $params);
        return json_encode("Successfully Inserted");
    } catch (Exception $e) {
        Database::WriteLog("AddPayment Error: " . $e->getMessage());
        return json_encode("Error inserting record: " . $e->getMessage());
    }
}

function UpdatePayment() {
    $pdo = $GLOBALS['pdo'];

    $sql = "UPDATE payment SET
                transaction_id = ?,
                payment_reference = ?,
                transaction_status = ?,
                method_payment = ?,
                payment_date = ?,
                amount = ?,
                balance = ?
            WHERE payment_id = ?";

    $params = [
        $_POST['transactionID'] ?? '',
        $_POST['paymentReference'] ?? '',
        $_POST['transactionStatus'] ?? '',
        $_POST['methodPayment'] ?? '',
        $_POST['paymentDate'] ?? '',
        $_POST['amount'] ?? 0,
        $_POST['balance'] ?? 0,
        $_POST['paymentID'] ?? 0
    ];

    try {
        Database::ManageRecord($pdo, $sql, $params);
        return json_encode("Successfully Updated");
    } catch (Exception $e) {
        Database::WriteLog("UpdatePayment Error: " . $e->getMessage());
        return json_encode("Error updating record: " . $e->getMessage());
    }
}

function DeletePayment() {
    $pdo = $GLOBALS['pdo'];
    $id = $_POST['paymentID'] ?? 0;

    $sql = "DELETE FROM payment WHERE payment_id = ?";
    try {
        Database::ManageRecord($pdo, $sql, [$id]);
        return json_encode("Successfully Deleted");
    } catch (Exception $e) {
        Database::WriteLog("DeletePayment Error: " . $e->getMessage());
        return json_encode("Error deleting record: " . $e->getMessage());
    }
}
?>
