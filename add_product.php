<?php
require_once 'inc/config.php';
require_once 'inc/auth.php';
include 'inc/header.php';

if ($_SERVER['REQUEST_METHOD']=='POST'){
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    // optional category/vendor handling (simple)
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $vendor_id = !empty($_POST['vendor_id']) ? intval($_POST['vendor_id']) : null;

    $stmt = $conn->prepare("INSERT INTO Products (vendor_id,category_id,name,description,price,stock_quantity) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("iissdi", $vendor_id, $category_id, $name, $desc, $price, $stock);
    if ($stmt->execute()){
        // inventory log: added by current user (if logged)
        $pid = $stmt->insert_id;
        $uid = current_user_id();
        $log = $conn->prepare("INSERT INTO Inventory_Log (product_id,user_id,change_type,quantity_changed) VALUES (?,?,?,?)");
        $type = 'added';
        $log->bind_param("iisi",$pid,$uid,$type,$stock);
        $log->execute();
        $log->close();

        $success = "Product added.";
    } else {
        $error = "Error: " . $conn->error;
    }
    $stmt->close();
}

// fetch categories/vendors for optional selects
$cats = $conn->query("SELECT category_id,name FROM Categories");
$vendors = $conn->query("SELECT vendor_id,name FROM Vendors");
?>

<h2>Add Product</h2>
<?php if(!empty($error)): ?><div class="notice error"><?=htmlspecialchars($error)?></div><?php endif; ?>
<?php if(!empty($success)): ?><div class="notice success"><?=htmlspecialchars($success)?></div><?php endif; ?>

<form method="post" style="max-width:600px">
  <div class="form-group"><input name="name" placeholder="Product name" required></div>
  <div class="form-group"><textarea name="description" placeholder="Description"></textarea></div>
  <div class="form-group"><input type="number" step="0.01" name="price" placeholder="Price" required></div>
  <div class="form-group"><input type="number" name="stock" placeholder="Stock quantity" required></div>
  <div class="form-group">
    <select name="category_id">
      <option value="">-- Select Category (optional) --</option>
      <?php while($c=$cats->fetch_assoc()): ?>
        <option value="<?=$c['category_id']?>"><?=htmlspecialchars($c['name'])?></option>
      <?php endwhile; ?>
    </select>
  </div>
  <div class="form-group">
    <select name="vendor_id">
      <option value="">-- Select Vendor (optional) --</option>
      <?php while($v=$vendors->fetch_assoc()): ?>
        <option value="<?=$v['vendor_id']?>"><?=htmlspecialchars($v['name'])?></option>
      <?php endwhile; ?>
    </select>
  </div>

  <button type="submit">Add Product</button>
</form>

<?php include 'inc/footer.php'; ?>
