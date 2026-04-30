<?php
require_once "../includes/auth.php";
checkRole(['admin', 'receive_user']);
include "../includes/header.php";
?>

<div class="mb-4">
    <h2 class="fw-bold">Receive User Dashboard</h2>
    <p class="text-muted">Receive tracked products and send available stock to delivery point.</p>
</div>

<div class="row g-4">

    <!-- Receive Product -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <h5 class="card-title fw-bold mb-3">Receive Product</h5>
                <p class="text-muted">Scan or enter product barcode to receive product and update stock.</p>

                <a href="receive_product.php" class="btn btn-success w-100">
                    Receive Product
                </a>
            </div>
        </div>
    </div>

    <!-- Send To Delivery -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <h5 class="card-title fw-bold mb-3">Send To Delivery</h5>
                <p class="text-muted">Send received stock to the selected delivery point.</p>

                <a href="send_to_delivery.php" class="btn btn-primary w-100">
                    Send Product To Delivery Point
                </a>
            </div>
        </div>
    </div>

</div>

<?php include "../includes/footer.php"; ?>