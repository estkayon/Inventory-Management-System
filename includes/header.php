<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inventory System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="../index.php">Inventory System</a>

        <?php if (isset($_SESSION['full_name'])): ?>
            <div class="text-white">
                <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                |
                <?php echo htmlspecialchars($_SESSION['role']); ?>
                |
                <a class="text-white" href="../auth/logout.php">Logout</a>
            </div>
        <?php endif; ?>
    </div>
</nav>

<div class="container">