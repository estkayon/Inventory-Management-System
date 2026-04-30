<?php
require_once "../includes/auth.php";
checkRole(['admin', 'receive_user']);
require_once "../config/db.php";
require_once "../includes/functions.php";
include "../includes/header.php";

$message = "";

/*
|--------------------------------------------------------------------------
| 1. RECEIVE PRODUCT BY BARCODE OR PENDING ACTION BUTTON
|--------------------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['receive_by_barcode'])) {
        $barcode = trim($_POST['product_barcode']);

        if (!empty($barcode)) {
            $stmt = $conn->prepare("SELECT * FROM product_barcodes WHERE product_barcode = ?");
            $stmt->bind_param("s", $barcode);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $data = $result->fetch_assoc();

                if ($data['status'] !== 'tracked') {
                    $message = "This product is not pending for receive.";
                } else {
                    $stmt2 = $conn->prepare("
                        UPDATE product_barcodes 
                        SET status='received',
                            current_stock_status='in_stock',
                            received_by=?,
                            received_at=NOW()
                        WHERE id=?
                    ");
                    $stmt2->bind_param("ii", $_SESSION['user_id'], $data['id']);
                    $stmt2->execute();

                    $checkStock = $conn->prepare("SELECT id FROM stock WHERE product_barcode_id = ?");
                    $checkStock->bind_param("i", $data['id']);
                    $checkStock->execute();
                    $stockResult = $checkStock->get_result();

                    if ($stockResult->num_rows == 0) {
                        $stmt3 = $conn->prepare("
                            INSERT INTO stock (product_barcode_id, stock_in, stock_out, updated_at)
                            VALUES (?, 1, 0, NOW())
                        ");
                    } else {
                        $stmt3 = $conn->prepare("
                            UPDATE stock 
                            SET stock_in = 1, stock_out = 0, updated_at = NOW()
                            WHERE product_barcode_id = ?
                        ");
                    }

                    $stmt3->bind_param("i", $data['id']);
                    $stmt3->execute();

                    logActivity($conn, $data['id'], 'received', $_SESSION['user_id'], 'Product received and stock updated');
                    $message = "Product received successfully.";
                }
            } else {
                $message = "Barcode not found.";
            }
        } else {
            $message = "Please enter a barcode.";
        }
    }

    if (isset($_POST['receive_pending'])) {
        $product_barcode_id = (int) $_POST['product_barcode_id'];

        $stmt = $conn->prepare("SELECT * FROM product_barcodes WHERE id = ?");
        $stmt->bind_param("i", $product_barcode_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $data = $result->fetch_assoc();

            if ($data['status'] !== 'tracked') {
                $message = "This product is not pending anymore.";
            } else {
                $stmt2 = $conn->prepare("
                    UPDATE product_barcodes 
                    SET status='received',
                        current_stock_status='in_stock',
                        received_by=?,
                        received_at=NOW()
                    WHERE id=?
                ");
                $stmt2->bind_param("ii", $_SESSION['user_id'], $data['id']);
                $stmt2->execute();

                $checkStock = $conn->prepare("SELECT id FROM stock WHERE product_barcode_id = ?");
                $checkStock->bind_param("i", $data['id']);
                $checkStock->execute();
                $stockResult = $checkStock->get_result();

                if ($stockResult->num_rows == 0) {
                    $stmt3 = $conn->prepare("
                        INSERT INTO stock (product_barcode_id, stock_in, stock_out, updated_at)
                        VALUES (?, 1, 0, NOW())
                    ");
                } else {
                    $stmt3 = $conn->prepare("
                        UPDATE stock 
                        SET stock_in = 1, stock_out = 0, updated_at = NOW()
                        WHERE product_barcode_id = ?
                    ");
                }

                $stmt3->bind_param("i", $data['id']);
                $stmt3->execute();

                logActivity($conn, $data['id'], 'received', $_SESSION['user_id'], 'Product received from pending list and stock updated');
                $message = "Pending product received successfully.";
            }
        } else {
            $message = "Pending product not found.";
        }
    }
}

/*
|--------------------------------------------------------------------------
| 2. FETCH PENDING PRODUCTS
|--------------------------------------------------------------------------
*/
$pendingSql = "
    SELECT 
        pb.id,
        pb.product_barcode,
        pb.tracked_at,
        p.product_name,
        rp.point_name AS receive_point_name,
        u.full_name AS tracker_name
    FROM product_barcodes pb
    INNER JOIN products p ON pb.product_id = p.id
    LEFT JOIN receive_points rp ON pb.receive_point_id = rp.id
    LEFT JOIN users u ON pb.tracked_by = u.id
    WHERE pb.status = 'tracked'
    ORDER BY pb.id DESC
