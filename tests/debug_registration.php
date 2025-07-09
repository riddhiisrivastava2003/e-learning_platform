<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔧 Registration Debug Information</h2>";

// Check PHP version
echo "<h3>PHP Information</h3>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Error Reporting:</strong> " . (error_reporting() ? 'Enabled' : 'Disabled') . "</p>";

// Check required functions
echo "<h3>Required Functions</h3>";
echo "<p><strong>mail() function:</strong> " . (function_exists('mail') ? '✅ Available' : '❌ Not Available') . "</p>";
echo "<p><strong>password_hash() function:</strong> " . (function_exists('password_hash') ? '✅ Available' : '❌ Not Available') . "</p>";
echo "<p><strong>PDO extension:</strong> " . (extension_loaded('pdo') ? '✅ Available' : '❌ Not Available') . "</p>";
echo "<p><strong>PDO MySQL extension:</strong> " . (extension_loaded('pdo_mysql') ? '✅ Available' : '❌ Not Available') . "</p>";

// Check file permissions
echo "<h3>File Permissions</h3>";
echo "<p><strong>services/EmailService.php:</strong> " . (file_exists('../services/EmailService.php') ? '✅ Exists' : '❌ Missing') . "</p>";
echo "<p><strong>config/database.php:</strong> " . (file_exists('config/database.php') ? '✅ Exists' : '❌ Missing') . "</p>";
echo "<p><strong>logs directory writable:</strong> " . (is_writable('logs') || is_writable('.') ? '✅ Writable' : '❌ Not Writable') . "</p>";

// Test database connection
echo "<h3>Database Connection</h3>";
try {
    require_once 'config/database.php';
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    echo "<p><strong>Database Connection:</strong> ✅ Successful</p>";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    echo "<p><strong>Users table:</strong> " . ($stmt->rowCount() > 0 ? '✅ Exists' : '❌ Missing') . "</p>";
    
} catch (PDOException $e) {
    echo "<p><strong>Database Connection:</strong> ❌ Failed - " . $e->getMessage() . "</p>";
}

// Test EmailService
echo "<h3>Email Service Test</h3>";
try {
    require_once '../services/EmailService.php';
    echo "<p><strong>EmailService class:</strong> ✅ Loaded successfully</p>";
    
    // Test email sending (without actually sending)
    $test_result = EmailService::sendWelcomeEmail('test@example.com', 'Test User', 'student');
    echo "<p><strong>Email sending test:</strong> " . ($test_result ? '✅ Success' : '❌ Failed (expected on localhost)') . "</p>";
    
} catch (Exception $e) {
    echo "<p><strong>EmailService:</strong> ❌ Error - " . $e->getMessage() . "</p>";
}

// Check registration files
echo "<h3>Registration Files</h3>";
$registration_files = [
    'student/register.php',
    'teacher/register.php', 
    'admin/register.php',
    'auth/select-role.php'
];

foreach ($registration_files as $file) {
    echo "<p><strong>$file:</strong> " . (file_exists($file) ? '✅ Exists' : '❌ Missing') . "</p>";
}

// Test form submission simulation
echo "<h3>Form Submission Test</h3>";
echo "<p>To test registration, try registering a new user at:</p>";
echo "<ul>";
echo "<li><a href='student/register.php' target='_blank'>Student Registration</a></li>";
echo "<li><a href='teacher/register.php' target='_blank'>Teacher Registration</a></li>";
echo "<li><a href='admin/register.php' target='_blank'>Admin Registration</a></li>";
echo "</ul>";

echo "<h3>🔍 Troubleshooting Tips</h3>";
echo "<ul>";
echo "<li>If registration is buffering, check the PHP error logs</li>";
echo "<li>Make sure all required files exist and are readable</li>";
echo "<li>Verify database connection and table structure</li>";
echo "<li>Check if email service is causing delays</li>";
echo "<li>Ensure proper file permissions</li>";
echo "</ul>";

echo "<p><a href='index.php'>← Back to Home</a></p>";
?> 