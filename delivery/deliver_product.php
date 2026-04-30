<?php
require_once "../includes/auth.php";
checkRole(['admin', 'delivery_user']);
require_once "../config/db.php";
require_once "../includes/functions.php";
include "../includes/header.php";

$message = "";

/*
|--------------------------------------------------------------------------
| 1. DELIVER PRODUCT BY BARCODE OR PENDING ACTION BUTTON
|--------------------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // deliver from barcode input
    if (isset($_POST['deliver_by_barcode'])) {
        $barcode = trim($_POST['product_barcode']);

        if (!empty($barcode)) {
            $stmt = $conn->prepare("
                SELECT * 
                FROM product_barcodes 
                WHERE product_barcode = ? 
                  AND status = 'sent_to_delivery'
            ");
            $stmt->bind_param("s", $barcode);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $data = $result->fetch_assoc();

                // mark as delivered
                $stmt2 = $conn->prepare("
                    UPDATE product_barcodes 
                    SET status='delivered',
                        current_stock_status='out_of_stock',
                        delivered_by=?,
                        delivered_at=NOW()
                    WHERE id=?
                ");
                $stmt2->bind_param("ii", $_SESSION['user_id'], $data['id']);
                $stmt2->execute();

                // update stock table
                $checkStock = $conn->prepare("SELECT id FROM stock WHERE product_barcode_id = ?");
                $checkStock->bind_param("i", $data['id']);
                $checkStock->execute();
                $stockResult = $checkStock->get_result();

                if ($stockResult->num_rows == 0) {
                    $stmt3 = $conn->prepare("
                        INSERT INTO stock (product_barcode_id, stock_in, stock_out, updated_at)
                        VALUES (?, 0, 1, NOW())
                    ");
                    $stmt3->bind_param("i", $data['id']);
                    $stmt3->execute();
                } else {
                    $stmt3 = $conn->prepare("
                        UPDATE stock 
                        SET stock_in=0, stock_out=1, updated_at=NOW()
                        WHERE product_barcode_id=?
                    ");
                    $stmt3->bind_param("i", $data['id']);
                    $stmt3->execute();
                }

                logActivity($conn, $data['id'], 'delivered', $_SESSION['user_id'], 'Product delivered and stock updated');
                $message = "Product delivered successfully.";
            } else {
                $message = "Product not found or not ready for delivery.";
            }
        } else {
            $message = "Please enter a barcode.";
        }
    }

    // deliver from pending table button
    if (isset($_POST['deliver_pending'])) {
        $product_barcode_id = (int) $_POST['product_barcode_id'];

        $stmt = $conn->prepare("
            SELECT * 
            FROM product_barcodes 
            WHERE id = ? 
              AND status = 'sent_to_delivery'
        ");
        $stmt->bind_param("i", $product_barcode_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $data = $result->fetch_assoc();

            // mark as delivered
            $stmt2 = $conn->prepare("
                UPDATE product_barcodes 
                SET status='delivered',
                    current_stock_status='out_of_stock',
                    delivered_by=?,
                    delivered_at=NOW()
                WHERE id=?
            ");
            $stmt2->bind_param("ii", $_SESSION['user_id'], $data['id']);
            $stmt2->execute();

            // update stock table
            $checkStock = $conn->prepare("SELECT id FROM stock WHERE product_barcode_id = ?");
            $checkStock->bind_param("i", $data['id']);
            $checkStock->execute();
            $stockResult = $checkStock->get_result();

            if ($stockResult->num_rows == 0) {
                $stmt3 = $conn->prepare("
                    INSERT INTO stock (product_barcode_id, stock_in, stock_out, updated_at)
                    VALUES (?, 0, 1, NOW())
                ");
                $stmt3->bind_param("i", $data['id']);
                $stmt3->execute();
            } else {
                $stmt3 = $conn->prepare("
                    UPDATE stock 
                    SET stock_in=0, stock_out=1, updated_at=NOW()
                    WHERE product_barcode_id=?
                ");
                $stmt3->bind_param("i", $data['id']);
                $stmt3->execute();
            }

            logActivity($conn, $data['id'], 'delivered', $_SESSION['user_id'], 'Product delivered from pending delivery list and stock updated');
            $message = "Pending delivery product delivered successfully.";
        } else {
            $message = "This product is not pending for delivery.";
        }
    }
}

/*
|--------------------------------------------------------------------------
| 2. FETCH PENDING DELIVERY PRODUCTS
|--------------------------------------------------------------------------
| Pending delivery = already sent from receive section to delivery point
*/
$pendingSql = "
    SELECT
        pb.id,
        pb.product_barcode,
        pb.sent_to_delivery_at,
        p.product_name,
        rp.point_name AS receive_point_name,
        dp.point_name AS delivery_point_name,
        u.full_name AS sent_by_name
    FROM product_barcodes pb
    INNER JOIN products p ON pb.product_id = p.id
    LEFT JOIN receive_points rp ON pb.receive_point_id = rp.id
    LEFT JOIN delivery_points dp ON pb.delivery_point_id = dp.id
    LEFT JOIN users u ON pb.sent_to_delivery_by = u.id
    WHERE pb.status = 'sent_to_delivery'
    ORDER BY pb.id DESC
