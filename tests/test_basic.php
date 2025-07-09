<?php
// Basic test file to verify server functionality
echo "<h1>ELMS Basic Test</h1>";
echo "<p>PHP is working!</p>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>PHP version: " . phpversion() . "</p>";

// Test database connection
echo "<h2>Database Test</h2>";
try {
    require_once 'config/database_simple.php';
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}

// Test file structure
echo "<h2>File Structure Test</h2>";
$files = [
    'index.php',
    'student/login.php',
    'teacher/login.php',
    'admin/login.php',
    'config/database_simple.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ $file exists</p>";
    } else {
        echo "<p style='color: red;'>✗ $file missing</p>";
    }
}

echo "<h2>Quick Links</h2>";
echo "<ul>";
echo "<li><a href='index.php'>Homepage</a></li>";
echo "<li><a href='student/login.php'>Student Login</a></li>";
echo "<li><a href='teacher/login.php'>Teacher Login</a></li>";
echo "<li><a href='admin/login.php'>Admin Login</a></li>";
echo "<li><a href='test_all.php'>Comprehensive Test</a></li>";
echo "</ul>";
?> 