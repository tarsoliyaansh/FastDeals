 <?php
// inc/auth.php
session_start();

function is_logged_in(){
    return isset($_SESSION['user_id']);
}

function current_user_id(){
    return $_SESSION['user_id'] ?? null;
}

function require_login(){
    if (!is_logged_in()){
        header("Location: login.php");
        exit;
    }
}
