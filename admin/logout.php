<?php
/**
 * Admin Logout
 */

// Define access constant
define('MSR_ACCESS', true);

// Include configuration
require_once '../config/config.php';

// Logout user
logout();

// Redirect to login page
header('Location: login.php?logged_out=1');
exit;
?>