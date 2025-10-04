<?php
require_once 'inc/config.php';
require_once 'inc/auth.php';
include 'inc/header.php';
$id = intval($_GET['id'] ?? 0);
if (!$id){ echo "<div class='notice error'>Invalid order id</div>"; include 'inc/footer.php'; exit; }

$stmt = $conn->prepare("SELECT o.*, u.name AS customer FROM Orders o LEFT JOIN Users u ON o.user_id=u.user_id WHERE o.order_id=?");
$stmt->bind_param("i",$id); $stmt->execute(); $order = $stmt->get_result()->fetch_assoc(); $stmt->close();
if (!$order){ echo "<div class='notice error'>Order not found</div>"; include 'inc/footer.php'; exit; }

$items = $conn->prepare("SELECT oi.*, p.name FROM Order_Items oi JOIN Products p ON oi.product_id = p.product_id WHERE order_id=?");
$items->bind_param("i",$id); $items->execute(); $itemsRes = $items->get_result(); $items->close();
?>
<h2>Order #<?=$order['order_id']?></h2>
<p><strong>Date:</strong> <?=$order['order_date']?> &nbsp; <strong>Customer:</strong> <?=htmlspecialchars($order['customer'] ?? 'Guest')?></p>
<table class="table">
  <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
  <?php while($it=$itemsRes->fetch_assoc()): ?>
    <tr>
      <td><?=htmlspecialchars($it['name'])?></td>
      <td><?=htmlspecialchars($it['quantity'])?></td>
      <td><?=htmlspecialchars($it['price'])?></td>
      <td><?=number_format($it['quantity']*$it['price'],2)?></td>
    </tr>
  <?php endwhile; ?>
  <tr><td colspan="3"><strong>Total</strong></td><td><strong><?=number_format($order['total_amount'],2)?></strong></td></tr>
</table>

<!-- link to payments -->
<p style="margin-top:12px"><a href="add_payment.php?order_id=<?=$order['order_id']?>"><button>Add Payment</button></a></p>

<?php include 'inc/footer.php'; ?>
