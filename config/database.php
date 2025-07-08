<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'edutech_pro');

// Create connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to create database and tables if they don't exist
function createDatabaseAndTables() {
    global $pdo;
    
    try {
        // Create database if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE " . DB_NAME);
        
        // Create users table
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            role ENUM('admin', 'teacher', 'student') NOT NULL,
            profile_image VARCHAR(255),
            phone VARCHAR(20),
            address TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Create courses table
        $pdo->exec("CREATE TABLE IF NOT EXISTS courses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            description TEXT,
            instructor_id INT,
            category VARCHAR(100),
            level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
            price DECIMAL(10,2) DEFAULT 0.00,
            is_premium BOOLEAN DEFAULT FALSE,
            thumbnail VARCHAR(255),
            duration INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE SET NULL
        )");
        
        // Create lessons table
        $pdo->exec("CREATE TABLE IF NOT EXISTS lessons (
            id INT AUTO_INCREMENT PRIMARY KEY,
            course_id INT NOT NULL,
            title VARCHAR(200) NOT NULL,
            description TEXT,
            video_url VARCHAR(255),
            reading_material TEXT,
            duration INT DEFAULT 0,
            order_number INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
        )");
        
        // Create enrollments table
        $pdo->exec("CREATE TABLE IF NOT EXISTS enrollments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            course_id INT NOT NULL,
            enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            progress DECIMAL(5,2) DEFAULT 0.00,
            completed_at TIMESTAMP NULL,
            FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
            UNIQUE KEY unique_enrollment (student_id, course_id)
        )");
        
        // Create quizzes table
        $pdo->exec("CREATE TABLE IF NOT EXISTS quizzes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            course_id INT NOT NULL,
            title VARCHAR(200) NOT NULL,
            description TEXT,
            passing_score INT DEFAULT 70,
            time_limit INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
        )");
        
        // Create quiz_questions table
        $pdo->exec("CREATE TABLE IF NOT EXISTS quiz_questions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            quiz_id INT NOT NULL,
            question TEXT NOT NULL,
            option_a TEXT NOT NULL,
            option_b TEXT NOT NULL,
            option_c TEXT NOT NULL,
            option_d TEXT NOT NULL,
            correct_answer ENUM('a', 'b', 'c', 'd') NOT NULL,
            points INT DEFAULT 1,
            FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
        )");
        
        // Create quiz_attempts table
        $pdo->exec("CREATE TABLE IF NOT EXISTS quiz_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            quiz_id INT NOT NULL,
            score INT DEFAULT 0,
            total_questions INT DEFAULT 0,
            correct_answers INT DEFAULT 0,
            time_taken INT DEFAULT 0,
            passed BOOLEAN DEFAULT FALSE,
            attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
        )");
        
        // Create certificates table
        $pdo->exec("CREATE TABLE IF NOT EXISTS certificates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            course_id INT NOT NULL,
            certificate_number VARCHAR(50) UNIQUE NOT NULL,
            issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
        )");
        
        // Create bounty_points table
        $pdo->exec("CREATE TABLE IF NOT EXISTS bounty_points (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            points INT DEFAULT 0,
            source VARCHAR(100),
            earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
        )");
        
        // Create attendance table
        $pdo->exec("CREATE TABLE IF NOT EXISTS attendance (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            course_id INT NOT NULL,
            lesson_id INT,
            status ENUM('present', 'absent', 'late') DEFAULT 'present',
            marked_by INT,
            marked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
            FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
            FOREIGN KEY (marked_by) REFERENCES users(id) ON DELETE SET NULL
        )");
        
        // Create payments table
        $pdo->exec("CREATE TABLE IF NOT EXISTS payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            course_id INT,
            amount DECIMAL(10,2) NOT NULL,
            payment_method VARCHAR(50),
            transaction_id VARCHAR(100),
            status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
            payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL
        )");
        
        // Create live_classes table
        $pdo->exec("CREATE TABLE IF NOT EXISTS live_classes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            course_id INT NOT NULL,
            title VARCHAR(200) NOT NULL,
            description TEXT,
            meeting_url VARCHAR(255),
            scheduled_at TIMESTAMP NOT NULL,
            duration INT DEFAULT 60,
            status ENUM('scheduled', 'ongoing', 'completed', 'cancelled') DEFAULT 'scheduled',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
        )");
        
        // Insert default admin user
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT IGNORE INTO users (username, email, password, full_name, role) 
                   VALUES ('admin', 'admin@edutechpro.com', '$adminPassword', 'System Administrator', 'admin')");
        
        // Insert sample teacher
        $teacherPassword = password_hash('teacher123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT IGNORE INTO users (username, email, password, full_name, role) 
                   VALUES ('teacher1', 'teacher@edutechpro.com', '$teacherPassword', 'John Doe', 'teacher')");
        
        // Insert sample student
        $studentPassword = password_hash('student123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT IGNORE INTO users (username, email, password, full_name, role) 
                   VALUES ('student1', 'student@edutechpro.com', '$studentPassword', 'Jane Smith', 'student')");
        
        // Insert sample courses
        $pdo->exec("INSERT IGNORE INTO courses (title, description, instructor_id, category, level, price, is_premium) 
                   VALUES 
                   ('Complete Web Development', 'Master HTML, CSS, JavaScript, PHP, and MySQL', 2, 'Programming', 'beginner', 2999.00, TRUE),
                   ('Data Science Fundamentals', 'Learn the basics of data analysis and machine learning', 2, 'Data Science', 'beginner', 0.00, FALSE),
                   ('Mobile App Development', 'Build iOS and Android apps using React Native', 2, 'Programming', 'intermediate', 3499.00, TRUE)");
        
        
        
    } catch(PDOException $e) {
        die("Error creating database: " . $e->getMessage());
    }
}

// Call the function to create database and tables
createDatabaseAndTables();
?> 