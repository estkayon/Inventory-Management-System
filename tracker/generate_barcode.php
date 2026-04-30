<?php
require_once "../includes/auth.php";
checkRole(['admin', 'component_tracker']);
require_once "../config/db.php";
require_once "../includes/functions.php";
include "../includes/header.php";

$message = "";
$generated_barcode = "";

function generateBarcode($length = 11) {
    $characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $barcode = "";

    for ($i = 0; $i < $length; $i++) {
        $barcode .= $characters[random_int(0, strlen($characters) - 1)];
    }

    return $barcode;
}

function barcodeExists($conn, $barcode) {
    $stmt = $conn->prepare("SELECT id FROM product_barcodes WHERE product_barcode = ?");
    $stmt->bind_param("s", $barcode);
    $stmt->execute();
    $result1 = $stmt->get_result();

    if ($result1->num_rows > 0) {
        return true;
    }

    $stmt2 = $conn->prepare("SELECT id FROM product_components WHERE component_barcode = ?");
    $stmt2->bind_param("s", $barcode);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if ($result2->num_rows > 0) {
        return true;
    }

    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    do {
        $generated_barcode = generateBarcode(11);
    } while (barcodeExists($conn, $generated_barcode));

    $message = "Barcode generated successfully.";
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Generate Barcode</h3>
        <p class="text-muted mb-0">Generate a unique 11-character barcode for product or component use.</p>
    </div>

    <a href="dashboard.php" class="btn btn-secondary">← Back</a>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-success">
        <?php echo h($message); ?>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Create New Barcode</h5>

        <form method="POST">
            <button type="submit" class="btn btn-primary">
                Generate New Barcode
            </button>
        </form>
    </div>
</div>

<?php if (!empty($generated_barcode)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 text-center">
            <h5 class="fw-bold mb-3">Your Generated Barcode</h5>

            <input 
                type="text" 
                class="form-control form-control-lg text-center fw-bold mb-3"
                value="<?php echo h($generated_barcode); ?>" 
                readonly 
                onclick="this.select();"
            >

            <p class="text-muted mb-0">
                Click the barcode field to select it. Then copy and use it for product barcode or component barcode.
            </p>
        </div>
    </div>
<?php endif; ?>

<?php include "../includes/footer.php"; ?>