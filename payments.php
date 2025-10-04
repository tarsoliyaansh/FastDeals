<?php
require_once 'inc/config.php';
require_once 'inc/auth.php';
include 'inc/header.php';

$res = $conn->query("SELECT p.*, o.order_id, o.total_amount, o.status FROM Payments p JOIN Orders o ON p.order_id=o.order_id ORDER BY p.payment_date DESC");
?>
<div style="display:flex;justify-content:space-between;align-items:center">
  <h2>Payments</h2>
</div>

<table class="table">
<tr><th>ID</th><th>Order</th><th>Amount</th><th>Mode</th><th>Status</th><th>Date</th></tr>
<?php while($r=$res->fetch_assoc()): ?>
<tr>
  <td><?=$r['payment_id']?></td>
  <td><a href="view_order.php?id=<?=$r['order_id']?>">#<?=$r['order_id']?></a></td>
  <td><?=htmlspecialchars($r['amount'])?></td>
  <td><?=htmlspecialchars($r['payment_mode'])?></td>
  <td><?=htmlspecialchars($r['payment_status'])?></td>
  <td><?=htmlspecialchars($r['payment_date'])?></td>
</tr>
<?php endwhile; ?>
</table>
<?php include 'inc/footer.php'; ?>
