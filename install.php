<?php
/**
 * ELMS Installation Script
 * Run this file to set up the ELMS platform
 */

// Check if already installed
if (file_exists('config/installed.lock')) {
    die('ELMS is already installed. Remove config/installed.lock to reinstall.');
}

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($step) {
        case 1:
            // Database configuration
            $db_host = $_POST['db_host'] ?? '';
            $db_name = $_POST['db_name'] ?? '';
            $db_user = $_POST['db_user'] ?? '';
            $db_pass = $_POST['db_pass'] ?? '';
            
            if (empty($db_host) || empty($db_name) || empty($db_user)) {
                $error = 'All database fields are required.';
            } else {
                try {
                    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Test connection successful
                    $_SESSION['db_config'] = [
                        'host' => $db_host,
                        'name' => $db_name,
                        'user' => $db_user,
                        'pass' => $db_pass
                    ];
                    $step = 2;
                } catch (PDOException $e) {
                    $error = 'Database connection failed: ' . $e->getMessage();
                }
            }
            break;
            
        case 2:
            // Admin account creation
            $admin_email = $_POST['admin_email'] ?? '';
            $admin_password = $_POST['admin_password'] ?? '';
            $admin_name = $_POST['admin_name'] ?? '';
            
            if (empty($admin_email) || empty($admin_password) || empty($admin_name)) {
                $error = 'All admin fields are required.';
            } else {
                try {
                    $db_config = $_SESSION['db_config'];
                    $pdo = new PDO("mysql:host={$db_config['host']};dbname={$db_config['name']}", $db_config['user'], $db_config['pass']);
                    
                    // Create database configuration file
                    $config_content = "<?php
// Database Configuration
define('DB_HOST', '{$db_config['host']}');
define('DB_NAME', '{$db_config['name']}');
define('DB_USER', '{$db_config['user']}');
define('DB_PASS', '{$db_config['pass']}');

// Create PDO connection
try {
    \$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    \$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException \$e) {
    die('Connection failed: ' . \$e->getMessage());
}
?>";
                    
                    file_put_contents('config/database.php', $config_content);
                    
                    // Import database schema
                    $sql_file = file_get_contents('database_updated.sql');
                    $pdo->exec($sql_file);
                    
                    // Create admin user
                    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (email, password, full_name, role, status) VALUES (?, ?, ?, 'admin', 'active')");
                    $stmt->execute([$admin_email, $hashed_password, $admin_name]);
                    
                    // Create admin details
                    $admin_id = $pdo->lastInsertId();
                    $stmt = $pdo->prepare("INSERT INTO admin_details (admin_id, department, position, permissions) VALUES (?, 'IT Administration', 'System Administrator', '[\"all\"]')");
                    $stmt->execute([$admin_id]);
                    
                    $step = 3;
                } catch (Exception $e) {
                    $error = 'Installation failed: ' . $e->getMessage();
                }
            }
            break;
            
        case 3:
            // Create installed lock file
            file_put_contents('config/installed.lock', date('Y-m-d H:i:s'));
            $success = 'ELMS has been successfully installed!';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELMS Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h3><i class="fas fa-graduation-cap"></i> ELMS Installation</h3>
                    </div>
                    <div class="card-body">
                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="progress">
                                <div class="progress-bar" style="width: <?php echo ($step / 3) * 100; ?>%"></div>
                            </div>
                            <small class="text-muted">Step <?php echo $step; ?> of 3</small>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                                <div class="mt-3">
                                    <a href="index.php" class="btn btn-primary">
                                        <i class="fas fa-home"></i> Go to Homepage
                                    </a>
                                    <a href="admin/login.php" class="btn btn-success">
                                        <i class="fas fa-user-shield"></i> Admin Login
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($step == 1): ?>
                            <!-- Step 1: Database Configuration -->
                            <h5 class="mb-3">Database Configuration</h5>
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="db_host" class="form-label">Database Host</label>
                                    <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                                </div>
                                <div class="mb-3">
                                    <label for="db_name" class="form-label">Database Name</label>
                                    <input type="text" class="form-control" id="db_name" name="db_name" value="edutech_pro" required>
                                </div>
                                <div class="mb-3">
                                    <label for="db_user" class="form-label">Database Username</label>
                                    <input type="text" class="form-control" id="db_user" name="db_user" required>
                                </div>
                                <div class="mb-3">
                                    <label for="db_pass" class="form-label">Database Password</label>
                                    <input type="password" class="form-control" id="db_pass" name="db_pass">
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-arrow-right"></i> Next Step
                                    </button>
                                </div>
                            </form>
                        <?php elseif ($step == 2): ?>
                            <!-- Step 2: Admin Account -->
                            <h5 class="mb-3">Create Admin Account</h5>
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="admin_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="admin_name" name="admin_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="admin_email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="admin_email" name="admin_email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="admin_password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-check"></i> Complete Installation
                                    </button>
                                </div>
                            </form>
                        <?php elseif ($step == 3): ?>
                            <!-- Step 3: Configuration -->
                            <h5 class="mb-3">Additional Configuration</h5>
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Next Steps:</h6>
                                <ol class="mb-0">
                                    <li>Configure Google OAuth in <code>config/google_config.php</code></li>
                                    <li>Set up Stripe payment in <code>config/payment_config.php</code></li>
                                    <li>Update site settings in admin dashboard</li>
                                    <li>Add your first courses and teachers</li>
                                </ol>
                            </div>
                            <form method="POST">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-rocket"></i> Launch ELMS
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <small class="text-muted">
                        ELMS - E-Learning Management System<br>
                        Made with ❤️ for education
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 