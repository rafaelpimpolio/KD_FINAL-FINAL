<?php
require "connect.php";

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

function DisplayCustomer() {
    $pdo = $GLOBALS['pdo'];
    $sql = "SELECT * FROM customer ORDER BY customer_id DESC";
    return json_encode(Database::GetAllData($pdo, $sql));
}

function AddCustomer() {
    $pdo = $GLOBALS['pdo'];

    $sql = "INSERT INTO customer (
                first_name,
                last_name,
                phone_number,
                email,
                barangay,
                city_municipality,
                province,
                postal_code
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $params = [
        $_POST['firstName'] ?? '',
        $_POST['lastName'] ?? '',
        $_POST['phone'] ?? '',
        $_POST['email'] ?? '',
        $_POST['barangay'] ?? '',
        $_POST['city'] ?? '',
        $_POST['province'] ?? '',
        $_POST['postalCode'] ?? ''
    ];

    try {
        Database::ManageRecord($pdo, $sql, $params);
        return json_encode("Successfully Inserted");
    } catch (Exception $e) {
        Database::WriteLog("AddCustomer Error: " . $e->getMessage());
        return json_encode("Error inserting record: " . $e->getMessage());
    }
}

function UpdateCustomer() {
    $pdo = $GLOBALS['pdo'];

    $sql = "UPDATE customer SET
                first_name = ?,
                last_name = ?,
                phone_number = ?,
                email = ?,
                barangay = ?,
                city_municipality = ?,
                province = ?,
                postal_code = ?
            WHERE customer_id = ? ";

    $params = [
        $_POST['firstName'] ?? '',
        $_POST['lastName'] ?? '',
        $_POST['phone'] ?? '',
        $_POST['email'] ?? '',
        $_POST['barangay'] ?? '',
        $_POST['city'] ?? '',
        $_POST['province'] ?? '',
        $_POST['postalCode'] ?? '',
        $_POST['customerID'] ?? 0
    ];

    try {
        Database::ManageRecord($pdo, $sql, $params);
        return json_encode("Successfully Updated");
    } catch (Exception $e) {
        Database::WriteLog("UpdateCustomer Error: " . $e->getMessage());
        return json_encode("Error updating record: " . $e->getMessage());
    }
}

function DeleteCustomer() {
    $pdo = $GLOBALS['pdo'];
    $id = $_POST['customerID'] ?? 0;

    $sql = "DELETE FROM customer WHERE customer_id = ?";
    try {
        Database::ManageRecord($pdo, $sql, [$id]);
        return json_encode("Successfully Deleted");
    } catch (Exception $e) {
        Database::WriteLog("DeleteCustomer Error: " . $e->getMessage());
        return json_encode("Error deleting record: " . $e->getMessage());
    }
}
?>
