<?php
require_once 'inc/config.php';
require_once 'inc/auth.php';
include 'inc/header.php';

$res = $conn->query("SELECT p.product_id,p.name,p.price,p.stock_quantity,c.name AS category,v.name AS vendor
                      FROM Products p
                      LEFT JOIN Categories c ON p.category_id = c.category_id
                      LEFT JOIN Vendors v ON p.vendor_id = v.vendor_id
                      ORDER BY p.created_at DESC");
?>
<div style="display:flex;justify-content:space-between;align-items:center">
  <h2>Products</h2>
  <a href="add_product.php"><button>Add Product</button></a>
</div>

<table class="table">
<tr><th>ID</th><th>Name</th><th>Category</th><th>Vendor</th><th>Price</th><th>Stock</th><th>Action</th></tr>
<?php while($row = $res->fetch_assoc()): ?>
<tr>
  <td><?=htmlspecialchars($row['product_id'])?></td>
  <td><?=htmlspecialchars($row['name'])?></td>
  <td><?=htmlspecialchars($row['category'] ?? '-')?></td>
  <td><?=htmlspecialchars($row['vendor'] ?? '-')?></td>
  <td><?=htmlspecialchars($row['price'])?></td>
  <td><?=htmlspecialchars($row['stock_quantity'])?></td>
  <td class="actions">
    <a href="edit_product.php?id=<?=$row['product_id']?>"><button class="secondary">Edit</button></a>
    <a href="delete_product.php?id=<?=$row['product_id']?>" onclick="return confirm('Delete product?')"><button>Delete</button></a>
  </td>
</tr>
<?php endwhile; ?>
</table>
<?php include 'inc/footer.php'; ?>
