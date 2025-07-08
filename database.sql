-- EduTech Pro Database Schema
-- Created for E-Learning Platform

-- Create database
CREATE DATABASE IF NOT EXISTS `edutech_pro` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `edutech_pro`;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `role` ENUM('admin', 'teacher', 'student') NOT NULL,
    `profile_image` VARCHAR(255),
    `phone` VARCHAR(20),
    `address` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Courses table
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
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`instructor_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
);

-- Lessons table
CREATE TABLE IF NOT EXISTS `lessons` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT,
    `video_url` VARCHAR(255),
    `reading_material` TEXT,
    `duration` INT DEFAULT 0,
    `order_number` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
);

-- Enrollments table
CREATE TABLE IF NOT EXISTS `enrollments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `course_id` INT NOT NULL,
    `enrolled_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `progress` DECIMAL(5,2) DEFAULT 0.00,
    `completed_at` TIMESTAMP NULL,
    FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_enrollment` (`student_id`, `course_id`)
);

-- Quizzes table
CREATE TABLE IF NOT EXISTS `quizzes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT,
    `passing_score` INT DEFAULT 70,
    `time_limit` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
);

-- Quiz questions table
CREATE TABLE IF NOT EXISTS `quiz_questions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `quiz_id` INT NOT NULL,
    `question` TEXT NOT NULL,
    `option_a` TEXT NOT NULL,
    `option_b` TEXT NOT NULL,
    `option_c` TEXT NOT NULL,
    `option_d` TEXT NOT NULL,
    `correct_answer` ENUM('a', 'b', 'c', 'd') NOT NULL,
    `points` INT DEFAULT 1,
    FOREIGN KEY (`quiz_id`) REFERENCES `quizzes`(`id`) ON DELETE CASCADE
);

-- Quiz attempts table
CREATE TABLE IF NOT EXISTS `quiz_attempts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `quiz_id` INT NOT NULL,
    `score` INT DEFAULT 0,
    `total_questions` INT DEFAULT 0,
    `correct_answers` INT DEFAULT 0,
    `time_taken` INT DEFAULT 0,
    `passed` BOOLEAN DEFAULT FALSE,
    `attempted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`quiz_id`) REFERENCES `quizzes`(`id`) ON DELETE CASCADE
);

-- Certificates table
CREATE TABLE IF NOT EXISTS `certificates` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `course_id` INT NOT NULL,
    `certificate_number` VARCHAR(50) UNIQUE NOT NULL,
    `issued_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
);

-- Bounty points table
CREATE TABLE IF NOT EXISTS `bounty_points` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `points` INT DEFAULT 0,
    `source` VARCHAR(100),
    `earned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Attendance table
CREATE TABLE IF NOT EXISTS `attendance` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `course_id` INT NOT NULL,
    `lesson_id` INT,
    `status` ENUM('present', 'absent', 'late') DEFAULT 'present',
    `marked_by` INT,
    `marked_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`lesson_id`) REFERENCES `lessons`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`marked_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
);

-- Payments table
CREATE TABLE IF NOT EXISTS `payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `course_id` INT,
    `amount` DECIMAL(10,2) NOT NULL,
    `payment_method` VARCHAR(50),
    `transaction_id` VARCHAR(100),
    `status` ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    `payment_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE SET NULL
);

