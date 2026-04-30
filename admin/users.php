<?php
require_once "../includes/auth.php";
checkRole(['admin']);
require_once "../config/db.php";
include "../includes/header.php";

$result = $conn->query("SELECT id, employee_id, full_name, username, role, created_at FROM users ORDER BY id DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold">Manage Users</h3>
    <a href="dashboard.php" class="btn btn-secondary">← Back</a>
</div>

<!-- Users Table -->
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="mb-3">User List</h5>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Employee ID</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo h($row['employee_id']); ?></td>
                            <td><?php echo h($row['full_name']); ?></td>
                            <td><?php echo h($row['username']); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo match($row['role']) {
                                        'admin' => 'dark',
                                        'component_tracker' => 'primary',
                                        'receive_user' => 'success',
                                        'delivery_user' => 'danger',
                                        'report_viewer' => 'info',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php echo h($row['role']); ?>
                                </span>
                            </td>
                            <td><?php echo $row['created_at']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php include "../includes/footer.php"; ?>