<?php
include 'db.php';

$action = $_POST['action'] ?? '';

/* ================= CREATE ================= */
if ($action === 'create') {
    $transaction_id     = $_POST['transaction_id'];
    $payment_reference  = $_POST['payment_reference'];
    $transaction_status = $_POST['transaction_status'];
    $method_payment     = $_POST['method_payment'];
    $payment_date       = $_POST['payment_date'];
    $amount             = $_POST['amount'];
    $balance            = $_POST['balance'];

    $sql = "INSERT INTO payment 
        (transaction_id, payment_reference, transaction_status, method_payment, payment_date, amount, balance)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "issssdd",
        $transaction_id,
        $payment_reference,
        $transaction_status,
        $method_payment,
        $payment_date,
        $amount,
        $balance
    );

    echo $stmt->execute() ? "success" : "error";
}

/* ================= READ ================= */
if ($action === 'read') {
    $result = $conn->query("SELECT * FROM payment ORDER BY payment_id DESC");
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
}

/* ================= UPDATE ================= */
if ($action === 'update') {
    $payment_id         = $_POST['payment_id'];
    $transaction_id     = $_POST['transaction_id'];
    $payment_reference  = $_POST['payment_reference'];
    $transaction_status = $_POST['transaction_status'];
    $method_payment     = $_POST['method_payment'];
    $payment_date       = $_POST['payment_date'];
    $amount             = $_POST['amount'];
    $balance            = $_POST['balance'];

    $sql = "UPDATE payment SET
        transaction_id=?,
        payment_reference=?,
        transaction_status=?,
        method_payment=?,
        payment_date=?,
        amount=?,
        balance=?
        WHERE payment_id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "issssddi",
        $transaction_id,
        $payment_reference,
        $transaction_status,
        $method_payment,
        $payment_date,
        $amount,
        $balance,
        $payment_id
    );

    echo $stmt->execute() ? "updated" : "error";
}

/* ================= DELETE ================= */
if ($action === 'delete') {
    $payment_id = $_POST['payment_id'];
    $stmt = $conn->prepare("DELETE FROM payment WHERE payment_id=?");
    $stmt->bind_param("i", $payment_id);
    echo $stmt->execute() ? "deleted" : "error";
}
?>
