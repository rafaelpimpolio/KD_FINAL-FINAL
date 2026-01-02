<?php

require "connect.php";

$pdo = Database::Connection();
Database::WritePost($_POST);

$func_name = $_POST['func_name'] ?? "DisplayRecord";

if (function_exists($func_name)) {
    $func_name();
} else {
    Database::WriteLog($func_name . " not exist");
}

/* ============================
   DISPLAY RECORDS
============================ */
function DisplayRecord()
{
    $pdo = $GLOBALS['pdo'];

    $sql = "SELECT 
                OrderID,
                InquiryID,
                EmployeeID,
                DateTime,
                Status
            FROM tborder
            ORDER BY OrderID DESC";

    $data = Database::GetAllData($pdo, $sql);
    echo json_encode($data);
}

/* ============================
   ADD RECORD
============================ */
function AddRecord()
{
    $inquiryID  = $_POST['inquiryID'] ?? null;
    $employeeID = $_POST['employeeID'] ?? null;
    $dateTime   = $_POST['dateTimeLocal'] ?? date('Y-m-d H:i:s');
    $status     = $_POST['status'] ?? "pending";

    $pdo = $GLOBALS['pdo'];

    $sql = "INSERT INTO tborder 
            (InquiryID, EmployeeID, DateTime, Status)
            VALUES 
            (:inquiryID, :employeeID, :dateTime, :status)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":inquiryID"  => $inquiryID,
        ":employeeID" => $employeeID,
        ":dateTime"   => $dateTime,
        ":status"     => $status
    ]);

    echo json_encode("Successfully Inserted");
}

/* ============================
   UPDATE RECORD
============================ */
function UpdateRecord()
{
    $orderID    = $_POST['orderID'] ?? "";
    $inquiryID  = $_POST['inquiryID'] ?? null;
    $employeeID = $_POST['employeeID'] ?? null;
    $dateTime   = $_POST['dateTimeLocal'] ?? date('Y-m-d H:i:s');
    $status     = $_POST['status'] ?? "pending";

    if ($orderID === "") {
        echo json_encode("OrderID is required.");
        return;
    }

    $pdo = $GLOBALS['pdo'];

    $sql = "UPDATE tborder
            SET InquiryID  = :inquiryID,
                EmployeeID = :employeeID,
                DateTime   = :dateTime,
                Status     = :status
            WHERE OrderID  = :orderID";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":inquiryID"  => $inquiryID,
        ":employeeID" => $employeeID,
        ":dateTime"   => $dateTime,
        ":status"     => $status,
        ":orderID"    => $orderID
    ]);

    echo json_encode("Successfully Updated");
}

/* ============================
   DELETE RECORD
============================ */
function DeleteRecord()
{
    $orderID = $_POST['orderID'] ?? "";

    if ($orderID === "") {
        echo json_encode("OrderID is required.");
        return;
    }

    $pdo = $GLOBALS['pdo'];

    $sql = "DELETE FROM tborder WHERE OrderID = :orderID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":orderID" => $orderID
    ]);

    echo json_encode("Successfully Deleted");
}
