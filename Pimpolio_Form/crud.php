<?php
require "connect.php";

$pdo = Database::Connection();

<<<<<<< HEAD
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
=======
if ($_POST['func_name'] === 'CreateCustomerAccount') {
    echo CreateCustomerAccount();
}

function CreateCustomerAccount()
{
    global $pdo;

    try {
        $pdo->beginTransaction();

        /* ---------- INSERT USER ---------- */
        $sqlUser = "INSERT INTO user (
                        username,
                        password_hash,
                        phone_number,
                        date_created,
                        role,
                        status
                    ) VALUES (?, ?, ?, NOW(), ?, ?)";

        $stmtUser = $pdo->prepare($sqlUser);

        $stmtUser->execute([
            $_POST['username'],
            password_hash($_POST['password'], PASSWORD_DEFAULT),
            $_POST['phone'],
            'customer',     // default role
            'active'        // default status
        ]);

        $user_id = $pdo->lastInsertId();

        /* ---------- INSERT CUSTOMER ---------- */
        $sqlCustomer = "INSERT INTO customer (
                            first_name,
                            last_name,
                            phone_number,
                            email,
                            barangay,
                            city_municipality,
                            province,
                            postal_code,
                            user_id
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmtCustomer = $pdo->prepare($sqlCustomer);
        $stmtCustomer->execute([
            $_POST['firstName'],
            $_POST['lastName'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['barangay'],
            $_POST['city'],
            $_POST['province'],
            $_POST['postalCode'],
            $user_id
        ]);

        $pdo->commit();

        return json_encode("Account successfully created!");

    } catch (Exception $e) {
        $pdo->rollBack();
        return json_encode("Error: " . $e->getMessage());
>>>>>>> d8b50dd3bf4c2b3cca1e1d17df4fcfec34ebc008
    }
}
?>
