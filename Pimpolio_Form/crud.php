<?php
require "connect.php";

$pdo = Database::Connection();
Database::WritePost($_POST);

$func_name = $_POST['func_name'] ?? '';

// Only allow known functions
$allowedFunctions = ['DeleteCustomer'];

if (in_array($func_name, $allowedFunctions)) {
    echo call_user_func($func_name);
} else {
    $msg = "Function '" . $func_name . "' not allowed";
    Database::WriteLog($msg);
    echo json_encode(["error" => $msg]);
}

// -----------------------------
// DELETE CUSTOMER
// -----------------------------
function DeleteCustomer()
{
    $pdo = Database::Connection();
    $id = $_POST['customerID'] ?? 0;

    if (!$id) {
        return json_encode(["error" => "Invalid customer ID"]);
    }

    $sql = "DELETE FROM customer WHERE customer_id = ?";

    try {
        Database::ManageRecord($pdo, $sql, [$id]);
        return json_encode(["success" => true]);
    } catch (Exception $e) {
        Database::WriteLog("DeleteCustomer Error: " . $e->getMessage());
        return json_encode(["error" => "Delete failed"]);
    }
}
?>
