<?php
/**
 * ELMS Database Setup Script
 * This script will create/update the database structure
 */

// Database configuration
$db_host = 'localhost';
$db_name = 'edutech_pro';
$db_user = 'root'; // Change this to your database username
$db_pass = ''; // Change this to your database password

try {
    // Connect to MySQL server
    $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$db_name`");
    
    echo "✅ Database '$db_name' created/selected successfully.\n";
    
    // Create users table with all required columns
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `google_id` VARCHAR(255) UNIQUE,
            `username` VARCHAR(50) UNIQUE,
            `email` VARCHAR(100) UNIQUE NOT NULL,
            `password` VARCHAR(255),
            `full_name` VARCHAR(100) NOT NULL,
            `role` ENUM('admin', 'teacher', 'student') NOT NULL,
            `profile_image` VARCHAR(255),
            `phone` VARCHAR(20),
            `address` TEXT,
            `status` ENUM('active', 'inactive', 'pending') DEFAULT 'active',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "✅ Users table created/updated successfully.\n";
    
    // Add missing columns if they don't exist
    $columns = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('status', $columns)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN `status` ENUM('active', 'inactive', 'pending') DEFAULT 'active' AFTER `address`");
        echo "✅ Added status column to users table.\n";
    }
    
    if (!in_array('google_id', $columns)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN `google_id` VARCHAR(255) UNIQUE AFTER `id`");
        echo "✅ Added google_id column to users table.\n";
    }
    
    if (!in_array('created_at', $columns)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `status`");
        echo "✅ Added created_at column to users table.\n";
    }
    
    if (!in_array('updated_at', $columns)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`");
        echo "✅ Added updated_at column to users table.\n";
    }
    
    // Create contact_messages table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `contact_messages` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) NOT NULL,
            `subject` VARCHAR(255) NOT NULL,
            `message` TEXT NOT NULL,
            `status` ENUM('unread', 'read', 'replied', 'archived') DEFAULT 'unread',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "✅ Contact messages table created successfully.\n";
    
    // Create courses table with all required columns
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `courses` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(200) NOT NULL,
            `description` TEXT,
            `instructor_id` INT,
            `category` VARCHAR(100),
            `level` ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
            `price` DECIMAL(10,2) DEFAULT 0.00,
            `is_premium` BOOLEAN DEFAULT FALSE,
            `thumbnail` VARCHAR(255),
            `duration` INT DEFAULT 0,
            `total_lessons` INT DEFAULT 0,
            `total_students` INT DEFAULT 0,
            `rating` DECIMAL(3,2) DEFAULT 0.00,
            `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (`instructor_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
        )
    ");
    echo "✅ Courses table created successfully.\n";
    
    // Add missing columns to courses table if they don't exist
    $columns = $pdo->query("SHOW COLUMNS FROM courses")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('total_lessons', $columns)) {
        $pdo->exec("ALTER TABLE courses ADD COLUMN `total_lessons` INT DEFAULT 0 AFTER `duration`");
        echo "✅ Added total_lessons column to courses table.\n";
    }
    
    if (!in_array('total_students', $columns)) {
        $pdo->exec("ALTER TABLE courses ADD COLUMN `total_students` INT DEFAULT 0 AFTER `total_lessons`");
        echo "✅ Added total_students column to courses table.\n";
    }
    
    if (!in_array('rating', $columns)) {
        $pdo->exec("ALTER TABLE courses ADD COLUMN `rating` DECIMAL(3,2) DEFAULT 0.00 AFTER `total_students`");
        echo "✅ Added rating column to courses table.\n";
    }
    
    if (!in_array('status', $columns)) {
        $pdo->exec("ALTER TABLE courses ADD COLUMN `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft' AFTER `rating`");
        echo "✅ Added status column to courses table.\n";
    }
    
    if (!in_array('created_at', $columns)) {
        $pdo->exec("ALTER TABLE courses ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `status`");
        echo "✅ Added created_at column to courses table.\n";
    }
    
    if (!in_array('updated_at', $columns)) {
        $pdo->exec("ALTER TABLE courses ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`");
        echo "✅ Added updated_at column to courses table.\n";
    }
    
    // Insert default admin user
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute(['admin@edutechpro.com']);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['admin', 'admin@edutechpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', 'active']);
        echo "✅ Default admin user created.\n";
    } else {
        echo "ℹ️  Admin user already exists.\n";
    }
    
    // Insert sample teacher
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute(['teacher@edutechpro.com']);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['teacher1', 'teacher@edutechpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'teacher', 'active']);
        echo "✅ Sample teacher created.\n";
    } else {
        echo "ℹ️  Sample teacher already exists.\n";
    }
    
    // Insert sample student
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute(['student@edutechpro.com']);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['student1', 'student@edutechpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', 'student', 'active']);
        echo "✅ Sample student created.\n";
    } else {
        echo "ℹ️  Sample student already exists.\n";
    }
    
    // Insert sample courses
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE title = ?");
    $stmt->execute(['Complete Web Development']);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO courses (title, description, instructor_id, category, level, price, is_premium, total_lessons, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(['Complete Web Development', 'Master HTML, CSS, JavaScript, PHP, and MySQL to build modern web applications. This comprehensive course covers everything from basic HTML to advanced PHP development.', 2, 'Programming', 'beginner', 2999.00, TRUE, 5, 'published']);
        echo "✅ Sample course 'Complete Web Development' created.\n";
    } else {
        echo "ℹ️  Sample course 'Complete Web Development' already exists.\n";
    }
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE title = ?");
    $stmt->execute(['Data Science Fundamentals']);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO courses (title, description, instructor_id, category, level, price, is_premium, total_lessons, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(['Data Science Fundamentals', 'Learn the basics of data analysis, statistics, and machine learning algorithms. Perfect for beginners who want to enter the field of data science.', 2, 'Data Science', 'beginner', 0.00, FALSE, 3, 'published']);
        echo "✅ Sample course 'Data Science Fundamentals' created.\n";
    } else {
        echo "ℹ️  Sample course 'Data Science Fundamentals' already exists.\n";
    }
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE title = ?");
    $stmt->execute(['Mobile App Development']);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO courses (title, description, instructor_id, category, level, price, is_premium, total_lessons, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(['Mobile App Development', 'Build iOS and Android apps using React Native and Flutter frameworks. Learn cross-platform development and create professional mobile applications.', 2, 'Programming', 'intermediate', 3499.00, TRUE, 0, 'draft']);
        echo "✅ Sample course 'Mobile App Development' created.\n";
    } else {
        echo "ℹ️  Sample course 'Mobile App Development' already exists.\n";
    }
    
    echo "\n🎉 Database setup completed successfully!\n";
    echo "Default login credentials:\n";
    echo "Admin: admin@edutechpro.com / password\n";
    echo "Teacher: teacher@edutechpro.com / password\n";
    echo "Student: student@edutechpro.com / password\n";
    
} catch (PDOException $e) {
    die("❌ Database setup failed: " . $e->getMessage() . "\n");
}
?> 