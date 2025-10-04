<?php
require_once 'inc/auth.php';
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit;
