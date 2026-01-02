<?php
require "connect.php";

$pdo = Database::Connection();

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
    }
}
?>
