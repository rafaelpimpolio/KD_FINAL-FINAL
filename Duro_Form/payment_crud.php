<?php
require_once __DIR__ . '/connect.php';

$action = isset($_GET['action'])
    ? $_GET['action']
    : (isset($_POST['action']) ? $_POST['action'] : 'read');

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

        $method_of_payment = trim($_POST['method_of_payment']);
        $total_amount      = floatval($_POST['total_amount']);
        $downpayment       = floatval($_POST['downpayment']);
        $balance           = floatval($_POST['balance']);
        $status            = trim($_POST['status']);
        $date              = !empty($_POST['date']) ? $_POST['date'] : date('Y-m-d H:i:s');
        $customer_id       = intval($_POST['customer_id']);
        $order_id          = intval($_POST['order_id']);
        $employee_id       = intval($_POST['employee_id']);

        $sql = "INSERT INTO payment
                (method_of_payment, total_amount, downpayment, balance, status, date, customer_id, order_id, employee_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sdddssiii",
            $method_of_payment,
            $total_amount,
            $downpayment,
            $balance,
            $status,
            $date,
            $customer_id,
            $order_id,
            $employee_id
        );

        if ($stmt->execute()) {
            showAlert("Payment recorded successfully.");
        } else {
            showAlert("Error: " . $stmt->error);
        }
        $stmt->close();
    }
}

/* =========================
   READ PAYMENTS
========================= */
function handleRead() {
    global $conn;

    $sql = "SELECT * FROM payment ORDER BY payment_id DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            echo "<tr>
                <td>{$row['payment_id']}</td>
                <td>{$row['method_of_payment']}</td>
                <td>{$row['total_amount']}</td>
                <td>{$row['downpayment']}</td>
                <td>{$row['balance']}</td>
                <td>{$row['status']}</td>
                <td>{$row['date']}</td>
                <td>{$row['customer_id']}</td>
                <td>{$row['order_id']}</td>
                <td>{$row['employee_id']}</td>
                <td>
                    <a href='payment_crud.php?action=edit&id={$row['payment_id']}' class='btn btn-sm btn-warning'>Edit</a>
                    <a href='payment_crud.php?action=delete&id={$row['payment_id']}'
                       class='btn btn-sm btn-danger'
                       onclick='return confirm(\"Delete this payment?\")'>Delete</a>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='11' class='text-center'>No records found</td></tr>";
    }
}

/* =========================
   EDIT FORM
========================= */
function handleEditForm() {
    global $conn;

    if (!isset($_GET['id'])) die("Payment not found");

    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM payment WHERE payment_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $payment = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$payment) die("Payment not found");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Payment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
<div class="card p-4 shadow mx-auto" style="max-width:600px;">
<h4>Edit Payment</h4>

<form method="POST" action="payment_crud.php?action=update">
<input type="hidden" name="payment_id" value="<?= $payment['payment_id'] ?>">

<input class="form-control mb-2" name="method_of_payment" value="<?= $payment['method_of_payment'] ?>" required>
<input class="form-control mb-2" name="total_amount" type="number" step="0.01" value="<?= $payment['total_amount'] ?>" required>
<input class="form-control mb-2" name="downpayment" type="number" step="0.01" value="<?= $payment['downpayment'] ?>" required>
<input class="form-control mb-2" name="balance" type="number" step="0.01" value="<?= $payment['balance'] ?>" required>

<select class="form-select mb-2" name="status">
    <option <?= $payment['status']=="pending"?"selected":"" ?>>pending</option>
    <option <?= $payment['status']=="partial"?"selected":"" ?>>partial</option>
    <option <?= $payment['status']=="paid"?"selected":"" ?>>paid</option>
</select>

<input class="form-control mb-2" type="datetime-local" name="date"
       value="<?= date('Y-m-d\TH:i', strtotime($payment['date'])) ?>">

<input class="form-control mb-2" name="customer_id" value="<?= $payment['customer_id'] ?>" required>
<input class="form-control mb-2" name="order_id" value="<?= $payment['order_id'] ?>" required>
<input class="form-control mb-2" name="employee_id" value="<?= $payment['employee_id'] ?>" required>

<button class="btn btn-primary w-100">Update</button>
</form>
</div>
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $id                = intval($_POST['payment_id']);
        $method_of_payment = trim($_POST['method_of_payment']);
        $total_amount      = floatval($_POST['total_amount']);
        $downpayment       = floatval($_POST['downpayment']);
        $balance           = floatval($_POST['balance']);
        $status            = trim($_POST['status']);
        $date              = $_POST['date'];
        $customer_id       = intval($_POST['customer_id']);
        $order_id          = intval($_POST['order_id']);
        $employee_id       = intval($_POST['employee_id']);

        $sql = "UPDATE payment SET
                method_of_payment=?,
                total_amount=?,
                downpayment=?,
                balance=?,
                status=?,
                date=?,
                customer_id=?,
                order_id=?,
                employee_id=?
                WHERE payment_id=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sdddssiiii",
            $method_of_payment,
            $total_amount,
            $downpayment,
            $balance,
            $status,
            $date,
            $customer_id,
            $order_id,
            $employee_id,
            $id
        );

        if ($stmt->execute()) {
            showAlert("Payment updated successfully.");
        } else {
            showAlert("Error: " . $stmt->error);
        }
        $stmt->close();
    }
}

/* =========================
   DELETE PAYMENT
========================= */
function handleDelete() {
    global $conn;

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("DELETE FROM payment WHERE payment_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        showAlert("Payment deleted successfully.");
    }
}

/* =========================
   ALERT + REDIRECT
========================= */
function showAlert($message) {
    echo "<script>alert('$message'); window.location.href='payment-form.html';</script>";
    exit;
}

$conn->close();
?>
