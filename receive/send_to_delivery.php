<?php
require_once "../includes/auth.php";
checkRole(['admin', 'receive_user']);
require_once "../config/db.php";
require_once "../includes/functions.php";
include "../includes/header.php";

$message = "";

/*
|--------------------------------------------------------------------------
| 1. FETCH DELIVERY POINTS
|--------------------------------------------------------------------------
*/
$delivery_points = $conn->query("SELECT * FROM delivery_points ORDER BY point_name ASC");

/*
|--------------------------------------------------------------------------
| 2. SEND PRODUCT TO DELIVERY
|--------------------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $barcode = trim($_POST['product_barcode']);
    $delivery_point_id = (int) $_POST['delivery_point_id'];

    if (!empty($barcode) && !empty($delivery_point_id)) {
        $stmt = $conn->prepare("
            SELECT * 
            FROM product_barcodes 
            WHERE product_barcode = ? 
              AND status = 'received'
              AND current_stock_status = 'in_stock'
        ");
        $stmt->bind_param("s", $barcode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $data = $result->fetch_assoc();

            $stmt2 = $conn->prepare("
                UPDATE product_barcodes 
                SET status='sent_to_delivery',
                    delivery_point_id=?,
                    sent_to_delivery_by=?,
                    sent_to_delivery_at=NOW()
                WHERE id=?
            ");
            $stmt2->bind_param("iii", $delivery_point_id, $_SESSION['user_id'], $data['id']);
            $stmt2->execute();

            logActivity($conn, $data['id'], 'sent_to_delivery', $_SESSION['user_id'], 'Product sent to delivery point');
            $message = "Product sent to delivery point successfully.";
        } else {
            $message = "Product must be received and in stock first.";
        }
    } else {
        $message = "Please fill all fields.";
    }
}

/*
|--------------------------------------------------------------------------
| 3. FETCH STOCK DETAILS
|--------------------------------------------------------------------------
*/
$stockSql = "
    SELECT
        pb.id,
        pb.product_barcode,
        p.product_name,
        rp.point_name AS receive_point_name,
        pb.received_at,
        u.full_name AS received_by_name,
        pb.status,
        pb.current_stock_status
    FROM product_barcodes pb
    INNER JOIN products p ON pb.product_id = p.id
    LEFT JOIN receive_points rp ON pb.receive_point_id = rp.id
    LEFT JOIN users u ON pb.received_by = u.id
    WHERE pb.status = 'received'
      AND pb.current_stock_status = 'in_stock'
    ORDER BY pb.received_at DESC, pb.id DESC
";

$stockResult = $conn->query($stockSql);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Send Product To Delivery Point</h3>
        <p class="text-muted mb-0">Send received stock to a selected delivery point.</p>
    </div>

    <a href="dashboard.php" class="btn btn-secondary">← Back</a>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-info">
        <?php echo h($message); ?>
    </div>
<?php endif; ?>

<!-- SEND FORM -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Send By Barcode</h5>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Product Barcode</label>
                <input 
                    type="text" 
                    name="product_barcode" 
                    class="form-control"
                    placeholder="Scan or enter product barcode"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Delivery Point</label>
                <select name="delivery_point_id" class="form-select" required>
                    <option value="">Select Delivery Point</option>
                    <?php
                    $delivery_points = $conn->query("SELECT * FROM delivery_points ORDER BY point_name ASC");
                    while($row = $delivery_points->fetch_assoc()):
                    ?>
                        <option value="<?php echo $row['id']; ?>">
                            <?php echo h($row['point_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                Send To Delivery
            </button>
        </form>
    </div>
</div>

<!-- STOCK DETAILS -->
<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Available Stock Details</h5>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Product Barcode</th>
                        <th>Receive Point</th>
                        <th>Received By</th>
                        <th>Received At</th>
                        <th>Status</th>
                        <th>Stock Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($stockResult && $stockResult->num_rows > 0): ?>
                        <?php while($row = $stockResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo h($row['product_name']); ?></td>
                                <td><?php echo h($row['product_barcode']); ?></td>
                                <td><?php echo h($row['receive_point_name'] ?? 'N/A'); ?></td>
                                <td><?php echo h($row['received_by_name'] ?? 'N/A'); ?></td>
                                <td><?php echo h($row['received_at']); ?></td>
                                <td>
                                    <span class="badge bg-success">
                                        <?php echo h($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?php echo h($row['current_stock_status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No stock available to send.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php include "../includes/footer.php"; ?>