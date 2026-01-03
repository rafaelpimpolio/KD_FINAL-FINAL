<?php
require_once "depotaconnect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $role     = trim($_POST["role"]);

    if ($username === "" || $password === "" || $role === "") {
        die("All fields are required.");
    }

    $stmt = $conn->prepare(
        "SELECT * FROM users WHERE username = :username AND role = :role LIMIT 1"
    );
    $stmt->execute([
        ":username" => $username,
        ":role" => $role
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user["password"])) {
        die("Invalid login credentials.");
    }

    if ($role === "customer") {
        header("Location: customer/dashboard.php");
        exit;
    }

    if ($role === "employee") {
        header("Location: Magan_Form");
        exit;
    }
}