";
$pendingResult = $conn->query($pendingSql);

/*
|--------------------------------------------------------------------------
| 3. FETCH STOCK DETAILS
|--------------------------------------------------------------------------
| Show products that are still pending for delivery / in transit
*/
$stockSql = "
    SELECT
        s.id AS stock_id,
        pb.id AS product_barcode_id,
        pb.product_barcode,
        p.product_name,
        rp.point_name AS receive_point_name,
        dp.point_name AS delivery_point_name,
        pb.sent_to_delivery_at,
        u.full_name AS sent_by_name,
        pb.status,
        pb.current_stock_status,
        s.stock_in,
        s.stock_out,
        s.updated_at
    FROM stock s
    INNER JOIN product_barcodes pb ON s.product_barcode_id = pb.id
    INNER JOIN products p ON pb.product_id = p.id
    LEFT JOIN receive_points rp ON pb.receive_point_id = rp.id
    LEFT JOIN delivery_points dp ON pb.delivery_point_id = dp.id
    LEFT JOIN users u ON pb.sent_to_delivery_by = u.id
    WHERE pb.status = 'sent_to_delivery'
    ORDER BY s.updated_at DESC, pb.id DESC
";
$stockResult = $conn->query($stockSql);
?>

<h3>Deliver Product</h3>

<?php if (!empty($message)): ?>
    <p><?php echo h($message); ?></p>
<?php endif; ?>

<!-- DELIVER BY BARCODE -->
<h4>Deliver By Scanning / Entering Barcode</h4>
<form method="POST">
    <label>Scan / Enter Product Barcode</label><br>
    <input type="text" name="product_barcode" required>
    <button type="submit" name="deliver_by_barcode">Deliver</button>
</form>

<hr>

<!-- PENDING DELIVERY PRODUCTS -->
<h4>Pending Delivery Products</h4>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Product</th>
        <th>Product Barcode</th>
        <th>Receive Point</th>
        <th>Delivery Point</th>
        <th>Sent By</th>
        <th>Sent To Delivery At</th>
    </tr>
        <!-- <th>Action</th> -->

    <?php if ($pendingResult && $pendingResult->num_rows > 0): ?>
        <?php while ($row = $pendingResult->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo h($row['product_name']); ?></td>
                <td><?php echo h($row['product_barcode']); ?></td>
                <td><?php echo h($row['receive_point_name'] ?? 'N/A'); ?></td>
                <td><?php echo h($row['delivery_point_name'] ?? 'N/A'); ?></td>
                <td><?php echo h($row['sent_by_name'] ?? 'N/A'); ?></td>
                <td><?php echo h($row['sent_to_delivery_at']); ?></td>
                <!-- <td>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="product_barcode_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="deliver_pending">Accept / Deliver</button>
                    </form>
                </td> -->
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="8">No pending delivery products found.</td>
        </tr>
    <?php endif; ?>
</table>

<hr>

<?php include "../includes/footer.php"; ?>