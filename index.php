<?php
require_once 'inc/config.php';
require_once 'inc/auth.php';
include 'inc/header.php';

// stats
$stats = [];
$q = $conn->query("SELECT COUNT(*) AS c FROM Products");
$stats['products'] = $q->fetch_assoc()['c'] ?? 0;
$q = $conn->query("SELECT COUNT(*) AS c FROM Orders");
$stats['orders'] = $q->fetch_assoc()['c'] ?? 0;
$q = $conn->query("SELECT COUNT(*) AS c FROM Users");
$stats['users'] = $q->fetch_assoc()['c'] ?? 0;

$recentProducts = $conn->query("SELECT product_id,name,price,stock_quantity FROM Products ORDER BY created_at DESC LIMIT 5");
$recentOrders = $conn->query("SELECT order_id,order_date,total_amount,status FROM Orders ORDER BY order_date DESC LIMIT 5");
?>
<h2>Dashboard</h2>
<div class="grid" style="margin-bottom:12px">
  <div class="card"><strong>Products</strong><div style="font-size:28px"><?=$stats['products']?></div></div>
  <div class="card"><strong>Orders</strong><div style="font-size:28px"><?=$stats['orders']?></div></div>
  <div class="card"><strong>Users</strong><div style="font-size:28px"><?=$stats['users']?></div></div>
</div>

<div style="display:flex;gap:12px;flex-wrap:wrap;">
  <div style="flex:1;min-width:320px;">
    <h3>Recent Products</h3>
    <table class="table">
      <tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th></tr>
      <?php while($p=$recentProducts->fetch_assoc()): ?>
        <tr>
          <td><?=htmlspecialchars($p['product_id'])?></td>
          <td><?=htmlspecialchars($p['name'])?></td>
          <td><?=htmlspecialchars($p['price'])?></td>
          <td><?=htmlspecialchars($p['stock_quantity'])?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>

  <div style="flex:1;min-width:320px;">
    <h3>Recent Orders</h3>
    <table class="table">
      <tr><th>Order</th><th>Date</th><th>Total</th><th>Status</th></tr>
      <?php while($o=$recentOrders->fetch_assoc()): ?>
        <tr>
          <td><a href="view_order.php?id=<?=$o['order_id']?>">#<?=$o['order_id']?></a></td>
          <td><?=htmlspecialchars($o['order_date'])?></td>
          <td><?=htmlspecialchars($o['total_amount'])?></td>
          <td><?=htmlspecialchars($o['status'])?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>
<?php include 'inc/footer.php'; ?>
