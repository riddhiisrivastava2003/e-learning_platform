<?php
session_start();
require_once '../config/google_config.php';
 
// Redirect to Google OAuth
$google_auth_url = getGoogleAuthUrl();
header('Location: ' . $google_auth_url);
exit();
?> 