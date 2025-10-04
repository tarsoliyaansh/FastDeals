<?php
require_once 'inc/config.php';
require_once 'inc/auth.php';
include 'inc/header.php';

$order_id = intval($_GET['order_id'] ?? 0);
if ($_SERVER['REQUEST_METHOD']=='POST'){
    $order_id = intval($_POST['order_id']);
    $amount = floatval($_POST['amount']);
    $mode = $_POST['payment_mode'];
    $status = $_POST['payment_status'];

    // basic insert
    $stmt = $conn->prepare("INSERT INTO Payments (order_id, payment_status, payment_mode, amount) VALUES (?,?,?,?)");
    $stmt->bind_param("issd",$order_id,$status,$mode,$amount);
    if ($stmt->execute()){
        // optionally update order status to paid
        if ($status === 'completed'){
            $up = $conn->prepare("UPDATE Orders SET status='paid' WHERE order_id=?");
            $up->bind_param("i",$order_id);
            $up->execute();
            $up->close();
        }
        $success = "Payment recorded.";
    } else {
        $error = "Error: " . $conn->error;
    }
    $stmt->close();
}

$order = null;
if ($order_id){
    $s = $conn->prepare("SELECT order_id, total_amount, status FROM Orders WHERE order_id=?");
    $s->bind_param("i",$order_id); $s->execute(); $order = $s->get_result()->fetch_assoc(); $s->close();
}
?>
<h2>Add Payment</h2>
<?php if(!empty($error)): ?><div class="notice error"><?=htmlspecialchars($error)?></div><?php endif; ?>
<?php if(!empty($success)): ?><div class="notice success"><?=htmlspecialchars($success)?></div><?php endif; ?>

<form method="post" style="max-width:420px">
  <input type="hidden" name="order_id" value="<?=htmlspecialchars($order_id)?>">
  <div class="form-group"><label>Order</label>
    <input type="text" value="<?= $order ? '#'.$order['order_id'].' (₹'.$order['total_amount'].')' : 'Select an order' ?>" readonly>
  </div>
  <div class="form-group"><input name="amount" type="number" step="0.01" placeholder="Amount" required></div>
  <div class="form-group">
    <select name="payment_mode">
      <option value="cash">Cash</option>
      <option value="card">Card</option>
      <option value="upi">UPI</option>
      <option value="other">Other</option>
    </select>
  </div>
  <div class="form-group">
    <select name="payment_status">
      <option value="completed">Completed</option>
      <option value="pending">Pending</option>
      <option value="failed">Failed</option>
    </select>
  </div>
  <button type="submit">Save Payment</button>
</form>

<?php include 'inc/footer.php'; ?>
