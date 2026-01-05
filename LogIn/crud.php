<?php
header('Content-Type: application/json');
session_start();

require_once 'connect.php'; 

$conn = getConnection();

$action = $_POST['action'] ?? '';

if ($action === 'signup') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $barangay = trim($_POST['barangay'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$first_name || !$last_name || !$phone || !$email || !$barangay || !$city || !$province || !$postal_code || !$username || !$password) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
        exit;
    }

    $stmt = $conn->prepare("SELECT customer_id FROM customer WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username already taken']);
        exit;
    }
    $stmt->close();

    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO customer (first_name, last_name, phone, email, barangay, city, province, postal_code, username, password, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssssssss", $first_name, $last_name, $phone, $email, $barangay, $city, $province, $postal_code, $username, $passwordHash);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    $stmt->close();
    $conn->close();
    exit;
}

if ($action === 'login') {
    $role = $_POST['role'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$role || !$username || !$password) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
        exit;
    }

    // Determine table based on role
    $table = '';
    if ($role === 'customer') {
        $table = 'customer';
    } elseif ($role === 'employee') {
        $table = 'employee';
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid role selected']);
        exit;
    }

    $stmt = $conn->prepare("SELECT password FROM $table WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($hash);
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Incorrect password']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
    $stmt->close();
    $conn->close();
    exit;
}

// If no valid action
echo json_encode(['success' => false, 'message' => 'Invalid action']);
exit;
?>
