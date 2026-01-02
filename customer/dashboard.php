<?php
require_once "../depotaconnect.php";

$stmt = $conn->query("SELECT * FROM inquiry ORDER BY id DESC");
$inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="../depota.css">
</head>
<body>

<nav class="navbar">
    <a href="../index.html">KD Sportswear</a>
</nav>

<h1>Customer Dashboard</h1>

<a href="submit_inquiry.html">Submit New Inquiry</a>

<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Status</th>
        <th>Design Notes</th>
        <th>Colors</th>
    </tr>

    <?php foreach ($inquiries as $row): ?>
    <tr>
        <td><?= $row["id"] ?></td>
        <td><?= $row["status"] ?></td>
        <td><?= $row["design_notes"] ?></td>
        <td><?= $row["colors"] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
