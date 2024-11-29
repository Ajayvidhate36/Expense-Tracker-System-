<?php
session_start(); // Start session
session_unset(); // Remove session variables
session_destroy(); // Destroy the session

// Redirect to login page after logout
header('Location: login.php');
exit();
?>
