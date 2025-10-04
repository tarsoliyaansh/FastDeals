<?php
require_once 'inc/config.php';
require_once 'inc/auth.php';
include 'inc/header.php';

$res = $conn->query("SELECT o.order_id,o.order_date,o.total_amount,o.status,u.name AS customer FROM Orders o LEFT JOIN Users u ON o.user_id=u.user_id ORDER BY o.order_date DESC");
?>
<div style="display:flex;justify-content:space-between;align-items:center">
  <h2>Orders</h2>
  <a href="create_order.php"><button>Create Order</button></a>
</div>

<table class="table">
<tr><th>#</th><th>Date</th><th>Customer</th><th>Total</th><th>Status</th><th>Action</th></tr>
<?php while($r=$res->fetch_assoc()): ?>
<tr>
  <td><?=$r['order_id']?></td>
  <td><?=htmlspecialchars($r['order_date'])?></td>
  <td><?=htmlspecialchars($r['customer'] ?? '-')?></td>
  <td><?=htmlspecialchars($r['total_amount'])?></td>
  <td><?=htmlspecialchars($r['status'])?></td>
  <td><a href="view_order.php?id=<?=$r['order_id']?>"><button>View</button></a></td>
</tr>
<?php endwhile; ?>
</table>
<?php include 'inc/footer.php'; ?>
