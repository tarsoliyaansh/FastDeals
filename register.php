<?php
require_once 'inc/config.php';
require_once 'inc/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $role = in_array($_POST['role'] ?? 'customer',['customer','staff','admin']) ? $_POST['role'] : 'customer';

    // simple validation
    if (!$name || !$email || !$password) {
        $error = "Please provide name, email and password.";
    } else {
        // check existence
        $stmt = $conn->prepare("SELECT user_id FROM Users WHERE email = ?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0){
            $error = "Email already registered.";
            $stmt->close();
        } else {
            $stmt->close();
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $ins = $conn->prepare("INSERT INTO Users (name,email,password,address,role) VALUES (?,?,?,?,?)");
            $ins->bind_param("sssss",$name,$email,$hash,$address,$role);
            if ($ins->execute()){
                $success = "Registration successful. You can login now.";
            } else {
                $error = "Error: " . $conn->error;
            }
            $ins->close();
        }
    }
}
include 'inc/header.php';
?>
<h2>Register</h2>
<?php if(!empty($error)): ?><div class="notice error"><?=htmlspecialchars($error)?></div><?php endif; ?>
<?php if(!empty($success)): ?><div class="notice success"><?=htmlspecialchars($success)?></div><?php endif; ?>
<form method="post" style="max-width:480px">
  <div class="form-group"><input type="text" name="name" placeholder="Full name" required></div>
  <div class="form-group"><input type="email" name="email" placeholder="Email" required></div>
  <div class="form-group"><input type="password" name="password" placeholder="Password" required></div>
  <div class="form-group"><input type="text" name="address" placeholder="Address (optional)"></div>
  <div class="form-group">
    <select name="role">
      <option value="customer">Customer</option>
      <option value="staff">Staff</option>
    </select>
  </div>
  <button type="submit">Register</button>
</form>
<?php include 'inc/footer.php'; ?>
