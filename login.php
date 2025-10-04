<?php
require_once 'inc/config.php';
require_once 'inc/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    if (!$email || !$password){
        $error = "Provide email and password.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, password FROM Users WHERE email = ?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()){
            if (password_verify($password, $row['password'])){
                session_start();
                $_SESSION['user_id'] = $row['user_id'];
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid credentials.";
            }
        } else {
            $error = "No user found with that email.";
        }
        $stmt->close();
    }
}

include 'inc/header.php';
?>
<h2>Login</h2>
<?php if(!empty($error)): ?><div class="notice error"><?=htmlspecialchars($error)?></div><?php endif; ?>
<form method="post" style="max-width:420px">
  <div class="form-group"><input type="email" name="email" placeholder="Email" required></div>
  <div class="form-group"><input type="password" name="password" placeholder="Password" required></div>
  <button type="submit">Login</button>
</form>
<?php include 'inc/footer.php'; ?>
