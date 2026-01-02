<?php
date_default_timezone_set('Asia/Manila');

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'kd_sportswear';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$action = isset($_GET['action']) ? $_GET['action'] : 'read_inquiries';

switch ($action) {
    case 'read_inquiries':
        readInquiries();
        break;
    case 'read_orders':
        readOrders();
        break;
    default:
        readInquiries();
}

function readInquiries() {
    global $conn;

    $sql = "SELECT inquiry_id, design_name, inquiry_date, design_file, initial_price, status, customer_id, employee_id FROM inquiry ORDER BY inquiry_id DESC";
    $result = $conn->query($sql);

    $html = '';

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $statusClass = getStatusClass($row['status']);
            $fileDisplay = !empty($row['design_file']) ? '<a href="' . htmlspecialchars($row['design_file']) . '" target="_blank" class="btn-link">View</a>' : '<span class="text-muted">-</span>';
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($row['inquiry_id']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['design_name']) . '</td>';
            $html .= '<td>' . date('M d, Y', strtotime($row['inquiry_date'])) . '</td>';
            $html .= '<td>₱' . number_format($row['initial_price'], 2) . '</td>';
            $html .= '<td><span class="badge ' . $statusClass . '">' . htmlspecialchars($row['status']) . '</span></td>';
            $html .= '<td>' . $fileDisplay . '</td>';
            $html .= '<td>';
            $html .= '<div class="action-buttons">';
            $html .= '<a href="../Duro_FORM/payment.html?inquiry_id=' . $row['inquiry_id'] . '" class="btn btn-sm btn-info">Payment</a>';
            $html .= '</div>';
            $html .= '</td>';
            $html .= '</tr>';
        }
    } else {
        $html = '<tr><td colspan="7" class="text-center text-muted">No inquiries found.</td></tr>';
    }

    echo $html;
}

function readOrders() {
    global $conn;

    $sql = "SELECT order_id, date, status, inquiry_id, employee_id FROM orders ORDER BY order_id DESC";
    $result = $conn->query($sql);

    $html = '';

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $statusClass = getStatusClass($row['status']);
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($row['order_id']) . '</td>';
            $html .= '<td>' . date('M d, Y', strtotime($row['date'])) . '</td>';
            $html .= '<td><span class="badge ' . $statusClass . '">' . htmlspecialchars($row['status']) . '</span></td>';
            $html .= '<td>' . htmlspecialchars($row['inquiry_id']) . '</td>';
            $html .= '<td>';
            $html .= '<div class="action-buttons">';
            $html .= '<a href="../Duro_FORM/payment.html?order_id=' . $row['order_id'] . '" class="btn btn-sm btn-info">Payment</a>';
            $html .= '</div>';
            $html .= '</td>';
            $html .= '</tr>';
        }
    } else {
        $html = '<tr><td colspan="5" class="text-center text-muted">No orders found.</td></tr>';
    }

    echo $html;
}

function getStatusClass($status) {
    switch($status) {
        case 'pending':
            return 'bg-warning text-dark';
        case 'approved':
            return 'bg-info';
        case 'completed':
            return 'bg-success';
        case 'cancelled':
            return 'bg-danger';
        case 'in_production':
            return 'bg-primary';
        default:
            return 'bg-secondary';
    }
}

$conn->close();
?>