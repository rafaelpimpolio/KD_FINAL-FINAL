<?php
session_start();
require_once "depotaconnect.php"; // DB connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $role     = trim($_POST["role"]);

    if ($username === "" || $password === "" || $role === "") {
        die("All fields are required.");
    }

    // Fetch user from the correct table
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = :username AND role = :role LIMIT 1");
    $stmt->execute([
        ":username" => $username,
        ":role" => $role
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Invalid username or role."); // triggers if user not found
    }

    // Verify password
    if (!password_verify($password, $user["password_hash"])) {
        die("Invalid password."); // triggers if wrong password
    }

    // Optional: check status
    if (isset($user["status"]) && $user["status"] !== "active") {
        die("Your account is inactive. Contact admin.");
    }

    // Login successful → store session
    $_SESSION["username"] = $user["username"];
    $_SESSION["role"] = $user["role"];

    // Redirect
    if ($role === "customer") {
        header("Location: Balatan_Form/inquiry.html");
        exit;
    } else if ($role === "employee") {
        header("Location: Magan_Form/employee_to_inquiry_order.html");
        exit;
    }
}
?>
