<?php
require_once 'inc/config.php';
require_once 'inc/auth.php';
include 'inc/header.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { echo "<div class='notice error'>Invalid product ID</div>"; include 'inc/footer.php'; exit; }

if ($_SERVER['REQUEST_METHOD']=='POST'){
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    // get old stock to log change
    $old = $conn->prepare("SELECT stock_quantity FROM Products WHERE product_id=?");
    $old->bind_param("i",$id); $old->execute(); $res = $old->get_result(); $oldRow = $res->fetch_assoc(); $old->close();
    $oldStock = $oldRow['stock_quantity'] ?? 0;

    $stmt = $conn->prepare("UPDATE Products SET name=?,description=?,price=?,stock_quantity=? WHERE product_id=?");
    $stmt->bind_param("ssdii",$name,$desc,$price,$stock,$id);
    if ($stmt->execute()){
        // log stock change if different
        $diff = $stock - $oldStock;
        if ($diff != 0){
            $type = $diff>0 ? 'restocked' : 'sold';
            $uid = current_user_id();
            $log = $conn->prepare("INSERT INTO Inventory_Log (product_id,user_id,change_type,quantity_changed) VALUES (?,?,?,?)");
            $q = abs($diff);
            $log->bind_param("iisi",$id,$uid,$type,$q);
            $log->execute();
            $log->close();
        }
        $success = "Updated.";
    } else {
        $error = "Error: ".$conn->error;
    }
    $stmt->close();
}

$res = $conn->prepare("SELECT * FROM Products WHERE product_id=?");
$res->bind_param("i",$id);
$res->execute();
$product = $res->get_result()->fetch_assoc();
$res->close();
if (!$product){ echo "<div class='notice error'>Product not found</div>"; include 'inc/footer.php'; exit; }
?>

<h2>Edit Product</h2>
<?php if(!empty($error)): ?><div class="notice error"><?=htmlspecialchars($error)?></div><?php endif; ?>
<?php if(!empty($success)): ?><div class="notice success"><?=htmlspecialchars($success)?></div><?php endif; ?>

<form method="post" style="max-width:600px">
  <div class="form-group"><input name="name" value="<?=htmlspecialchars($product['name'])?>" required></div>
  <div class="form-group"><textarea name="description"><?=htmlspecialchars($product['description'])?></textarea></div>
  <div class="form-group"><input type="number" step="0.01" name="price" value="<?=htmlspecialchars($product['price'])?>" required></div>
  <div class="form-group"><input type="number" name="stock" value="<?=htmlspecialchars($product['stock_quantity'])?>" required></div>
  <button type="submit">Save</button>
</form>

<?php include 'inc/footer.php'; ?>
