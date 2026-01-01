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

        $transaction_id      = htmlspecialchars(trim($_POST['transaction_id']));
        $payment_reference   = htmlspecialchars(trim($_POST['payment_reference']));
        $transaction_status  = htmlspecialchars(trim($_POST['transaction_status']));
        $method_payment      = htmlspecialchars(trim($_POST['method_payment']));
        $payment_date        = htmlspecialchars(trim($_POST['payment_date']));
        $amount              = floatval($_POST['amount']);
        $balance             = floatval($_POST['balance']);

        // Check duplicate payment reference
        $checkSql = "SELECT payment_id FROM payments WHERE payment_reference = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $payment_reference);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            showAlert("danger", "Payment Reference <strong>$payment_reference</strong> already exists.");
            $checkStmt->close();
            return;
        }
        $checkStmt->close();

        $sql = "INSERT INTO payments
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

        if ($stmt->execute()) {
            showAlert("success", "Payment recorded successfully.");
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
            $html .= '<td>' . htmlspecialchars($row['payment_id']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['transaction_id']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['payment_reference']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['transaction_status']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['method_payment']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['payment_date']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['amount']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['balance']) . '</td>';
            $html .= '<td>
                        <a href="payment_crud.php?action=edit&id=' . $row['payment_id'] . '" class="btn btn-sm btn-warning">Edit</a>
                        <a href="payment_crud.php?action=delete&id=' . $row['payment_id'] . '" class="btn btn-sm btn-danger"
                           onclick="return confirm(\'Are you sure?\')">Delete</a>
                      </td>';
            $html .= '</tr>';
        }
    } else {
        $html = '<tr><td colspan="9" class="text-center text-muted">No payment records found.</td></tr>';
    }

    echo $html;
}

/* =========================
   EDIT FORM
========================= */
function handleEditForm() {
    global $conn;

    if (!isset($_GET['id'])) {
        die("Payment not found!");
    }

    $id = intval($_GET['id']);
    $sql = "SELECT * FROM payments WHERE payment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment = $result->fetch_assoc();
    $stmt->close();

    if (!$payment) {
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
    <div class="card p-4 shadow-sm mx-auto" style="max-width: 550px;">
        <h3 class="text-center mb-4">Edit Payment</h3>

        <form method="POST" action="payment_crud.php?action=update" class="needs-validation" novalidate>
            <input type="hidden" name="payment_id" value="<?= $payment['payment_id'] ?>">

            <div class="mb-3">
                <label>Transaction ID</label>
                <input type="number" class="form-control" name="transaction_id"
                       value="<?= htmlspecialchars($payment['transaction_id']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Payment Reference</label>
                <input type="text" class="form-control" name="payment_reference"
                       value="<?= htmlspecialchars($payment['payment_reference']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Status</label>
                <select class="form-select" name="transaction_status" required>
                    <option <?= $payment['transaction_status']=="Pending"?"selected":"" ?>>Pending</option>
                    <option <?= $payment['transaction_status']=="Completed"?"selected":"" ?>>Completed</option>
                    <option <?= $payment['transaction_status']=="Failed"?"selected":"" ?>>Failed</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Payment Method</label>
                <select class="form-select" name="method_payment" required>
                    <option <?= $payment['method_payment']=="Cash"?"selected":"" ?>>Cash</option>
                    <option <?= $payment['method_payment']=="GCash"?"selected":"" ?>>GCash</option>
                    <option <?= $payment['method_payment']=="Bank Transfer"?"selected":"" ?>>Bank Transfer</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Payment Date</label>
                <input type="date" class="form-control" name="payment_date"
                       value="<?= $payment['payment_date'] ?>" required>
            </div>

            <div class="mb-3">
                <label>Amount</label>
                <input type="number" class="form-control" name="amount"
                       value="<?= $payment['amount'] ?>" required min="1">
            </div>

            <div class="mb-3">
                <label>Balance</label>
                <input type="number" class="form-control" name="balance"
                       value="<?= $payment['balance'] ?>" required min="0">
            </div>

            <button class="btn btn-primary w-100">Update Payment</button>
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

        $id                 = intval($_POST['payment_id']);
        $transaction_id     = htmlspecialchars(trim($_POST['transaction_id']));
        $payment_reference  = htmlspecialchars(trim($_POST['payment_reference']));
        $transaction_status = htmlspecialchars(trim($_POST['transaction_status']));
        $method_payment     = htmlspecialchars(trim($_POST['method_payment']));
        $payment_date       = htmlspecialchars(trim($_POST['payment_date']));
        $amount             = floatval($_POST['amount']);
        $balance            = floatval($_POST['balance']);

        $sql = "UPDATE payments SET
                transaction_id=?, payment_reference=?, transaction_status=?, method_payment=?,
                payment_date=?, amount=?, balance=?
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
            $id
        );

        if ($stmt->execute()) {
            showAlert("success", "Payment updated successfully.");
        } else {
            showAlert("danger", "Error: " . $stmt->error);
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

        $sql = "DELETE FROM payments WHERE payment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            showAlert("success", "Payment deleted successfully.");
        } else {
            showAlert("danger", "Error deleting payment.");
        }
        $stmt->close();
    }
}

/* =========================
   ALERT
========================= */
function showAlert($type, $message) {
?>
<script>
    alert("<?= strip_tags($message) ?>");
    window.location.href = "payment-form.html";
</script>
<?php
    exit();
}

$conn->close();
?>
