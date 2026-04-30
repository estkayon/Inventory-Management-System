<?php
require_once "../includes/auth.php";
checkRole(['admin', 'component_tracker']);
include "../includes/header.php";
?>

<div class="mb-4">
    <h2 class="fw-bold">Component Tracker Dashboard</h2>
    <p class="text-muted">Generate barcodes and track products with components.</p>
</div>

<div class="row g-4">

    <!-- Generate Barcode -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <h5 class="card-title fw-bold mb-3">Generate Barcode</h5>
                <p class="text-muted">Create unique 11-digit barcodes for products and components.</p>

                <a href="generate_barcode.php" class="btn btn-primary w-100">
                    Generate Barcode
                </a>
            </div>
        </div>
    </div>

    <!-- Track Product -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <h5 class="card-title fw-bold mb-3">Track Product</h5>
                <p class="text-muted">Assign product barcode and attach component barcodes.</p>

                <a href="track_component.php" class="btn btn-success w-100">
                    Track Product + Components
                </a>
            </div>
        </div>
    </div>

</div>

<?php include "../includes/footer.php"; ?>