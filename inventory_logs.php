<?php
require_once 'inc/config.php';
require_once 'inc/auth.php';
include 'inc/header.php';

$res = $conn->query("SELECT il.*, p.name AS product, u.name AS user FROM Inventory_Log il JOIN Products p ON il.product_id = p.product_id LEFT JOIN Users u ON il.user_id = u.user_id ORDER BY il.timestamp DESC");
?>
<h2>Inventory Logs</h2>
<table class="table">
<tr><th>Time</th><th>Product</th><th>User</th><th>Change</th><th>Qty</th></tr>
<?php while($r=$res->fetch_assoc()): ?>
<tr>
  <td><?=htmlspecialchars($r['timestamp'])?></td>
  <td><?=htmlspecialchars($r['product'])?></td>
  <td><?=htmlspecialchars($r['user'] ?? 'system')?></td>
  <td><?=htmlspecialchars($r['change_type'])?></td>
  <td><?=htmlspecialchars($r['quantity_changed'])?></td>
</tr>
<?php endwhile; ?>
</table>
<?php include 'inc/footer.php'; ?>
