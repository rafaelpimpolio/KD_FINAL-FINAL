<?php
require "connect.php";
$pdo = Database::Connection();

$func = $_POST['func_name'] ?? '';

$allowed = ["DisplayCustomer","AddCustomer","UpdateCustomer","DeleteCustomer"];
if (!in_array($func, $allowed)) {
    echo json_encode(["success"=>false,"message"=>"Invalid action"]);
    exit;
}

call_user_func($func);

// ================= DISPLAY =================
function DisplayCustomer() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM customer ORDER BY customer_id DESC");
    echo json_encode([
        "success" => true,
        "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}

// ================= ADD =================
function AddCustomer() {
    global $pdo;
    $sql = "INSERT INTO customer 
        (first_name,last_name,phone_number,email,barangay,city_municipality,province,postal_code)
        VALUES (?,?,?,?,?,?,?,?)";

    $pdo->prepare($sql)->execute([
        $_POST['firstName'],
        $_POST['lastName'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['barangay'],
        $_POST['city'],
        $_POST['province'],
        $_POST['postalCode']
    ]);

    echo json_encode(["success"=>true,"message"=>"Customer added successfully"]);
}

// ================= UPDATE =================
function UpdateCustomer() {
    global $pdo;
    $sql = "UPDATE customer SET
        first_name=?, last_name=?, phone_number=?, email=?,
        barangay=?, city_municipality=?, province=?, postal_code=?
        WHERE customer_id=?";

    $pdo->prepare($sql)->execute([
        $_POST['firstName'],
        $_POST['lastName'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['barangay'],
        $_POST['city'],
        $_POST['province'],
        $_POST['postalCode'],
        $_POST['customerID']
    ]);

    echo json_encode(["success"=>true,"message"=>"Customer updated successfully"]);
}

// ================= DELETE =================
function DeleteCustomer() {
    global $pdo;
    $pdo->prepare("DELETE FROM customer WHERE customer_id=?")
        ->execute([$_POST['customerID']]);

    echo json_encode(["success"=>true,"message"=>"Customer deleted successfully"]);
}
