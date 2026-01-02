<?php

require "connect.php";

$pdo = Database::Connection();

// Log POST data for debugging
Database::WritePost($_POST);

$func_name = $_POST['func_name'] ?? "DisplayRecord";

if (function_exists($func_name)) {
    $func_name($pdo);
} else {
    Database::WriteLog("$func_name does not exist");
    echo json_encode([
        "status" => "error",
        "message" => "$func_name does not exist"
    ]);
}

/* ============================
   DISPLAY RECORDS
============================ */
function DisplayRecord(PDO $pdo)
{
    try {
        $sql = "SELECT OrderID, InquiryID, EmployeeID, DateTime, Status
                FROM tborder
                ORDER BY OrderID DESC";

        $data = Database::GetAllData($pdo, $sql);

        echo json_encode([
            "status" => "success",
            "data"   => $data
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
}

/* ============================
   ADD RECORD
============================ */
function AddRecord(PDO $pdo)
{
    $inquiryID  = trim($_POST['inquiryID'] ?? null);
    $employeeID = trim($_POST['employeeID'] ?? null);
    $dateTime   = trim($_POST['dateTimeLocal'] ?? date('Y-m-d H:i:s'));
    $status     = trim($_POST['status'] ?? "pending");

    try {
        $sql = "INSERT INTO tborder (InquiryID, EmployeeID, DateTime, Status)
                VALUES (:inquiryID, :employeeID, :dateTime, :status)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":inquiryID"  => $inquiryID,
            ":employeeID" => $employeeID,
            ":dateTime"   => $dateTime,
            ":status"     => $status
        ]);

        echo json_encode([
            "status" => "success",
            "message" => "Record successfully inserted."
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
}

/* ============================
   UPDATE RECORD
============================ */
function UpdateRecord(PDO $pdo)
{
    $orderID    = trim($_POST['orderID'] ?? "");
    $inquiryID  = trim($_POST['inquiryID'] ?? null);
    $employeeID = trim($_POST['employeeID'] ?? null);
    $dateTime   = trim($_POST['dateTimeLocal'] ?? date('Y-m-d H:i:s'));
    $status     = trim($_POST['status'] ?? "pending");

    if ($orderID === "") {
        echo json_encode([
            "status" => "error",
            "message" => "OrderID is required."
        ]);
        return;
    }

    try {
        $sql = "UPDATE tborder
                SET InquiryID = :inquiryID,
                    EmployeeID = :employeeID,
                    DateTime = :dateTime,
                    Status = :status
                WHERE OrderID = :orderID";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":inquiryID"  => $inquiryID,
            ":employeeID" => $employeeID,
            ":dateTime"   => $dateTime,
            ":status"     => $status,
            ":orderID"    => $orderID
        ]);

        echo json_encode([
            "status" => "success",
            "message" => "Record successfully updated."
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
}

/* ============================
   DELETE RECORD
============================ */
function DeleteRecord(PDO $pdo)
{
    $orderID = trim($_POST['orderID'] ?? "");

    if ($orderID === "") {
        echo json_encode([
            "status" => "error",
            "message" => "OrderID is required."
        ]);
        return;
    }

    try {
        $sql = "DELETE FROM tborder WHERE OrderID = :orderID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([":orderID" => $orderID]);

        echo json_encode([
            "status" => "success",
            "message" => "Record successfully deleted."
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
}
