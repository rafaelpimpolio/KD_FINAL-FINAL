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
    echo json_encode(Database::GetAllData($pdo, $sql));
}


function AddRecord()
{
    global $pdo;

    $dropdownFields = [
        'jerseySando',
        'jerseyNeck',
        'tshirt',
        'poloSize',
        'others',
        'jerseyShort',
        'sublimationDTF',
        'otherService'
    ];

    $hasSelection = false;
    foreach ($dropdownFields as $field) {
        if (!empty($_POST[$field])) {
            $hasSelection = true;
            break;
        }
    }

    if (!$hasSelection) {
        echo json_encode([
            "status" => "error",
            "message" => "At least one dropdown must be selected."
        ]);
        return;
    }

    $customerFile = '';
    if (!empty($_FILES['customerFile']['name']) && $_FILES['customerFile']['error'] === 0) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $customerFile = $targetDir . time() . '_' . basename($_FILES['customerFile']['name']);
        move_uploaded_file($_FILES['customerFile']['tmp_name'], $customerFile);
    }

    $colorSelection = isset($_POST['colorSelection'])
        ? implode(',', $_POST['colorSelection'])
        : '';

    $materialType = $_POST['materialType'] ?? '';

    $sql = "INSERT INTO kd_form (
        customer, customerFile, jerseySando, jerseyNeck, jerseySandoSize,
        longsleeves, tshirt, tshirtSize, poloSize, others,
        jerseyShort, shortSize, joggingPants, warmer,
        sublimationDTF, otherService, colorSelection, materialType, created_at
    ) VALUES (
        :customer, :customerFile, :jerseySando, :jerseyNeck, :jerseySandoSize,
        :longsleeves, :tshirt, :tshirtSize, :poloSize, :others,
        :jerseyShort, :shortSize, :joggingPants, :warmer,
        :sublimationDTF, :otherService, :colorSelection, :materialType, NOW()
    )";

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
        ':colorSelection' => $colorSelection,
        ':materialType' => $materialType
    ]);

    echo json_encode(["status" => "success", "message" => "Record added successfully."]);
}


function UpdateRecord()
{
    global $pdo;

    $colorSelection = isset($_POST['colorSelection'])
        ? implode(',', $_POST['colorSelection'])
        : '';

    $materialType = $_POST['materialType'] ?? '';

    $customerFile = '';
    if (!empty($_FILES['customerFile']['name']) && $_FILES['customerFile']['error'] === 0) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $customerFile = $targetDir . time() . '_' . basename($_FILES['customerFile']['name']);
        move_uploaded_file($_FILES['customerFile']['tmp_name'], $customerFile);
    }

    $sql = "UPDATE kd_form SET
        customer = :customer,
        customerFile = IF(:customerFile = '', customerFile, :customerFile),
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
        materialType = :materialType,
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
        ':materialType' => $materialType,
        ':colorSelection' => $colorSelection
    ]);

    echo json_encode(["status" => "success", "message" => "Record updated"]);
}


function DeleteRecord()
{
    global $pdo;
    Database::ManageRecord(
        $pdo,
        "DELETE FROM kd_form WHERE id = :id",
        [':id' => $_POST['id']]
    );
    echo json_encode(["status" => "success", "message" => "Record deleted"]);
}


function GetRecord()
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM kd_form WHERE id = :id");
    $stmt->execute([':id' => $_POST['id']]);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
}
