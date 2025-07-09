<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Complete System Test</h1>";

// Test 1: Basic PHP
echo "<h2>1. PHP Environment</h2>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Error Reporting:</strong> " . (error_reporting() ? 'Enabled' : 'Disabled') . "</p>";

// Test 2: Database Connection
echo "<h2>2. Database Connection</h2>";
try {
    require_once 'config/database_simple.php';
    echo "<p><strong>Database Connection:</strong> ✅ Successful</p>";
    
    // Test users table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<p><strong>Users in database:</strong> " . $result['count'] . "</p>";
    
} catch (Exception $e) {
    echo "<p><strong>Database Error:</strong> ❌ " . $e->getMessage() . "</p>";
}

// Test 3: File Existence
echo "<h2>3. File Existence Check</h2>";
$files = [
    'config/database_simple.php',
    'student/register.php',
    'student/login.php',
    'teacher/register.php',
    'admin/register.php',
    'student/register_simple.php',
    'student/login_simple.php'
];

foreach ($files as $file) {
    echo "<p><strong>$file:</strong> " . (file_exists($file) ? '✅ Exists' : '❌ Missing') . "</p>";
}

// Test 4: Form Processing
echo "<h2>4. Form Processing Test</h2>";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<p><strong>Form submitted:</strong> ✅ Yes</p>";
    echo "<p><strong>POST data:</strong> " . print_r($_POST, true) . "</p>";
} else {
    echo "<p><strong>Form submitted:</strong> ❌ No</p>";
}

// Test 5: Session
echo "<h2>5. Session Test</h2>";
session_start();
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Status:</strong> " . (session_status() == PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "</p>";

echo "<h2>6. Quick Links</h2>";
echo "<ul>";
echo "<li><a href='test_simple.php' target='_blank'>🔧 Simple PHP Test</a></li>";
echo "<li><a href='student/register_simple.php' target='_blank'>📝 Simple Student Registration</a></li>";
echo "<li><a href='student/login_simple.php' target='_blank'>🔑 Simple Student Login</a></li>";
echo "<li><a href='student/register.php' target='_blank'>🎓 Full Student Registration</a></li>";
echo "<li><a href='student/login.php' target='_blank'>🎓 Full Student Login</a></li>";
echo "<li><a href='teacher/register.php' target='_blank'>👨‍🏫 Teacher Registration</a></li>";
echo "<li><a href='admin/register.php' target='_blank'>👨‍💼 Admin Registration</a></li>";
echo "</ul>";

echo "<h2>7. Test Form</h2>";
?>
<form method="POST">
    <input type="text" name="test_field" placeholder="Test input" value="test">
    <button type="submit">Submit Test</button>
</form>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2 { color: #333; }
p { margin: 5px 0; }
ul { margin: 10px 0; }
li { margin: 5px 0; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style> 