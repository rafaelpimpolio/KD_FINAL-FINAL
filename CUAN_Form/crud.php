<?php
header("Content-Type: application/json");
include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

// Handle different request types (GET, POST, DELETE)
switch ($method) {
    case 'GET':
        // Fetch all users from database
        $sql = "SELECT * FROM users ORDER BY id DESC";
        $result = $conn->query($sql);
        $users = [];
        while($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        echo json_encode($users);
        break;

    case 'POST':
        // Get data from the JavaScript Fetch request
        $input = json_decode(file_get_contents('php://input'), true);
        $username = $input['username'];
        $phone = $input['phone'];
        $status = $input['status'];
        
        if (isset($input['id']) && !empty($input['id'])) {
            // UPDATE existing user
            $id = $input['id'];
            $stmt = $conn->prepare("UPDATE users SET username=?, phone=?, status=? WHERE id=?");
            $stmt->bind_param("sssi", $username, $phone, $status, $id);
        } else {
            // CREATE new user
            $stmt = $conn->prepare("INSERT INTO users (username, phone, status) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $phone, $status);
        }
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => $conn->error]);
        }
        break;

    case 'DELETE':
        // Delete user by ID passed in the URL (?id=xxx)
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $conn->query("DELETE FROM users WHERE id=$id");
            echo json_encode(["status" => "success"]);
        }
        break;
}

$conn->close();
?>