<?php
require_once 'inc/config.php';
require_once 'inc/auth.php';
$id = intval($_GET['id'] ?? 0);
if ($id){
    // safe delete
    $stmt = $conn->prepare("DELETE FROM Products WHERE product_id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $stmt->close();
}
header("Location: products.php");
exit;
