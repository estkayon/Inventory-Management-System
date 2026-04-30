<?php
session_start();

if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: admin/dashboard.php");
            break;
        case 'component_tracker':
            header("Location: tracker/dashboard.php");
            break;
        case 'receive_user':
            header("Location: receive/dashboard.php");
            break;
        case 'delivery_user':
            header("Location: delivery/dashboard.php");
            break;
        case 'report_viewer':
            header("Location: reports/dashboard.php");
            break;
    }
    exit();
}
header("Location: auth/login.php");
exit();
?>