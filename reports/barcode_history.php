<?php
require_once "../includes/auth.php";
checkRole(['admin', 'report_viewer']);
require_once "../config/db.php";
include "../includes/header.php";

$barcode = $_GET['barcode'] ?? '';
$data = null;
$logs = null;
$components = null;

if (!empty($barcode)) {
    $stmt = $conn->prepare("
        SELECT pb.*, p.product_name
        FROM product_barcodes pb
        JOIN products p ON pb.product_id = p.id
        WHERE pb.product_barcode = ?
    ");
    $stmt->bind_param("s", $barcode);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 1) {
        $data = $res->fetch_assoc();

        $stmt2 = $conn->prepare("
            SELECT al.*, u.full_name
            FROM activity_logs al
            JOIN users u ON al.action_by = u.id
            WHERE al.product_barcode_id = ?
            ORDER BY al.action_date ASC
        ");
        $stmt2->bind_param("i", $data['id']);
        $stmt2->execute();
        $logs = $stmt2->get_result();

        $stmt3 = $conn->prepare("
            SELECT pc.component_barcode, c.component_name, pc.tracked_at
            FROM product_components pc
            JOIN components c ON pc.component_id = c.id
            WHERE pc.product_barcode_id = ?
        ");
        $stmt3->bind_param("i", $data['id']);
        $stmt3->execute();
        $components = $stmt3->get_result();
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Barcode Wise History</h3>
        <p class="text-muted mb-0">Search and view full lifecycle of a product.</p>
    </div>

    <a href="dashboard.php" class="btn btn-secondary">← Back</a>
</div>

<!-- SEARCH FORM -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-9">
                <input 
                    type="text" 
                    name="barcode" 
                    class="form-control"
                    placeholder="Enter product barcode"
                    value="<?php echo h($barcode); ?>" 
                    required
                >
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    Search
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($data): ?>

<!-- PRODUCT INFO -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Product Information</h5>

        <p><strong>Product:</strong> <?php echo h($data['product_name']); ?></p>
        <p><strong>Barcode:</strong> <?php echo h($data['product_barcode']); ?></p>
        <p>
            <strong>Status:</strong> 
            <span class="badge bg-info"><?php echo h($data['status']); ?></span>
        </p>
    </div>
</div>

<!-- COMPONENTS -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Components</h5>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Component</th>
                        <th>Component Barcode</th>
                        <th>Tracked At</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while($row = $components->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo h($row['component_name']); ?></td>
                        <td><?php echo h($row['component_barcode']); ?></td>
                        <td><?php echo $row['tracked_at']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ACTIVITY HISTORY -->
<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Activity History</h5>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Action</th>
                        <th>Action By</th>
                        <th>Action Date</th>
                        <th>Notes</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while($row = $logs->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <span class="badge bg-secondary">
                                <?php echo h($row['action_type']); ?>
                            </span>
                        </td>
                        <td><?php echo h($row['full_name']); ?></td>
                        <td><?php echo $row['action_date']; ?></td>
                        <td><?php echo h($row['notes']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php endif; ?>

<?php include "../includes/footer.php"; ?>