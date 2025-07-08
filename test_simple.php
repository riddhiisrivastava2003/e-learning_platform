<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔧 Simple PHP Test</h2>";

// Test basic PHP
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Error Reporting:</strong> " . (error_reporting() ? 'Enabled' : 'Disabled') . "</p>";

// Test database connection
echo "<h3>Database Test</h3>";
try {
    require_once 'config/database.php';
    echo "<p><strong>Database Connection:</strong> ✅ Successful</p>";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<p><strong>Users in database:</strong> " . $result['count'] . "</p>";
    
} catch (Exception $e) {
    echo "<p><strong>Database Error:</strong> ❌ " . $e->getMessage() . "</p>";
}

// Test file includes
echo "<h3>File Include Test</h3>";
$files_to_test = [
    'config/database.php',
    'student/register.php',
    'teacher/register.php',
    'admin/register.php',
    'student/login.php',
    'teacher/login.php',
    'admin/login.php'
];

foreach ($files_to_test as $file) {
    if (file_exists($file)) {
        echo "<p><strong>$file:</strong> ✅ Exists</p>";
    } else {
        echo "<p><strong>$file:</strong> ❌ Missing</p>";
    }
}

// Test form submission
echo "<h3>Form Test</h3>";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<p><strong>Form submitted:</strong> ✅ Yes</p>";
    echo "<p><strong>POST data:</strong> " . print_r($_POST, true) . "</p>";
} else {
    echo "<p><strong>Form submitted:</strong> ❌ No</p>";
}

echo "<h3>Test Form</h3>";
?>
<form method="POST">
    <input type="text" name="test_field" placeholder="Test input">
    <button type="submit">Submit Test</button>
</form>

<p><a href="index.php">← Back to Home</a></p> 