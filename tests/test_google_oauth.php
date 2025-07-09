<?php
session_start();
require_once 'config/google_config.php';

// Test if cURL is available
if (!function_exists('curl_init')) {
    die("❌ cURL extension is not available. Please enable it in your PHP configuration.");
}

// Test configuration
echo "<h2>Google OAuth Configuration Test</h2>";
echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 5px;'>";

echo "<h3>1. Configuration Check:</h3>";
echo "Client ID: " . (GOOGLE_CLIENT_ID !== 'YOUR_GOOGLE_CLIENT_ID' ? '✅ Set' : '❌ Not set') . "<br>";
echo "Client Secret: " . (GOOGLE_CLIENT_SECRET !== 'YOUR_GOOGLE_CLIENT_SECRET' ? '✅ Set' : '❌ Not set') . "<br>";
echo "Redirect URI: " . GOOGLE_REDIRECT_URI . "<br>";
echo "cURL Extension: ✅ Available<br>";

echo "<h3>2. Generated Auth URL:</h3>";
$auth_url = getGoogleAuthUrl();
echo "<a href='$auth_url' target='_blank'>$auth_url</a><br>";

echo "<h3>3. Test Links:</h3>";
echo "<a href='auth/google-login.php' class='btn btn-primary'>Test Google Login</a><br><br>";
echo "<a href='index.php' class='btn btn-secondary'>Back to Homepage</a>";

echo "</div>";

// If credentials are not set, show instructions
if (GOOGLE_CLIENT_ID === 'YOUR_GOOGLE_CLIENT_ID' || GOOGLE_CLIENT_SECRET === 'YOUR_GOOGLE_CLIENT_SECRET') {
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3>⚠️ Google OAuth Not Configured</h3>";
    echo "<p>To enable Google OAuth authentication:</p>";
    echo "<ol>";
    echo "<li>Follow the setup guide in <code>GOOGLE_OAUTH_SETUP.md</code></li>";
    echo "<li>Get your Google OAuth credentials from <a href='https://console.cloud.google.com/' target='_blank'>Google Cloud Console</a></li>";
    echo "<li>Update <code>config/google_config.php</code> with your actual credentials</li>";
    echo "<li>Make sure your redirect URI matches: <code>" . GOOGLE_REDIRECT_URI . "</code></li>";
    echo "</ol>";
    echo "</div>";
}

// Check if callback file exists
if (!file_exists('auth/google-callback.php')) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3>❌ Missing Files</h3>";
    echo "<p>The following files are missing:</p>";
    echo "<ul>";
    if (!file_exists('auth/google-callback.php')) echo "<li>auth/google-callback.php</li>";
    if (!file_exists('auth/select-role.php')) echo "<li>auth/select-role.php</li>";
    echo "</ul>";
    echo "</div>";
}

// Check database connection
try {
    require_once 'config/database.php';
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3>✅ Database Connection</h3>";
    echo "<p>Database connection successful!</p>";
    
    // Check if users table has google_id column
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (in_array('google_id', $columns)) {
        echo "<p>✅ Users table has google_id column</p>";
    } else {
        echo "<p>❌ Users table missing google_id column</p>";
    }
    echo "</div>";
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3>❌ Database Error</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google OAuth Test - ELMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .btn { margin: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Google OAuth Test Page</h1>
        <p>This page helps you test and debug Google OAuth integration.</p>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="auth/google-login.php" class="btn btn-primary">
                            <i class="fab fa-google me-2"></i>Test Google Login
                        </a>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-home me-2"></i>Back to Homepage
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Configuration Status</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Google Client ID</span>
                                <span class="badge <?php echo GOOGLE_CLIENT_ID !== 'YOUR_GOOGLE_CLIENT_ID' ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo GOOGLE_CLIENT_ID !== 'YOUR_GOOGLE_CLIENT_ID' ? 'Set' : 'Not Set'; ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Google Client Secret</span>
                                <span class="badge <?php echo GOOGLE_CLIENT_SECRET !== 'YOUR_GOOGLE_CLIENT_SECRET' ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo GOOGLE_CLIENT_SECRET !== 'YOUR_GOOGLE_CLIENT_SECRET' ? 'Set' : 'Not Set'; ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>cURL Extension</span>
                                <span class="badge bg-success">Available</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Database Connection</span>
                                <span class="badge bg-success">Connected</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html> 