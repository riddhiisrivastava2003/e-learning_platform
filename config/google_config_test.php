<?php
// Google OAuth Test Configuration
// This is for testing purposes only - replace with your actual credentials

// Test credentials (replace with your actual ones from Google Cloud Console)
define('GOOGLE_CLIENT_ID', '123456789-abcdefghijklmnopqrstuvwxyz.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'GOCSPX-your-actual-secret-here');
define('GOOGLE_REDIRECT_URI', 'http://localhost/elms/auth/google-callback.php');

// Google API endpoints
define('GOOGLE_AUTH_URL', 'https://accounts.google.com/o/oauth2/auth');
define('GOOGLE_TOKEN_URL', 'https://oauth2.googleapis.com/token');
define('GOOGLE_USERINFO_URL', 'https://www.googleapis.com/oauth2/v2/userinfo');

// Scopes for user data
define('GOOGLE_SCOPES', 'email profile');

// Function to generate Google OAuth URL
function getGoogleAuthUrl() {
    $params = array(
        'client_id' => GOOGLE_CLIENT_ID,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'scope' => GOOGLE_SCOPES,
        'response_type' => 'code',
        'access_type' => 'offline',
        'prompt' => 'consent'
    );
    
    return GOOGLE_AUTH_URL . '?' . http_build_query($params);
}

// Function to get Google access token
function getGoogleAccessToken($code) {
    $data = array(
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'code' => $code,
        'grant_type' => 'authorization_code',
        'redirect_uri' => GOOGLE_REDIRECT_URI
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, GOOGLE_TOKEN_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For testing only
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code !== 200) {
        error_log("Google OAuth Error: HTTP $http_code - $response");
        return false;
    }
    
    return json_decode($response, true);
}

// Function to get Google user info
function getGoogleUserInfo($access_token) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, GOOGLE_USERINFO_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $access_token));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For testing only
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code !== 200) {
        error_log("Google UserInfo Error: HTTP $http_code - $response");
        return false;
    }
    
    return json_decode($response, true);
}

// Test function to check configuration
function testGoogleConfig() {
    echo "Testing Google OAuth Configuration:\n";
    echo "Client ID: " . (GOOGLE_CLIENT_ID !== '123456789-abcdefghijklmnopqrstuvwxyz.apps.googleusercontent.com' ? '✓ Set' : '✗ Not set') . "\n";
    echo "Client Secret: " . (GOOGLE_CLIENT_SECRET !== 'GOCSPX-your-actual-secret-here' ? '✓ Set' : '✗ Not set') . "\n";
    echo "Redirect URI: " . GOOGLE_REDIRECT_URI . "\n";
    echo "cURL Extension: " . (function_exists('curl_init') ? '✓ Available' : '✗ Not available') . "\n";
    
    $auth_url = getGoogleAuthUrl();
    echo "Auth URL: " . $auth_url . "\n";
}
?> 