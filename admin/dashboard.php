<?php
require_once "../includes/auth.php";
checkRole(['admin']);
include "../includes/header.php";
?>

<div class="mb-4">
    <h2 class="fw-bold">Admin Dashboard</h2>
    <p class="text-muted">Manage inventory, barcode tracking, receiving, delivery, and reports.</p>
</div>

<div class="row g-4">

    <!-- Admin Management -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">Admin Management</h5>

                <div class="d-grid gap-2">
                    <a href="products.php" class="btn btn-outline-dark">Manage Products</a>
                    <a href="components.php" class="btn btn-outline-dark">Manage Components</a>
                    <a href="receive_points.php" class="btn btn-outline-dark">Manage Receive Points</a>
                    <a href="delivery_points.php" class="btn btn-outline-dark">Manage Delivery Points</a>
                    <a href="users.php" class="btn btn-outline-dark">Manage Users</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Component Tracker -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">Component Tracker</h5>

                <div class="d-grid gap-2">
                    <a href="../tracker/generate_barcode.php" class="btn btn-outline-primary">Generate Barcode</a>
                    <a href="../tracker/track_component.php" class="btn btn-outline-primary">Track Product + Components</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Receive User -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">Receive Operations</h5>

                <div class="d-grid gap-2">
                    <a href="../receive/receive_product.php" class="btn btn-outline-success">Receive Product</a>
                    <a href="../receive/send_to_delivery.php" class="btn btn-outline-success">Send To Delivery Point</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery User -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">Delivery Operations</h5>

                <div class="d-grid gap-2">
                    <a href="../delivery/deliver_product.php" class="btn btn-outline-danger">Deliver Product</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">Reports</h5>

                <div class="d-grid gap-2">
                    <a href="../reports/stock_report.php" class="btn btn-outline-info">Stock Report</a>
                    <a href="../reports/summary_report.php" class="btn btn-outline-info">Summary Report</a>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include "../includes/footer.php"; ?>