";
$pendingResult = $conn->query($pendingSql);

/*
|--------------------------------------------------------------------------
| 3. FETCH STOCK DETAILS
|--------------------------------------------------------------------------
*/
$stockSql = "
    SELECT
        s.id AS stock_id,
        pb.product_barcode,
        p.product_name,
        rp.point_name AS receive_point_name,
        pb.received_at,
        u.full_name AS received_by_name,
        s.stock_in,
        s.stock_out,
        s.updated_at
    FROM stock s
    INNER JOIN product_barcodes pb ON s.product_barcode_id = pb.id
    INNER JOIN products p ON pb.product_id = p.id
    LEFT JOIN receive_points rp ON pb.receive_point_id = rp.id
    LEFT JOIN users u ON pb.received_by = u.id
    WHERE pb.current_stock_status = 'in_stock'
    ORDER BY s.updated_at DESC
";
$stockResult = $conn->query($stockSql);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Receive Product</h3>
        <p class="text-muted mb-0">Receive tracked products and update stock.</p>
    </div>

    <a href="dashboard.php" class="btn btn-secondary">← Back</a>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-info">
        <?php echo h($message); ?>
    </div>
<?php endif; ?>

<!-- RECEIVE BY BARCODE -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Receive By Barcode</h5>

        <form method="POST" class="row g-3">
            <div class="col-md-8">
                <input 
                    type="text" 
                    name="product_barcode" 
                    class="form-control"
                    placeholder="Scan or enter product barcode"
                    required
                >
            </div>

            <div class="col-md-4">
                <button type="submit" name="receive_by_barcode" class="btn btn-success w-100">
                    Receive Product
                </button>
            </div>
        </form>
    </div>
</div>

<!-- PENDING PRODUCTS -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Pending Products</h5>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Product Barcode</th>
                        <th>Receive Point</th>
                        <th>Tracked By</th>
                        <th>Tracked At</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($pendingResult && $pendingResult->num_rows > 0): ?>
                        <?php while ($row = $pendingResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo h($row['product_name']); ?></td>
                                <td><?php echo h($row['product_barcode']); ?></td>
                                <td><?php echo h($row['receive_point_name'] ?? 'N/A'); ?></td>
                                <td><?php echo h($row['tracker_name'] ?? 'N/A'); ?></td>
                                <td><?php echo h($row['tracked_at']); ?></td>
                                <td>
                                    <form method="POST" class="m-0">
                                        <input type="hidden" name="product_barcode_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="receive_pending" class="btn btn-sm btn-success">
                                            Accept / Receive
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No pending products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- STOCK DETAILS -->
<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Stock Details</h5>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Stock ID</th>
                        <th>Product</th>
                        <th>Product Barcode</th>
                        <th>Receive Point</th>
                        <th>Received By</th>
                        <th>Received At</th>
                        <th>Stock In</th>
                        <th>Stock Out</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($stockResult && $stockResult->num_rows > 0): ?>
                        <?php while ($row = $stockResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['stock_id']; ?></td>
                                <td><?php echo h($row['product_name']); ?></td>
                                <td><?php echo h($row['product_barcode']); ?></td>
                                <td><?php echo h($row['receive_point_name'] ?? 'N/A'); ?></td>
                                <td><?php echo h($row['received_by_name'] ?? 'N/A'); ?></td>
                                <td><?php echo h($row['received_at']); ?></td>
                                <td><span class="badge bg-success"><?php echo $row['stock_in']; ?></span></td>
                                <td><span class="badge bg-danger"><?php echo $row['stock_out']; ?></span></td>
                                <td><?php echo h($row['updated_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">No stock available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>