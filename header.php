<?php
// inc/header.php
require_once __DIR__.'/config.php';
require_once __DIR__.'/auth.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Inventory System</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="topbar">
  <div class="wrap">
    <a class="brand" href="index.php">FastDeals</a>
    <nav>
      <a href="index.php">Dashboard</a>
      <a href="products.php">Products</a>
      <a href="orders.php">Orders</a>
      <a href="payments.php">Payments</a>
      <a href="inventory_logs.php">Inventory Logs</a>
      <?php if(is_logged_in()): ?>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="container">
