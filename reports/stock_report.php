<?php
require_once "../includes/auth.php";
checkRole(['admin', 'report_viewer']);
require_once "../config/db.php";
include "../includes/header.php";

$sql = "
SELECT 
    pb.product_barcode,
    p.product_name,
    pb.status,
    pb.current_stock_status,
    rp.point_name AS receive_point,
    dp.point_name AS delivery_point,
    pb.tracked_at,
    pb.received_at,
    pb.delivered_at
FROM product_barcodes pb
JOIN products p ON pb.product_id = p.id
LEFT JOIN receive_points rp ON pb.receive_point_id = rp.id
LEFT JOIN delivery_points dp ON pb.delivery_point_id = dp.id
ORDER BY pb.id DESC
";

$result = $conn->query($sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Stock Report</h3>
        <p class="text-muted mb-0">View full product lifecycle and stock status.</p>
    </div>

    <a href="dashboard.php" class="btn btn-secondary">← Back</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Barcode</th>
                        <th>Product</th>
                        <th>Status</th>
                        <th>Stock</th>
                        <th>Receive Point</th>
                        <th>Delivery Point</th>
                        <th>Tracked</th>
                        <th>Received</th>
                        <th>Delivered</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo h($row['product_barcode']); ?></td>
                            <td><?php echo h($row['product_name']); ?></td>

                            <td>
                                <span class="badge bg-<?php 
                                    echo match($row['status']) {
                                        'tracked' => 'secondary',
                                        'received' => 'success',
                                        'sent_to_delivery' => 'primary',
                                        'delivered' => 'dark',
                                        default => 'light'
                                    };
                                ?>">
                                    <?php echo h($row['status']); ?>
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-<?php 
                                    echo $row['current_stock_status'] === 'in_stock' ? 'success' : 'danger';
                                ?>">
                                    <?php echo h($row['current_stock_status']); ?>
                                </span>
                            </td>

                            <td><?php echo h($row['receive_point'] ?? 'N/A'); ?></td>
                            <td><?php echo h($row['delivery_point'] ?? 'N/A'); ?></td>
                            <td><?php echo $row['tracked_at']; ?></td>
                            <td><?php echo $row['received_at']; ?></td>
                            <td><?php echo $row['delivered_at']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>

    </div>
</div>

<?php include "../includes/footer.php"; ?>