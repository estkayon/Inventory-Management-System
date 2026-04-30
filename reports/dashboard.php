<?php
require_once "../includes/auth.php";
checkRole(['admin', 'report_viewer']);
include "../includes/header.php";
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Reports Dashboard</h3>
        <p class="text-muted mb-0">View stock reports and summary analytics.</p>
    </div>

    <!-- <a href="../index.php" class="btn btn-secondary">← Back</a> -->
</div>

<div class="row g-4">

    <!-- Stock Report -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <h5 class="fw-bold mb-3">Stock Report</h5>
                <p class="text-muted">View current stock availability and status.</p>

                <a href="stock_report.php" class="btn btn-primary w-100">
                    Open Stock Report
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Report -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <h5 class="fw-bold mb-3">Summary Report</h5>
                <p class="text-muted">View overall system activity and summary.</p>

                <a href="summary_report.php" class="btn btn-success w-100">
                    Open Summary Report
                </a>
            </div>
        </div>
    </div>

    
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <h5 class="fw-bold mb-3">Barcode History</h5>
                <p class="text-muted">Track full lifecycle of a product.</p>

                <a href="barcode_history.php" class="btn btn-info w-100">
                    View History
                </a>
            </div>
        </div>
    </div>
   

</div>

<?php include "../includes/footer.php"; ?>