<?php
session_start();

// Oturumu sonlandır
session_destroy();

// Admin giriş sayfasına yönlendir
header("Location: login.php");
exit;
?>