<?php
require "connect.php";
header('Content-Type: application/json');

$pdo = Database::Connection();

$func_name = $_POST['func_name'] ?? 'AddRecord';

if (function_exists($func_name)) {
    $func_name();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid function"]);
}

function DisplayRecord()
{
    global $pdo;
    $sql = "SELECT * FROM kd_form ORDER BY id DESC";
    $data = Database::GetAllData($pdo, $sql);
    echo json_encode($data);
}

function AddRecord()
{
    global $pdo;

    $customerFile = '';
    if (isset($_FILES['customerFile']) && $_FILES['customerFile']['error'] == 0) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        $customerFile = $targetDir . time() . '_' . basename($_FILES['customerFile']['name']);
        move_uploaded_file($_FILES['customerFile']['tmp_name'], $customerFile);
    }

    $colorSelection = isset($_POST['colorSelection'])
        ? implode(',', $_POST['colorSelection'])
        : '';

    $sql = "INSERT INTO kd_form (
        customer, customerFile, jerseySando, jerseyNeck, jerseySandoSize,
        longsleeves, tshirt, tshirtSize, poloSize, others,
        jerseyShort, shortSize, joggingPants, warmer,
        sublimationDTF, otherService, colorSelection, created_at
    ) VALUES (
        :customer, :customerFile, :jerseySando, :jerseyNeck, :jerseySandoSize,
        :longsleeves, :tshirt, :tshirtSize, :poloSize, :others,
        :jerseyShort, :shortSize, :joggingPants, :warmer,
        :sublimationDTF, :otherService, :colorSelection, NOW()
    )";

    try {
        Database::ManageRecord($pdo, $sql, [
            ':customer' => $_POST['customer'] ?? '',
            ':customerFile' => $customerFile,
            ':jerseySando' => $_POST['jerseySando'] ?? '',
            ':jerseyNeck' => $_POST['jerseyNeck'] ?? '',
            ':jerseySandoSize' => $_POST['jerseySandoSize'] ?? '',
            ':longsleeves' => $_POST['longsleeves'] ?? '',
            ':tshirt' => $_POST['tshirt'] ?? '',
            ':tshirtSize' => $_POST['tshirtSize'] ?? '',
            ':poloSize' => $_POST['poloSize'] ?? '',
            ':others' => $_POST['others'] ?? '',
            ':jerseyShort' => $_POST['jerseyShort'] ?? '',
            ':shortSize' => $_POST['shortSize'] ?? '',
            ':joggingPants' => $_POST['joggingPants'] ?? '',
            ':warmer' => $_POST['warmer'] ?? '',
            ':sublimationDTF' => $_POST['sublimationDTF'] ?? '',
            ':otherService' => $_POST['otherService'] ?? '',
            ':colorSelection' => $colorSelection
        ]);

        echo json_encode(["status" => "success", "message" => "Record added successfully."]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}

function UpdateRecord()
{
    global $pdo;

    $colorSelection = isset($_POST['colorSelection'])
        ? implode(',', $_POST['colorSelection'])
        : '';

    $customerFile = '';
    if (isset($_FILES['customerFile']) && $_FILES['customerFile']['error'] == 0) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        $customerFile = $targetDir . time() . '_' . basename($_FILES['customerFile']['name']);
        move_uploaded_file($_FILES['customerFile']['tmp_name'], $customerFile);
    }

    $sql = "UPDATE kd_form SET
        customer = :customer,
        customerFile = CASE WHEN :customerFile <> '' THEN :customerFile ELSE customerFile END,
        jerseySando = :jerseySando,
        jerseyNeck = :jerseyNeck,
        jerseySandoSize = :jerseySandoSize,
        longsleeves = :longsleeves,
        tshirt = :tshirt,
        tshirtSize = :tshirtSize,
        poloSize = :poloSize,
        others = :others,
        jerseyShort = :jerseyShort,
        shortSize = :shortSize,
        joggingPants = :joggingPants,
        warmer = :warmer,
        sublimationDTF = :sublimationDTF,
        otherService = :otherService,
        colorSelection = :colorSelection
        WHERE id = :id";

    Database::ManageRecord($pdo, $sql, [
        ':id' => $_POST['id'],
        ':customer' => $_POST['customer'] ?? '',
        ':customerFile' => $customerFile,
        ':jerseySando' => $_POST['jerseySando'] ?? '',
        ':jerseyNeck' => $_POST['jerseyNeck'] ?? '',
        ':jerseySandoSize' => $_POST['jerseySandoSize'] ?? '',
        ':longsleeves' => $_POST['longsleeves'] ?? '',
        ':tshirt' => $_POST['tshirt'] ?? '',
        ':tshirtSize' => $_POST['tshirtSize'] ?? '',
        ':poloSize' => $_POST['poloSize'] ?? '',
        ':others' => $_POST['others'] ?? '',
        ':jerseyShort' => $_POST['jerseyShort'] ?? '',
        ':shortSize' => $_POST['shortSize'] ?? '',
        ':joggingPants' => $_POST['joggingPants'] ?? '',
        ':warmer' => $_POST['warmer'] ?? '',
        ':sublimationDTF' => $_POST['sublimationDTF'] ?? '',
        ':otherService' => $_POST['otherService'] ?? '',
        ':colorSelection' => $colorSelection
    ]);

    echo json_encode(["status" => "success", "message" => "Record updated"]);
}

function DeleteRecord()
{
    global $pdo;

    $sql = "DELETE FROM kd_form WHERE id = :id";
    Database::ManageRecord($pdo, $sql, [
        ':id' => $_POST['id']
    ]);

    echo json_encode(["status" => "success", "message" => "Record deleted"]);
}

function GetRecord()
{
    global $pdo;

    if (!isset($_POST['id'])) {
        echo json_encode(null);
        return;
    }

    $sql = "SELECT * FROM kd_form WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $_POST['id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($row);
}
