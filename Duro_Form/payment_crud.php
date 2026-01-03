<?php
require_once __DIR__ . '/connect.php';

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : 'read');

switch ($action) {
    case 'create':
        handleCreate();
        break;
    case 'read':
        handleRead();
        break;
    case 'edit':
        handleEditForm();
        break;
    case 'update':
        handleUpdate();
        break;
    case 'delete':
        handleDelete();
        break;
    default:
        handleRead();
}

/* =========================
   CREATE PAYMENT
========================= */
function handleCreate() {
    global $conn;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $method_of_payment = htmlspecialchars(trim($_POST['method_of_payment']));
        $total_amount      = floatval($_POST['total_amount']);
        $downpayment       = floatval($_POST['downpayment']);
        $balance           = floatval($_POST['balance']);
        $status            = htmlspecialchars(trim($_POST['status']));
        $customer_id       = intval($_POST['customer_id']);
        $order_id          = intval($_POST['order_id']);
        $employee_id       = intval($_POST['employee_id']);

        $sql = "INSERT INTO payments
                (method_of_payment, total_amount, downpayment, balance, status, customer_id, order_id, employee_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sdddsi ii",
            $method_of_payment,
            $total_amount,
            $downpayment,
            $balance,
            $status,
            $customer_id,
            $order_id,
            $employee_id
        );

        if ($stmt->execute()) {
            showAlert("success", "Payment added successfully.");
        } else {
            showAlert("danger", "Error: " . $stmt->error);
        }
        $stmt->close();
    }
}

/* =========================
   READ PAYMENTS
========================= */
function handleRead() {
    global $conn;

    $sql = "SELECT * FROM payments ORDER BY payment_id DESC";
    $result = $conn->query($sql);

    $html = '';

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {

            $html .= '<tr>';
            $html .= '<td>'.$row['payment_id'].'</td>';
            $html .= '<td>'.$row['method_of_payment'].'</td>';
            $html .= '<td>'.$row['total_amount'].'</td>';
            $html .= '<td>'.$row['downpayment'].'</td>';
            $html .= '<td>'.$row['balance'].'</td>';
            $html .= '<td>'.$row['date'].'</td>';
            $html .= '<td>'.$row['status'].'</td>';
            $html .= '<td>'.$row['customer_id'].'</td>';
            $html .= '<td>'.$row['order_id'].'</td>';
            $html .= '<td>'.$row['employee_id'].'</td>';
            $html .= '<td>
                        <a href="payment_crud.php?action=edit&id='.$row['payment_id'].'" class="btn btn-warning btn-sm">Edit</a>
                        <a href="payment_crud.php?action=delete&id='.$row['payment_id'].'" class="btn btn-danger btn-sm"
                           onclick="return confirm(\'Are you sure?\')">Delete</a>
                      </td>';
            $html .= '</tr>';
        }
    } else {
        $html = '<tr><td colspan="11" class="text-center">No records found</td></tr>';
    }

    echo $html;
}

/* =========================
   EDIT FORM
========================= */
function handleEditForm() {
    global $conn;



    $id = intval($_GET['id']);
    $sql = "SELECT * FROM payments WHERE payment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $payment = $stmt->get_result()->fetch_assoc();

    $stmt->close();
        if (!isset($_GET['id'])) {
        die("Payment not found!");
    }
?>
<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <title>Edit Payment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
<form method="POST" action="payment_crud.php?action=update" class="card p-4">
<input type="hidden" name="payment_id" value="<?= $payment['payment_id'] ?>">

<label>Method of Payment</label>
<input type="text" name="method_of_payment" class="form-control" value="<?= $payment['method_of_payment'] ?>">

<label>Total Amount</label>
<input type="number" name="total_amount" class="form-control" value="<?= $payment['total_amount'] ?>">

<label>Downpayment</label>
<input type="number" name="downpayment" class="form-control" value="<?= $payment['downpayment'] ?>">

<label>Balance</label>
<input type="number" name="balance" class="form-control" value="<?= $payment['balance'] ?>">

<label>Status</label>
<select name="status" class="form-control">
    <option <?= $payment['status']=="pending"?"selected":"" ?>>pending</option>
    <option <?= $payment['status']=="partial"?"selected":"" ?>>partial</option>
    <option <?= $payment['status']=="paid"?"selected":"" ?>>paid</option>
</select>

<label>Customer ID</label>
<input type="number" name="customer_id" class="form-control" value="<?= $payment['customer_id'] ?>">

<label>Order ID</label>
<input type="number" name="order_id" class="form-control" value="<?= $payment['order_id'] ?>">

<label>Employee ID</label>
<input type="number" name="employee_id" class="form-control" value="<?= $payment['employee_id'] ?>">

<button class="btn btn-primary mt-3">Update</button>
</form>
</div>
</body>
</html>
<?php
}

/* =========================
   UPDATE PAYMENT
========================= */
function handleUpdate() {
    global $conn;

    $sql = "UPDATE payments SET
            method_of_payment=?,
            total_amount=?,
            downpayment=?,
            balance=?,
            status=?,
            customer_id=?,
            order_id=?,
            employee_id=?
            WHERE payment_id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sdddsi iii",
        $_POST['method_of_payment'],
        $_POST['total_amount'],
        $_POST['downpayment'],
        $_POST['balance'],
        $_POST['status'],
        $_POST['customer_id'],
        $_POST['order_id'],
        $_POST['employee_id'],
        $_POST['payment_id']
    );

    if ($stmt->execute()) {
        showAlert("success", "Payment updated.");
    } else {
        showAlert("danger", "Update failed.");
    }
    $stmt->close();
}

/* =========================
   DELETE PAYMENT
========================= */
function handleDelete() {
    global $conn;

    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM payments WHERE payment_id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        showAlert("success", "Payment deleted.");
    }
    $stmt->close();
}

/* =========================
   ALERT
========================= */
function showAlert($type, $message) {
?>
<script>
alert("<?= $message ?>");
window.location.href="payment-form.html";
</script>
<?php exit(); }

$conn->close();
?>
