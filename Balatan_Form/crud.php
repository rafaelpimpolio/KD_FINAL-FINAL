<?php
require "connect.php";
header('Content-Type: application/json');

$pdo = Database::Connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'] ?? null;
    $design_name = $_POST['design_name'] ?? '';
    $product_type = $_POST['product_type'] ?? '';
    $size = $_POST['size'] ?? '';
    $fabric_type = $_POST['fabric_type'] ?? '';
    $color = isset($_POST['color']) ? implode(',', $_POST['color']) : '';

    // Handle file upload
    $design_file = '';
    if (!empty($_FILES['design_file']['name']) && $_FILES['design_file']['error'] === 0) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        $design_file = $targetDir . time() . '_' . basename($_FILES['design_file']['name']);
        move_uploaded_file($_FILES['design_file']['tmp_name'], $design_file);
    }

    $sql = "INSERT INTO inquiry 
        (design_name, design_file, inquiry_date, initial_price, status, customer_id, employee_id, size, fabric_type, color, product_type)
        VALUES
        (:design_name, :design_file, NOW(), NULL, 'Pending', :customer_id, NULL, :size, :fabric_type, :color, :product_type)";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':design_name' => $design_name,
        ':design_file' => $design_file,
        ':customer_id' => $customer_id,
        ':size' => $size,
        ':fabric_type' => $fabric_type,
        ':color' => $color,
        ':product_type' => $product_type
    ]);

    if ($result) {
        echo json_encode(["status" => "success", "message" => "Inquiry submitted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to submit inquiry"]);
    }
}
