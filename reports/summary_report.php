<?php
require_once "../includes/auth.php";
checkRole(['admin', 'report_viewer']);
require_once "../config/db.php";
include "../includes/header.php";

$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

$where = "";
$params = [];
$types = "";

if (!empty($from_date) && !empty($to_date)) {
    $where = " WHERE DATE(created_at) BETWEEN ? AND ? ";
    $params[] = $from_date;
    $params[] = $to_date;
    $types = "ss";
}

$sqlTracked = "SELECT COUNT(*) as total_tracked FROM product_barcodes" . $where;
$sqlReceived = "SELECT COUNT(*) as total_received FROM product_barcodes " . ($where ? str_replace("created_at", "received_at", $where) : " WHERE status='received' OR status='sent_to_delivery' OR status='delivered'");
$sqlDelivered = "SELECT COUNT(*) as total_delivered FROM product_barcodes " . ($where ? str_replace("created_at", "delivered_at", $where) : " WHERE status='delivered'");

function getCountData($conn, $sql, $types = "", $params = []) {
    $stmt = $conn->prepare($sql);
    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

$tracked = getCountData($conn, $sqlTracked, $types, $params);
$received = getCountData($conn, $sqlReceived, $types, $params);
$delivered = getCountData($conn, $sqlDelivered, $types, $params);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Summary Report</h3>
        <p class="text-muted mb-0">View tracked, received, and delivered product summary by date.</p>
    </div>

    <a href="dashboard.php" class="btn btn-secondary">← Back</a>
</div>

<!-- FILTER FORM -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">From Date</label>
                <input 
                    type="date" 
                    name="from_date" 
                    class="form-control"
                    value="<?php echo h($from_date); ?>"
                >
            </div>

            <div class="col-md-5">
                <label class="form-label">To Date</label>
                <input 
                    type="date" 
                    name="to_date" 
                    class="form-control"
                    value="<?php echo h($to_date); ?>"
                >
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SUMMARY CARDS -->
<div class="row g-4 mb-4">

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <h6 class="text-muted">Total Tracked</h6>
                <h2 class="fw-bold"><?php echo $tracked['total_tracked'] ?? 0; ?></h2>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <h6 class="text-muted">Total Received</h6>
                <h2 class="fw-bold"><?php echo $received['total_received'] ?? 0; ?></h2>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <h6 class="text-muted">Total Delivered</h6>
                <h2 class="fw-bold"><?php echo $delivered['total_delivered'] ?? 0; ?></h2>
            </div>
        </div>
    </div>

</div>

<!-- SUMMARY TABLE -->
<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Summary Details</h5>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Report Type</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>Total Tracked</td>
                        <td><?php echo $tracked['total_tracked'] ?? 0; ?></td>
                    </tr>
                    <tr>
                        <td>Total Received</td>
                        <td><?php echo $received['total_received'] ?? 0; ?></td>
                    </tr>
                    <tr>
                        <td>Total Delivered</td>
                        <td><?php echo $delivered['total_delivered'] ?? 0; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php include "../includes/footer.php"; ?>