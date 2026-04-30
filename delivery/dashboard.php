<?php
require_once "../includes/auth.php";
checkRole(['admin', 'delivery_user']);
include "../includes/header.php";
?>

<h3>Delivery User Dashboard</h3>
<ul>
    <li><a href="deliver_product.php">Deliver Product</a></li>
</ul>

<?php include "../includes/footer.php"; ?>