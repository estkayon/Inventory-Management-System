<?php
require_once "../includes/auth.php";
checkRole(['admin', 'component_tracker']);
require_once "../config/db.php";
require_once "../includes/functions.php";
include "../includes/header.php";

$message = "";
$message_type = "";

$products = $conn->query("SELECT * FROM products ORDER BY product_name ASC");
$receive_points = $conn->query("SELECT * FROM receive_points ORDER BY point_name ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $product_barcode = trim($_POST['product_barcode']);
    $receive_point_id = $_POST['receive_point_id'];
    $component_ids = $_POST['component_id'] ?? [];
    $component_barcodes = $_POST['component_barcode'] ?? [];

    $stmt = $conn->prepare("
        INSERT INTO product_barcodes 
        (product_id, product_barcode, receive_point_id, status, tracked_by, tracked_at) 
        VALUES (?, ?, ?, 'tracked', ?, NOW())
    ");
    $stmt->bind_param("isii", $product_id, $product_barcode, $receive_point_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $product_barcode_id = $conn->insert_id;

        for ($i = 0; $i < count($component_ids); $i++) {
            if (!empty($component_ids[$i]) && !empty($component_barcodes[$i])) {
                $stmt2 = $conn->prepare("
                    INSERT INTO product_components 
                    (product_barcode_id, component_id, component_barcode, tracked_by, tracked_at) 
                    VALUES (?, ?, ?, ?, NOW())
                ");
                $stmt2->bind_param("iisi", $product_barcode_id, $component_ids[$i], $component_barcodes[$i], $_SESSION['user_id']);
                $stmt2->execute();
            }
        }

        logActivity($conn, $product_barcode_id, 'tracked', $_SESSION['user_id'], 'Product tracked with components and sent to receive point');

        $message = "Tracking completed successfully.";
        $message_type = "success";
    } else {
        $message = "Error: " . $conn->error;
        $message_type = "danger";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Track Product + Components</h3>
        <p class="text-muted mb-0">Assign a product barcode, component barcodes, and send to receive point.</p>
    </div>

    <a href="dashboard.php" class="btn btn-secondary">← Back</a>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $message_type; ?>">
        <?php echo h($message); ?>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">

        <form method="POST">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Product</label>
                    <select name="product_id" class="form-select" required>
                        <option value="">Select Product</option>
                        <?php while($row = $products->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo h($row['product_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Product Barcode</label>
                    <input type="text" name="product_barcode" class="form-control" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Receive Point</label>
                <select name="receive_point_id" class="form-select" required>
                    <option value="">Select Receive Point</option>
                    <?php while($row = $receive_points->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>">
                            <?php echo h($row['point_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <h5 class="fw-bold mb-3">Components</h5>

            <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="card bg-light border-0 mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold">Component <?php echo $i; ?></h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Component Name</label>
                                <select name="component_id[]" class="form-select">
                                    <option value="">Select Component</option>
                                    <?php
                                    $components_data = $conn->query("SELECT * FROM components ORDER BY component_name ASC");
                                    while($row = $components_data->fetch_assoc()):
                                    ?>
                                        <option value="<?php echo $row['id']; ?>">
                                            <?php echo h($row['component_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Component Barcode</label>
                                <input 
                                    type="text" 
                                    name="component_barcode[]" 
                                    class="form-control" 
                                    placeholder="Enter component barcode"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>

            <button type="submit" class="btn btn-primary">
                Save Tracking
            </button>

        </form>

    </div>
</div>

<?php include "../includes/footer.php"; ?>