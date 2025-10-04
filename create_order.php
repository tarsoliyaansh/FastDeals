<?php
require_once 'inc/config.php';
require_once 'inc/auth.php';
include 'inc/header.php';

$products = $conn->query("SELECT product_id,name,price,stock_quantity FROM Products WHERE stock_quantity > 0 ORDER BY name ASC");
$users = $conn->query("SELECT user_id,name FROM Users ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD']=='POST'){
    $user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : null;
    // collect items: arrays product_id[] qty[]
    $pids = $_POST['product_id'] ?? [];
    $qtys = $_POST['quantity'] ?? [];

    // compute total & validate
    $total = 0;
    $items = [];
    for($i=0;$i<count($pids);$i++){
        $pid = intval($pids[$i]);
        $qty = intval($qtys[$i]);
        if ($pid>0 && $qty>0){
            // get current price & stock
            $stmt = $conn->prepare("SELECT price,stock_quantity FROM Products WHERE product_id=?");
            $stmt->bind_param("i",$pid); $stmt->execute(); $res = $stmt->get_result();
            if ($row=$res->fetch_assoc()){
                if ($row['stock_quantity'] < $qty){
                    $error = "Not enough stock for product ID $pid";
                    break;
                }
                $items[] = ['product_id'=>$pid,'quantity'=>$qty,'price'=>$row['price']];
                $total += $row['price'] * $qty;
            } else {
                $error = "Product not found (ID $pid)";
                break;
            }
            $stmt->close();
        }
    }

    if (empty($items)) $error = $error ?? "Select at least one product with quantity.";

    if (empty($error)){
        // create order
        $ins = $conn->prepare("INSERT INTO Orders (user_id,total_amount) VALUES (?,?)");
        $ins->bind_param("id",$user_id,$total);
        $ins->execute();
        $order_id = $ins->insert_id;
        $ins->close();

        // insert order items and reduce stock & log
        $stmtItem = $conn->prepare("INSERT INTO Order_Items (order_id,product_id,quantity,price) VALUES (?,?,?,?)");
        $updStock = $conn->prepare("UPDATE Products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
        $logStmt = $conn->prepare("INSERT INTO Inventory_Log (product_id,user_id,change_type,quantity_changed) VALUES (?,?,?,?)");
        foreach($items as $it){
            $stmtItem->bind_param("iiid",$order_id,$it['product_id'],$it['quantity'],$it['price']);
            $stmtItem->execute();

            $updStock->bind_param("ii",$it['quantity'],$it['product_id']);
            $updStock->execute();

            $type = 'sold';
            $uid = current_user_id();
            $q = $it['quantity'];
            $logStmt->bind_param("iisi",$it['product_id'],$uid,$type,$q);
            $logStmt->execute();
        }
        $stmtItem->close(); $updStock->close(); $logStmt->close();

        header("Location: view_order.php?id=".$order_id);
        exit;
    }
}
?>
<h2>Create Order</h2>
<?php if(!empty($error)): ?><div class="notice error"><?=htmlspecialchars($error)?></div><?php endif; ?>

<form method="post">
  <div class="form-group">
    <label>Customer (optional)</label>
    <select name="user_id">
      <option value="">-- guest --</option>
      <?php while($u=$users->fetch_assoc()): ?>
        <option value="<?=$u['user_id']?>"><?=htmlspecialchars($u['name'])?></option>
      <?php endwhile; ?>
    </select>
  </div>

  <h3>Items</h3>
  <div id="items">
    <?php $i=0; foreach($products as $p): ?>
      <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px">
        <div style="width:50%"><?=htmlspecialchars($p['name'])?> (₹<?=htmlspecialchars($p['price'])?>) [stock: <?=htmlspecialchars($p['stock_quantity'])?>]</div>
        <input type="hidden" name="product_id[]" value="<?=$p['product_id']?>">
        <div style="width:120px"><input type="number" name="quantity[]" min="0" placeholder="Qty" value="0"></div>
      </div>
    <?php endforeach; ?>
  </div>

  <button type="submit">Place Order</button>
</form>

<?php include 'inc/footer.php'; ?>
