<?php
session_start();

// Check if form was submitted
if ($_POST['action'] === 'update_credentials') {
    $client_id = trim($_POST['client_id']);
    $client_secret = trim($_POST['client_secret']);
    
    if (empty($client_id) || empty($client_secret)) {
        $error = "Both Client ID and Client Secret are required!";
    } else {
        // Read the current config file
        $config_content = file_get_contents('config/google_config.php');
        
        // Replace the placeholder values
        $config_content = str_replace(
            "define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID');",
            "define('GOOGLE_CLIENT_ID', '$client_id');",
            $config_content
        );
        
        $config_content = str_replace(
            "define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');",
            "define('GOOGLE_CLIENT_SECRET', '$client_secret');",
            $config_content
        );
        
        // Write back to the file
        if (file_put_contents('config/google_config.php', $config_content)) {
            $success = "Google OAuth credentials updated successfully!";
        } else {
            $error = "Failed to update the configuration file. Please check file permissions.";
        }
    }
}

// Read current values
$current_config = file_get_contents('config/google_config.php');
$has_placeholders = (
    strpos($current_config, 'YOUR_GOOGLE_CLIENT_ID') !== false ||
    strpos($current_config, 'YOUR_GOOGLE_CLIENT_SECRET') !== false
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Google OAuth Credentials</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card mt-5">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">
                            <?php if ($has_placeholders): ?>
                                🔧 Update Google OAuth Credentials
                            <?php else: ?>
                                ✅ Google OAuth Credentials Configured
                            <?php endif; ?>
                        </h2>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($has_placeholders): ?>
                            <div class="alert alert-warning">
                                <strong>Current Status:</strong> Google OAuth credentials are not configured.
                                <br>You need to get your Client ID and Client Secret from Google Cloud Console.
                            </div>
                            
                            <form method="POST">
                                <input type="hidden" name="action" value="update_credentials">
                                
                                <div class="mb-3">
                                    <label for="client_id" class="form-label">Google Client ID</label>
                                    <input type="text" class="form-control" id="client_id" name="client_id" 
                                           placeholder="123456789-abcdefghijklmnopqrstuvwxyz.apps.googleusercontent.com"
                                           required>
                                    <div class="form-text">Get this from Google Cloud Console > APIs & Services > Credentials</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="client_secret" class="form-label">Google Client Secret</label>
                                    <input type="text" class="form-control" id="client_secret" name="client_secret" 
                                           placeholder="GOCSPX-your-secret-here"
                                           required>
                                    <div class="form-text">Get this from Google Cloud Console > APIs & Services > Credentials</div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Update Credentials</button>
                                </div>
                            </form>
                            
                            <hr class="my-4">
                            
                            <div class="text-center">
                                <h5>Need Help Getting Credentials?</h5>
                                <p>Follow these steps:</p>
                                <ol class="text-start">
                                    <li>Go to <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
                                    <li>Create a new project or select existing one</li>
                                    <li>Enable Google+ API and OAuth2 API</li>
                                    <li>Configure OAuth consent screen</li>
                                    <li>Create OAuth 2.0 credentials</li>
                                    <li>Add redirect URI: <code>http://localhost/elms/auth/google-callback.php</code></li>
                                    <li>Copy the Client ID and Client Secret</li>
                                </ol>
                            </div>
                            
                        <?php else: ?>
                            <div class="alert alert-success">
                                <strong>Great!</strong> Your Google OAuth credentials are already configured.
                            </div>
                            
                            <div class="text-center">
                                <a href="test_google_oauth.php" class="btn btn-success me-2">Test OAuth Setup</a>
                                <a href="auth/google-login.php" class="btn btn-primary">Try Google Login</a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="text-center mt-4">
                            <a href="index.php" class="btn btn-outline-secondary">Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 