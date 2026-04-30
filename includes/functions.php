<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function redirectByRole($role) {
    switch ($role) {
        case 'admin':
            header("Location: ../admin/dashboard.php");
            break;
        case 'component_tracker':
            header("Location: ../tracker/dashboard.php");
            break;
        case 'receive_user':
            header("Location: ../receive/dashboard.php");
            break;
        case 'delivery_user':
            header("Location: ../delivery/dashboard.php");
            break;
        case 'report_viewer':
            header("Location: ../reports/dashboard.php");
            break;
        default:
            header("Location: ../index.php");
    }
    exit();
}

function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../auth/login.php");
        exit();
    }
}

function checkRole($allowedRoles = []) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
        die("Access Denied");
    }
}

function logActivity($conn, $product_barcode_id, $action_type, $action_by, $notes = null) {
    $stmt = $conn->prepare("INSERT INTO activity_logs (product_barcode_id, action_type, action_by, action_date, notes) VALUES (?, ?, ?, NOW(), ?)");
    $stmt->bind_param("isis", $product_barcode_id, $action_type, $action_by, $notes);
    $stmt->execute();
}

function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>