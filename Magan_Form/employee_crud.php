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

function handleCreate() {
    global $conn;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $employee_id = htmlspecialchars(trim($_POST['employee_id']));
        $first_name = htmlspecialchars(trim($_POST['first_name']));
        $last_name = htmlspecialchars(trim($_POST['last_name']));
        $job_position = htmlspecialchars(trim($_POST['job_position']));
        $phone_number = htmlspecialchars(trim($_POST['phone_number']));
        $email = htmlspecialchars(trim($_POST['email']));

        // Check if employee_id already exists
        $checkIdSql = "SELECT id FROM employees WHERE employee_id = ?";
        $checkIdStmt = $conn->prepare($checkIdSql);
        $checkIdStmt->bind_param("s", $employee_id);
        $checkIdStmt->execute();
        $checkIdResult = $checkIdStmt->get_result();

        if ($checkIdResult->num_rows > 0) {
            showAlert("danger", "Employee ID <strong>$employee_id</strong> already exists. Please use a different ID.");
            writeLog("CREATE_FAILED", $employee_id, $first_name, $last_name, "Duplicate Employee ID");
            $checkIdStmt->close();
            return;
        }
        $checkIdStmt->close();

        // Check if email already exists
        $checkEmailSql = "SELECT id FROM employees WHERE email = ?";
        $checkEmailStmt = $conn->prepare($checkEmailSql);
        $checkEmailStmt->bind_param("s", $email);
        $checkEmailStmt->execute();
        $checkEmailResult = $checkEmailStmt->get_result();

        if ($checkEmailResult->num_rows > 0) {
            showAlert("danger", "Email <strong>$email</strong> already exists. Please use a different email.");
            writeLog("CREATE_FAILED", $employee_id, $first_name, $last_name, "Duplicate Email");
            $checkEmailStmt->close();
            return;
        }
        $checkEmailStmt->close();

        $sql = "INSERT INTO employees (employee_id, first_name, last_name, job_position, phone_number, email)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $employee_id, $first_name, $last_name, $job_position, $phone_number, $email);

        if ($stmt->execute()) {
            writeLog("CREATE", $employee_id, $first_name, $last_name, "Position: $job_position | Email: $email");
            showAlert("success", "Employee added successfully.");
        } else {
            showAlert("danger", "Error: " . $stmt->error);
            writeLog("CREATE_FAILED", $employee_id, $first_name, $last_name, "Error: " . $stmt->error);
        }
        $stmt->close();
    }
}

function handleRead() {
    global $conn;

    $sql = "SELECT id, employee_id, first_name, last_name, job_position, phone_number, email FROM employees ORDER BY id DESC";
    $result = $conn->query($sql);

    $html = '';

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($row['employee_id']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['first_name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['last_name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['job_position']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['phone_number']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['email']) . '</td>';
            $html .= '<td>';
            $html .= '<div class="action-buttons">';
            $html .= '<a href="employee_crud.php?action=edit&id=' . $row['id'] . '" class="btn btn-sm btn-warning">Edit</a> ';
            $html .= '<a href="employee_crud.php?action=delete&id=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</a>';
            $html .= '</div>';
            $html .= '</td>';
            $html .= '</tr>';
        }
    } else {
        $html = '<tr><td colspan="7" class="text-center text-muted">No employees found. Add one to get started!</td></tr>';
    }

    echo $html;
}

