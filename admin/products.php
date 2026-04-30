<?php
require_once "../includes/auth.php";
checkRole(['admin']);
require_once "../config/db.php";
include "../includes/header.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = trim($_POST['product_name']);

    if (!empty($product_name)) {
        $stmt = $conn->prepare("INSERT INTO products (product_name) VALUES (?)");
        $stmt->bind_param("s", $product_name);
        $stmt->execute();

        $message = "Product added successfully.";
    }
}

$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold">Manage Products</h3>
    <a href="dashboard.php" class="btn btn-secondary">← Back</a>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-success">
        <?php echo h($message); ?>
    </div>
<?php endif; ?>

<!-- Add Product Form -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" name="product_name" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>
</div>

<!-- Product Table -->
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="mb-3">Product List</h5>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo h($row['product_name']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php include "../includes/footer.php"; ?>