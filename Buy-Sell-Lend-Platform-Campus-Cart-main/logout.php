<?php
session_start(); 

// Destroy session
session_destroy(); // clear all session data

// Redirect to login page
header("Location: login.php"); // send them to login page
exit();
?> 