<?php
require_once __DIR__ . '/connect.php';

header('Content-Type: application/json');

// Get all employee users that are not already linked to an employee
$sql = "SELECT u.user_id, u.username
        FROM user u
        LEFT JOIN employee e ON u.user_id = e.user_id
        WHERE u.role = 'employee' AND e.employee_id IS NULL
        ORDER BY u.username";

$result = $conn->query($sql);
$users = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

echo json_encode($users);

$conn->close();
?>