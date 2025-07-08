<?php
// Test Google OAuth Configuration
// This file helps you verify your Google OAuth setup

// Check if real credentials are set
$real_config_file = 'google_config.php';
$config_content = file_get_contents($real_config_file);

// Check for placeholder values
$has_placeholders = (
    strpos($config_content, 'YOUR_GOOGLE_CLIENT_ID') !== false ||
    strpos($config_content, 'YOUR_GOOGLE_CLIENT_SECRET') !== false
);

if ($has_placeholders) {
    echo "<h2>❌ Google OAuth Not Configured</h2>";
    echo "<p>Your <code>config/google_config.php</code> still contains placeholder values.</p>";
    echo "<p>Please follow the setup guide in <code>GOOGLE_OAUTH_QUICK_FIX.md</code></p>";
    
    echo "<h3>Current Configuration:</h3>";
    echo "<pre>";
    echo "GOOGLE_CLIENT_ID: YOUR_GOOGLE_CLIENT_ID\n";
    echo "GOOGLE_CLIENT_SECRET: YOUR_GOOGLE_CLIENT_SECRET\n";
    echo "</pre>";
    
    echo "<h3>What You Need to Do:</h3>";
    echo "<ol>";
    echo "<li>Go to <a href='https://console.cloud.google.com/' target='_blank'>Google Cloud Console</a></li>";
    echo "<li>Create a new project or select existing one</li>";
    echo "<li>Enable Google+ API and OAuth2 API</li>";
    echo "<li>Configure OAuth consent screen</li>";
    echo "<li>Create OAuth 2.0 credentials</li>";
    echo "<li>Copy the Client ID and Client Secret</li>";
    echo "<li>Update <code>config/google_config.php</code></li>";
    echo "</ol>";
    
    echo "<p><strong>Redirect URI to add in Google Console:</strong><br>";
    echo "<code>http://localhost/elms/auth/google-callback.php</code></p>";
    
} else {
    echo "<h2>✅ Google OAuth Configuration Found</h2>";
    echo "<p>Your configuration appears to have real credentials set.</p>";
    
    // Try to load the real config
    require_once $real_config_file;
    
    echo "<h3>Configuration Details:</h3>";
    echo "<pre>";
    echo "Client ID: " . (defined('GOOGLE_CLIENT_ID') ? substr(GOOGLE_CLIENT_ID, 0, 20) . '...' : 'Not defined') . "\n";
    echo "Client Secret: " . (defined('GOOGLE_CLIENT_SECRET') ? substr(GOOGLE_CLIENT_SECRET, 0, 10) . '...' : 'Not defined') . "\n";
    echo "Redirect URI: " . (defined('GOOGLE_REDIRECT_URI') ? GOOGLE_REDIRECT_URI : 'Not defined') . "\n";
    echo "</pre>";
    
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Test the Google login: <a href='../auth/google-login.php'>Try Google Login</a></li>";
    echo "<li>Check the test page: <a href='../test_google_oauth.php'>OAuth Test Page</a></li>";
    echo "</ol>";
}

// Check system requirements
echo "<h3>System Requirements Check:</h3>";
echo "<ul>";
echo "<li>cURL Extension: " . (function_exists('curl_init') ? '✅ Available' : '❌ Not Available') . "</li>";
echo "<li>JSON Extension: " . (function_exists('json_decode') ? '✅ Available' : '❌ Not Available') . "</li>";
echo "<li>PHP Version: " . PHP_VERSION . " " . (version_compare(PHP_VERSION, '7.0.0') >= 0 ? '✅ OK' : '❌ Too Old') . "</li>";
echo "</ul>";

// Check database connection
echo "<h3>Database Connection:</h3>";
try {
    require_once 'database.php';
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    echo "✅ Database connection successful";
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage();
}
?> 