-- Live classes table
CREATE TABLE IF NOT EXISTS `live_classes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT,
    `meeting_url` VARCHAR(255),
    `scheduled_at` TIMESTAMP NOT NULL,
    `duration` INT DEFAULT 60,
    `status` ENUM('scheduled', 'ongoing', 'completed', 'cancelled') DEFAULT 'scheduled',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
);

-- Insert default admin user
INSERT IGNORE INTO `users` (`username`, `email`, `password`, `full_name`, `role`) 
VALUES ('admin', 'admin@edutechpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin');

-- Insert sample teacher
INSERT IGNORE INTO `users` (`username`, `email`, `password`, `full_name`, `role`) 
VALUES ('teacher1', 'teacher@edutechpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'teacher');

-- Insert sample student
INSERT IGNORE INTO `users` (`username`, `email`, `password`, `full_name`, `role`) 
VALUES ('student1', 'student@edutechpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', 'student');

-- Insert sample courses
INSERT IGNORE INTO `courses` (`title`, `description`, `instructor_id`, `category`, `level`, `price`, `is_premium`) 
VALUES 
('Complete Web Development', 'Master HTML, CSS, JavaScript, PHP, and MySQL to build modern web applications. This comprehensive course covers everything from basic HTML to advanced PHP development.', 2, 'Programming', 'beginner', 2999.00, TRUE),
('Data Science Fundamentals', 'Learn the basics of data analysis, statistics, and machine learning algorithms. Perfect for beginners who want to enter the field of data science.', 2, 'Data Science', 'beginner', 0.00, FALSE),
('Mobile App Development', 'Build iOS and Android apps using React Native and Flutter frameworks. Learn cross-platform development and create professional mobile applications.', 2, 'Programming', 'intermediate', 3499.00, TRUE),
('Digital Marketing Mastery', 'Learn SEO, social media marketing, content marketing, and PPC advertising. Master the art of digital marketing and grow your business online.', 2, 'Marketing', 'beginner', 1999.00, TRUE),
('Graphic Design Fundamentals', 'Master Adobe Photoshop, Illustrator, and InDesign. Create stunning graphics, logos, and marketing materials.', 2, 'Design', 'beginner', 2499.00, TRUE);

-- Insert sample lessons for Web Development course
INSERT IGNORE INTO `lessons` (`course_id`, `title`, `description`, `video_url`, `reading_material`, `duration`, `order_number`) 
VALUES 
(1, 'Introduction to HTML', 'Learn the basics of HTML markup language', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'HTML is the standard markup language for creating web pages...', 45, 1),
(1, 'CSS Styling Basics', 'Master CSS for beautiful web design', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'CSS is used to style and layout web pages...', 60, 2),
(1, 'JavaScript Fundamentals', 'Learn JavaScript programming basics', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'JavaScript is a programming language that adds interactivity...', 75, 3),
(1, 'PHP Backend Development', 'Build server-side applications with PHP', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'PHP is a server-side scripting language...', 90, 4),
(1, 'MySQL Database Management', 'Learn database design and management', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'MySQL is a popular open-source database...', 60, 5);

-- Insert sample lessons for Data Science course
INSERT IGNORE INTO `lessons` (`course_id`, `title`, `description`, `video_url`, `reading_material`, `duration`, `order_number`) 
VALUES 
(2, 'Introduction to Data Science', 'Understanding the field of data science', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'Data science is an interdisciplinary field...', 30, 1),
(2, 'Python for Data Analysis', 'Learn Python programming for data analysis', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'Python is a powerful programming language...', 60, 2),
(2, 'Statistical Analysis', 'Master statistical concepts and methods', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'Statistics is the science of collecting...', 45, 3);

-- Insert sample quizzes
INSERT IGNORE INTO `quizzes` (`course_id`, `title`, `description`, `passing_score`, `time_limit`) 
VALUES 
(1, 'HTML Basics Quiz', 'Test your knowledge of HTML fundamentals', 70, 30),
(1, 'CSS Styling Quiz', 'Evaluate your CSS skills', 70, 45),
(2, 'Data Science Fundamentals', 'Test your understanding of data science basics', 70, 30);

-- Insert sample quiz questions
INSERT IGNORE INTO `quiz_questions` (`quiz_id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `points`) 
VALUES 
(1, 'What does HTML stand for?', 'Hyper Text Markup Language', 'High Tech Modern Language', 'Home Tool Markup Language', 'Hyperlink and Text Markup Language', 'a', 1),
(1, 'Which HTML tag is used for creating a hyperlink?', '<link>', '<a>', '<href>', '<url>', 'b', 1),
(1, 'What is the correct HTML element for inserting a line break?', '<break>', '<lb>', '<br>', '<newline>', 'c', 1),
(2, 'What does CSS stand for?', 'Computer Style Sheets', 'Creative Style Sheets', 'Cascading Style Sheets', 'Colorful Style Sheets', 'c', 1),
(2, 'Which CSS property controls the text size?', 'font-style', 'text-size', 'font-size', 'text-style', 'c', 1),
(3, 'What is the primary goal of data science?', 'To create beautiful websites', 'To extract insights from data', 'To design mobile apps', 'To write code', 'b', 1);

