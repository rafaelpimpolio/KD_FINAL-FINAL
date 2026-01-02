<?php
// ALWAYS load database connection first
require_once __DIR__ . "/connect.php";

// Safety check (prevents your error)
if (!isset($conn)) {
    die("Database connection not found.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $role     = trim($_POST["role"] ?? "");

    if ($username === "" || $password === "" || $role === "") {
        die("All fields are required.");
    }

    // Fetch user by username & role
    $stmt = $conn->prepare(
        "SELECT * FROM users WHERE username = :username AND role = :role LIMIT 1"
    );
    $stmt->execute([
        ":username" => $username,
        ":role"     => $role
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Invalid username or role.");
    }

    // Verify hashed password
    if (!password_verify($password, $user["password"])) {
        die("Invalid password.");
    }

    // Redirect based on role
    if ($user["role"] === "customer") {
        header("Location: customer.html");
        exit;
    }

    if ($user["role"] === "employee") {
        header("Location: employee.html");
        exit;
    }
}
