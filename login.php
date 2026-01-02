<?php
session_start();
require_once "depotaconnect.php"; // your DB connection file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $role     = trim($_POST["role"]);

    if ($username === "" || $password === "" || $role === "") {
        die("All fields are required.");
    }

    // Fetch user from "user" table
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = :username AND role = :role LIMIT 1");
    $stmt->execute([
        ":username" => $username,
        ":role" => $role
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Invalid username or role.");
    }

    // Verify password hash
    if (!password_verify($password, $user["password_hash"])) {
        die("Invalid password.");
    }

    // Optional: check account status
    if (isset($user["status"]) && $user["status"] !== "active") {
        die("Your account is inactive. Contact admin.");
    }

    // Login successful → store session
    $_SESSION["username"] = $user["username"];
    $_SESSION["role"] = $user["role"];

    // Redirect based on role
    if ($role === "customer") {
        header("Location: customer/dashboard.php");
        exit;
    }

    if ($role === "employee") {
        header("Location: employee/dashboard.php");
        exit;
    }
}
?>
