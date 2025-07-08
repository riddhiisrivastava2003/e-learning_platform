<?php
session_start();
require_once '../config/database.php';
require_once '../config/google_config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the callback
error_log("Google OAuth Callback: " . $_SERVER['REQUEST_URI']);

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    error_log("Google OAuth Code received: " . substr($code, 0, 10) . "...");
    
    // Get access token
    $token_data = getGoogleAccessToken($code);
    
    if ($token_data && isset($token_data['access_token'])) {
        error_log("Google OAuth Access token received successfully");
        
        // Get user info from Google
        $user_info = getGoogleUserInfo($token_data['access_token']);
        
        if ($user_info && isset($user_info['id'])) {
            $google_id = $user_info['id'];
            $email = $user_info['email'];
            $name = $user_info['name'];
            $picture = isset($user_info['picture']) ? $user_info['picture'] : '';
            
            error_log("Google User Info: " . $email . " (" . $name . ")");
            
            // Check if user already exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR google_id = ?");
            $stmt->execute([$email, $google_id]);
            $existing_user = $stmt->fetch();
            
            if ($existing_user) {
                // User exists, log them in
                $_SESSION['user_id'] = $existing_user['id'];
                $_SESSION['user_name'] = $existing_user['full_name'];
                $_SESSION['user_role'] = $existing_user['role'];
                
                error_log("Existing user logged in: " . $existing_user['email'] . " (Role: " . $existing_user['role'] . ")");
                
                // Redirect based on role
                switch ($existing_user['role']) {
                    case 'admin':
                        header('Location: ../admin/dashboard.php');
                        break;
                    case 'teacher':
                        header('Location: ../teacher/dashboard.php');
                        break;
                    case 'student':
                        header('Location: ../student/dashboard.php');
                        break;
                    default:
                        header('Location: ../index.php');
                }
                exit();
            } else {
                // New user, redirect to role selection
                $_SESSION['google_user'] = [
                    'google_id' => $google_id,
                    'email' => $email,
                    'name' => $name,
                    'picture' => $picture
                ];
                
                error_log("New user, redirecting to role selection: " . $email);
                header('Location: ../auth/select-role.php');
                exit();
            }
        } else {
            error_log("Failed to get user info from Google. Response: " . json_encode($user_info));
            $_SESSION['error'] = "Failed to get user information from Google. Please try again.";
            header('Location: ../index.php');
            exit();
        }
    } else {
        error_log("Failed to get access token. Response: " . json_encode($token_data));
        $_SESSION['error'] = "Failed to authenticate with Google. Please try again.";
        header('Location: ../index.php');
        exit();
    }
} elseif (isset($_GET['error'])) {
    // Handle OAuth errors
    $error = $_GET['error'];
    $error_description = isset($_GET['error_description']) ? $_GET['error_description'] : '';
    
    error_log("Google OAuth Error: " . $error . " - " . $error_description);
    $_SESSION['error'] = "Google authentication failed: " . $error_description;
    header('Location: ../index.php');
    exit();
} else {
    error_log("No authorization code or error received from Google");
    $_SESSION['error'] = "No authorization received from Google. Please try again.";
    header('Location: ../index.php');
    exit();
}
?> 