-- Insert sample enrollments
INSERT IGNORE INTO `enrollments` (`student_id`, `course_id`, `progress`) 
VALUES 
(3, 2, 75.00),  -- Student enrolled in Data Science course
(3, 1, 45.00);  -- Student enrolled in Web Development course

-- Insert sample bounty points
INSERT IGNORE INTO `bounty_points` (`student_id`, `points`, `source`) 
VALUES 
(3, 25, 'Course Enrollment - Data Science'),
(3, 50, 'Course Enrollment - Web Development'),
(3, 10, 'Quiz Completion - HTML Basics');

-- Insert sample quiz attempts
INSERT IGNORE INTO `quiz_attempts` (`student_id`, `quiz_id`, `score`, `total_questions`, `correct_answers`, `passed`) 
VALUES 
(3, 1, 80, 3, 3, TRUE),
(3, 3, 85, 1, 1, TRUE);

-- Insert sample certificates
INSERT IGNORE INTO `certificates` (`student_id`, `course_id`, `certificate_number`) 
VALUES 
(3, 2, 'CERT-DS-2024-001');

-- Insert sample payments
INSERT IGNORE INTO `payments` (`student_id`, `course_id`, `amount`, `payment_method`, `transaction_id`, `status`) 
VALUES 
(3, 1, 2999.00, 'credit_card', 'TXN20241201001', 'completed');

-- Insert sample live classes
INSERT IGNORE INTO `live_classes` (`course_id`, `title`, `description`, `meeting_url`, `scheduled_at`, `duration`) 
VALUES 
(1, 'Web Development Live Session', 'Live coding session on HTML and CSS', 'https://meet.google.com/abc-defg-hij', '2024-12-15 14:00:00', 60),
(2, 'Data Science Q&A Session', 'Question and answer session on data science topics', 'https://meet.google.com/xyz-uvw-rst', '2024-12-16 15:00:00', 45);

-- Insert sample attendance
INSERT IGNORE INTO `attendance` (`student_id`, `course_id`, `lesson_id`, `status`, `marked_by`) 
VALUES 
(3, 2, 1, 'present', 2),
(3, 2, 2, 'present', 2),
(3, 1, 1, 'present', 2);

-- Create indexes for better performance
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_courses_instructor ON courses(instructor_id);
CREATE INDEX idx_courses_category ON courses(category);
CREATE INDEX idx_enrollments_student ON enrollments(student_id);
CREATE INDEX idx_enrollments_course ON enrollments(course_id);
CREATE INDEX idx_lessons_course ON lessons(course_id);
CREATE INDEX idx_quizzes_course ON quizzes(course_id);
CREATE INDEX idx_quiz_questions_quiz ON quiz_questions(quiz_id);
CREATE INDEX idx_quiz_attempts_student ON quiz_attempts(student_id);
CREATE INDEX idx_quiz_attempts_quiz ON quiz_attempts(quiz_id);
CREATE INDEX idx_certificates_student ON certificates(student_id);
CREATE INDEX idx_bounty_points_student ON bounty_points(student_id);
CREATE INDEX idx_attendance_student ON attendance(student_id);
CREATE INDEX idx_payments_student ON payments(student_id);
CREATE INDEX idx_live_classes_course ON live_classes(course_id);

-- Show success message
SELECT 'Database created successfully!' as message; 