function handleEditForm() {
    global $conn;

    if (!isset($_GET['id'])) {
        die("Employee not found!");
    }

    $id = intval($_GET['id']);
    $sql = "SELECT * FROM employees WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();
    $stmt->close();

    if (!$employee) {
        die("Employee not found!");
    }

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Employee</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="card shadow-sm p-4 mx-auto" style="max-width: 500px;">
                <h3 class="text-center mb-4">Edit Employee</h3>
                <form method="POST" action="employee_crud.php?action=update" class="needs-validation" novalidate>
                    <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">

                    <div class="mb-3">
                        <label class="form-label">Employee ID</label>
                        <input type="text" class="form-control" name="employee_id" value="<?php echo htmlspecialchars($employee['employee_id']); ?>" required pattern="[0-9]{2,10}">
                        <div class="invalid-feedback">Please enter a valid Employee ID.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($employee['first_name']); ?>" required pattern="[A-Za-z ]+">
                        <div class="invalid-feedback">First name should contain only letters.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($employee['last_name']); ?>" required pattern="[A-Za-z ]+">
                        <div class="invalid-feedback">Last name should contain only letters.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Job Position</label>
                        <select class="form-select" name="job_position" required>
                            <option value="Manager" <?php echo $employee['job_position'] === 'Manager' ? 'selected' : ''; ?>>Manager</option>
                            <option value="Supervisor" <?php echo $employee['job_position'] === 'Supervisor' ? 'selected' : ''; ?>>Supervisor</option>
                            <option value="Staff" <?php echo $employee['job_position'] === 'Staff' ? 'selected' : ''; ?>>Staff</option>
                        </select>
                        <div class="invalid-feedback">Please select a job position.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" name="phone_number" value="<?php echo htmlspecialchars($employee['phone_number']); ?>" required pattern="09[0-9]{9}">
                        <div class="invalid-feedback">Enter a valid PH mobile number.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" required>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary flex-grow-1" type="submit">Update Employee</button>
                        <a href="employee.html" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        (() => {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
        </script>
    </body>
    </html>
    <?php
}

function handleUpdate() {
    global $conn;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $employee_id = htmlspecialchars(trim($_POST['employee_id']));
        $first_name = htmlspecialchars(trim($_POST['first_name']));
        $last_name = htmlspecialchars(trim($_POST['last_name']));
        $job_position = htmlspecialchars(trim($_POST['job_position']));
        $phone_number = htmlspecialchars(trim($_POST['phone_number']));
        $email = htmlspecialchars(trim($_POST['email']));

        // Get old data for comparison
        $selectSql = "SELECT * FROM employees WHERE id = ?";
        $selectStmt = $conn->prepare($selectSql);
        $selectStmt->bind_param("i", $id);
        $selectStmt->execute();
        $result = $selectStmt->get_result();
        $oldEmployee = $result->fetch_assoc();
        $selectStmt->close();

        // Check if email already exists (but not the same employee)
        $checkEmailSql = "SELECT id FROM employees WHERE email = ? AND id != ?";
        $checkEmailStmt = $conn->prepare($checkEmailSql);
        $checkEmailStmt->bind_param("si", $email, $id);
        $checkEmailStmt->execute();
        $checkEmailResult = $checkEmailStmt->get_result();

        if ($checkEmailResult->num_rows > 0) {
            showAlert("danger", "Email <strong>$email</strong> is already used by another employee.");
            writeLog("UPDATE_FAILED", $employee_id, $first_name, $last_name, "Duplicate Email");
            $checkEmailStmt->close();
            return;
        }
        $checkEmailStmt->close();

        $sql = "UPDATE employees SET employee_id=?, first_name=?, last_name=?, job_position=?, phone_number=?, email=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $employee_id, $first_name, $last_name, $job_position, $phone_number, $email, $id);

        if ($stmt->execute()) {
            $changes = [];
            if ($oldEmployee['first_name'] !== $first_name) $changes[] = "FirstName: {$oldEmployee['first_name']} → $first_name";
            if ($oldEmployee['last_name'] !== $last_name) $changes[] = "LastName: {$oldEmployee['last_name']} → $last_name";
            if ($oldEmployee['job_position'] !== $job_position) $changes[] = "Position: {$oldEmployee['job_position']} → $job_position";
            if ($oldEmployee['phone_number'] !== $phone_number) $changes[] = "Phone: {$oldEmployee['phone_number']} → $phone_number";
            if ($oldEmployee['email'] !== $email) $changes[] = "Email: {$oldEmployee['email']} → $email";

            $changeDetails = implode(" | ", $changes);
            writeLog("UPDATE", $employee_id, $first_name, $last_name, $changeDetails);

            showAlert("success", "Employee updated successfully.");
        } else {
            showAlert("danger", "Error: " . $stmt->error);
            writeLog("UPDATE_FAILED", $employee_id, $first_name, $last_name, "Error: " . $stmt->error);
        }
        $stmt->close();
    }
}

function handleDelete() {
    global $conn;

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        $selectSql = "SELECT employee_id, first_name, last_name FROM employees WHERE id = ?";
        $selectStmt = $conn->prepare($selectSql);
        $selectStmt->bind_param("i", $id);
        $selectStmt->execute();
        $result = $selectStmt->get_result();
        $employee = $result->fetch_assoc();
        $selectStmt->close();

        if ($employee) {
            $deleteSql = "DELETE FROM employees WHERE id = ?";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bind_param("i", $id);

            if ($deleteStmt->execute()) {
                writeLog("DELETE", $employee['employee_id'], $employee['first_name'], $employee['last_name'], "Permanently removed from database");
                showAlert("success", "Employee deleted successfully.");
            } else {
                showAlert("danger", "Error: " . $deleteStmt->error);
                writeLog("DELETE_FAILED", $employee['employee_id'], $employee['first_name'], $employee['last_name'], "Error: " . $deleteStmt->error);
            }
            $deleteStmt->close();
        } else {
            showAlert("danger", "Employee not found");
            writeLog("DELETE_FAILED", "UNKNOWN", "UNKNOWN", "UNKNOWN", "Employee ID not found");
        }
    } else {
        showAlert("danger", "No ID provided");
    }
}

function showAlert($type, $message) {
    $title = ($type === 'success') ? 'Success!' : 'Error!';
    ?>
    <script>
        alert("<?php echo $title ?>\n\n<?php echo strip_tags($message); ?>");
        window.location.href = 'employee.html';
    </script>
    <?php
    exit();
}

$conn->close();
?>