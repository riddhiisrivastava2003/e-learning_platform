-- EduTech Pro Database Schema - Updated Version
-- Created for E-Learning Platform with Teacher and Admin Registration

-- Create database
CREATE DATABASE IF NOT EXISTS `edutech_pro` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `edutech_pro`;

-- Users table (updated with Google OAuth support)
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
);

-- Teacher details table
CREATE TABLE IF NOT EXISTS `teacher_details` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `teacher_id` INT NOT NULL,
    `qualification` VARCHAR(200) NOT NULL,
    `experience` VARCHAR(100) NOT NULL,
    `specialization` VARCHAR(200) NOT NULL,
    `bio` TEXT,
    `linkedin` VARCHAR(255),
    `website` VARCHAR(255),
    `hourly_rate` DECIMAL(10,2) DEFAULT 0.00,
    `rating` DECIMAL(3,2) DEFAULT 0.00,
    `total_students` INT DEFAULT 0,
    `total_courses` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`teacher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Admin details table
CREATE TABLE IF NOT EXISTS `admin_details` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `admin_id` INT NOT NULL,
    `department` VARCHAR(100) NOT NULL,
    `position` VARCHAR(100) NOT NULL,
    `permissions` JSON,
    `last_login` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`admin_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Student details table
CREATE TABLE IF NOT EXISTS `student_details` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `date_of_birth` DATE,
    `gender` ENUM('male', 'female', 'other'),
    `education_level` VARCHAR(100),
    `institution` VARCHAR(200),
    `interests` TEXT,
    `goals` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
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
    `total_lessons` INT DEFAULT 0,
    `total_students` INT DEFAULT 0,
    `rating` DECIMAL(3,2) DEFAULT 0.00,
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
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
    `is_free` BOOLEAN DEFAULT FALSE,
    `status` ENUM('draft', 'published') DEFAULT 'draft',
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
    `last_accessed` TIMESTAMP NULL,
    `status` ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
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
    `total_questions` INT DEFAULT 0,
    `is_required` BOOLEAN DEFAULT TRUE,
    `status` ENUM('draft', 'published') DEFAULT 'draft',
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
    `explanation` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
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
    `expires_at` TIMESTAMP NULL,
    `status` ENUM('active', 'expired', 'revoked') DEFAULT 'active',
    FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
);

-- Bounty points table
CREATE TABLE IF NOT EXISTS `bounty_points` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `points` INT DEFAULT 0,
    `source` VARCHAR(100),
    `description` TEXT,
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
    `notes` TEXT,
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
    `status` ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    `payment_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `refund_date` TIMESTAMP NULL,
    `notes` TEXT,
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
    `meeting_id` VARCHAR(100),
    `meeting_password` VARCHAR(50),
    `scheduled_at` TIMESTAMP NOT NULL,
    `duration` INT DEFAULT 60,
    `max_participants` INT DEFAULT 100,
    `status` ENUM('scheduled', 'ongoing', 'completed', 'cancelled') DEFAULT 'scheduled',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
);

-- Live class participants table
CREATE TABLE IF NOT EXISTS `live_class_participants` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `live_class_id` INT NOT NULL,
    `student_id` INT NOT NULL,
    `joined_at` TIMESTAMP NULL,
    `left_at` TIMESTAMP NULL,
    `duration_attended` INT DEFAULT 0,
    `status` ENUM('registered', 'attended', 'absent') DEFAULT 'registered',
    FOREIGN KEY (`live_class_id`) REFERENCES `live_classes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Notifications table
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `message` TEXT NOT NULL,
    `type` ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- System settings table
CREATE TABLE IF NOT EXISTS `system_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) UNIQUE NOT NULL,
    `setting_value` TEXT,
    `description` TEXT,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact messages table
CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('unread', 'read', 'replied', 'archived') DEFAULT 'unread',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT IGNORE INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `status`) 
VALUES ('admin', 'admin@edutechpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', 'active');

-- Insert sample teacher
INSERT IGNORE INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `status`) 
VALUES ('teacher1', 'teacher@edutechpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'teacher', 'active');

-- Insert sample student
INSERT IGNORE INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `status`) 
VALUES ('student1', 'student@edutechpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', 'student', 'active');

-- Insert teacher details
INSERT IGNORE INTO `teacher_details` (`teacher_id`, `qualification`, `experience`, `specialization`, `bio`, `linkedin`, `website`) 
VALUES (2, 'M.Tech in Computer Science', '5+ years', 'Web Development, Data Science', 'Experienced software engineer with expertise in modern web technologies and data science.', 'https://linkedin.com/in/johndoe', 'https://johndoe.dev');

-- Insert admin details
INSERT IGNORE INTO `admin_details` (`admin_id`, `department`, `position`, `permissions`) 
VALUES (1, 'IT Administration', 'System Administrator', '["all"]');

-- Insert student details
INSERT IGNORE INTO `student_details` (`student_id`, `date_of_birth`, `gender`, `education_level`, `institution`) 
VALUES (3, '2000-05-15', 'female', 'Bachelor\'s Degree', 'University of Technology');

-- Insert sample courses
INSERT IGNORE INTO `courses` (`title`, `description`, `instructor_id`, `category`, `level`, `price`, `is_premium`, `total_lessons`, `status`) 
VALUES 
('Complete Web Development', 'Master HTML, CSS, JavaScript, PHP, and MySQL to build modern web applications. This comprehensive course covers everything from basic HTML to advanced PHP development.', 2, 'Programming', 'beginner', 2999.00, TRUE, 5, 'published'),
('Data Science Fundamentals', 'Learn the basics of data analysis, statistics, and machine learning algorithms. Perfect for beginners who want to enter the field of data science.', 2, 'Data Science', 'beginner', 0.00, FALSE, 3, 'published'),
('Mobile App Development', 'Build iOS and Android apps using React Native and Flutter frameworks. Learn cross-platform development and create professional mobile applications.', 2, 'Programming', 'intermediate', 3499.00, TRUE, 0, 'draft'),
('Digital Marketing Mastery', 'Learn SEO, social media marketing, content marketing, and PPC advertising. Master the art of digital marketing and grow your business online.', 2, 'Marketing', 'beginner', 1999.00, TRUE, 0, 'draft'),
('Graphic Design Fundamentals', 'Master Adobe Photoshop, Illustrator, and InDesign. Create stunning graphics, logos, and marketing materials.', 2, 'Design', 'beginner', 2499.00, TRUE, 0, 'draft');

-- Insert sample lessons for Web Development course
INSERT IGNORE INTO `lessons` (`course_id`, `title`, `description`, `video_url`, `reading_material`, `duration`, `order_number`, `is_free`, `status`) 
VALUES 
(1, 'Introduction to HTML', 'Learn the basics of HTML markup language', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'HTML is the standard markup language for creating web pages...', 45, 1, TRUE, 'published'),
(1, 'CSS Styling Basics', 'Master CSS for beautiful web design', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'CSS is used to style and layout web pages...', 60, 2, FALSE, 'published'),
(1, 'JavaScript Fundamentals', 'Learn JavaScript programming basics', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'JavaScript is a programming language that adds interactivity...', 75, 3, FALSE, 'published'),
(1, 'PHP Backend Development', 'Build server-side applications with PHP', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'PHP is a server-side scripting language...', 90, 4, FALSE, 'published'),
(1, 'MySQL Database Management', 'Learn database design and management', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'MySQL is a popular open-source database...', 60, 5, FALSE, 'published');

-- Insert sample lessons for Data Science course
INSERT IGNORE INTO `lessons` (`course_id`, `title`, `description`, `video_url`, `reading_material`, `duration`, `order_number`, `is_free`, `status`) 
VALUES 
(2, 'Introduction to Data Science', 'Understanding the field of data science', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'Data science is an interdisciplinary field...', 30, 1, TRUE, 'published'),
(2, 'Python for Data Analysis', 'Learn Python programming for data analysis', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'Python is a powerful programming language...', 60, 2, TRUE, 'published'),
(2, 'Statistical Analysis', 'Master statistical concepts and methods', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'Statistics is the science of collecting...', 45, 3, TRUE, 'published');

-- Insert sample quizzes
INSERT IGNORE INTO `quizzes` (`course_id`, `title`, `description`, `passing_score`, `time_limit`, `total_questions`, `status`) 
VALUES 
(1, 'HTML Basics Quiz', 'Test your knowledge of HTML fundamentals', 70, 30, 3, 'published'),
(1, 'CSS Styling Quiz', 'Evaluate your CSS skills', 70, 45, 2, 'published'),
(2, 'Data Science Fundamentals', 'Test your understanding of data science basics', 70, 30, 1, 'published');

-- Insert sample quiz questions
INSERT IGNORE INTO `quiz_questions` (`quiz_id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `points`, `explanation`) 
VALUES 
(1, 'What does HTML stand for?', 'Hyper Text Markup Language', 'High Tech Modern Language', 'Home Tool Markup Language', 'Hyperlink and Text Markup Language', 'a', 1, 'HTML stands for Hyper Text Markup Language'),
(1, 'Which HTML tag is used for creating a hyperlink?', '<link>', '<a>', '<href>', '<url>', 'b', 1, 'The <a> tag is used to create hyperlinks in HTML'),
(1, 'What is the correct HTML element for inserting a line break?', '<break>', '<lb>', '<br>', '<newline>', 'c', 1, 'The <br> tag is used for line breaks'),
(2, 'What does CSS stand for?', 'Computer Style Sheets', 'Creative Style Sheets', 'Cascading Style Sheets', 'Colorful Style Sheets', 'c', 1, 'CSS stands for Cascading Style Sheets'),
(2, 'Which CSS property controls the text size?', 'font-style', 'text-size', 'font-size', 'text-style', 'c', 1, 'The font-size property controls text size'),
(3, 'What is the primary goal of data science?', 'To create beautiful websites', 'To extract insights from data', 'To design mobile apps', 'To write code', 'b', 1, 'Data science aims to extract insights from data');

-- Insert sample enrollments
INSERT IGNORE INTO `enrollments` (`student_id`, `course_id`, `progress`, `status`) 
VALUES 
(3, 2, 75.00, 'active'),  -- Student enrolled in Data Science course
(3, 1, 45.00, 'active');  -- Student enrolled in Web Development course

-- Insert sample bounty points
INSERT IGNORE INTO `bounty_points` (`student_id`, `points`, `source`, `description`) 
VALUES 
(3, 25, 'Course Enrollment', 'Enrolled in Data Science course'),
(3, 50, 'Course Enrollment', 'Enrolled in Web Development course'),
(3, 10, 'Quiz Completion', 'Completed HTML Basics Quiz');

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
INSERT IGNORE INTO `live_classes` (`course_id`, `title`, `description`, `meeting_url`, `scheduled_at`, `duration`, `max_participants`) 
VALUES 
(1, 'Web Development Live Session', 'Live coding session on HTML and CSS', 'https://meet.google.com/abc-defg-hij', '2024-12-15 14:00:00', 60, 50),
(2, 'Data Science Q&A Session', 'Question and answer session on data science topics', 'https://meet.google.com/xyz-uvw-rst', '2024-12-16 15:00:00', 45, 30);

-- Insert sample attendance
INSERT IGNORE INTO `attendance` (`student_id`, `course_id`, `lesson_id`, `status`, `marked_by`) 
VALUES 
(3, 2, 1, 'present', 2),
(3, 2, 2, 'present', 2),
(3, 1, 1, 'present', 2);

-- Insert sample notifications
INSERT IGNORE INTO `notifications` (`user_id`, `title`, `message`, `type`) 
VALUES 
(3, 'Welcome to EduTech Pro!', 'Thank you for joining our learning platform. Start exploring courses and begin your learning journey!', 'success'),
(2, 'New Student Enrollment', 'Jane Smith has enrolled in your Web Development course.', 'info');

-- Insert system settings
INSERT IGNORE INTO `system_settings` (`setting_key`, `setting_value`, `description`) 
VALUES 
('site_name', 'EduTech Pro', 'Website name'),
('site_description', 'Premium E-Learning Platform', 'Website description'),
('admin_email', 'admin@edutechpro.com', 'Admin contact email'),
('max_file_size', '10485760', 'Maximum file upload size in bytes'),
('allowed_file_types', 'jpg,jpeg,png,gif,pdf,doc,docx,mp4,avi,mov', 'Allowed file types for uploads'),
('default_currency', 'INR', 'Default currency for payments'),
('bounty_points_enrollment', '25', 'Bounty points for course enrollment'),
('bounty_points_quiz', '10', 'Bounty points for quiz completion'),
('certificate_validity', '365', 'Certificate validity in days');

-- Create indexes for better performance
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_teacher_details_teacher ON teacher_details(teacher_id);
CREATE INDEX idx_admin_details_admin ON admin_details(admin_id);
CREATE INDEX idx_student_details_student ON student_details(student_id);
CREATE INDEX idx_courses_instructor ON courses(instructor_id);
CREATE INDEX idx_courses_category ON courses(category);
CREATE INDEX idx_courses_status ON courses(status);
CREATE INDEX idx_enrollments_student ON enrollments(student_id);
CREATE INDEX idx_enrollments_course ON enrollments(course_id);
CREATE INDEX idx_enrollments_status ON enrollments(status);
CREATE INDEX idx_lessons_course ON lessons(course_id);
CREATE INDEX idx_lessons_status ON lessons(status);
CREATE INDEX idx_quizzes_course ON quizzes(course_id);
CREATE INDEX idx_quizzes_status ON quizzes(status);
CREATE INDEX idx_quiz_questions_quiz ON quiz_questions(quiz_id);
CREATE INDEX idx_quiz_attempts_student ON quiz_attempts(student_id);
CREATE INDEX idx_quiz_attempts_quiz ON quiz_attempts(quiz_id);
CREATE INDEX idx_certificates_student ON certificates(student_id);
CREATE INDEX idx_certificates_status ON certificates(status);
CREATE INDEX idx_bounty_points_student ON bounty_points(student_id);
CREATE INDEX idx_attendance_student ON attendance(student_id);
CREATE INDEX idx_payments_student ON payments(student_id);
CREATE INDEX idx_payments_status ON payments(status);
CREATE INDEX idx_live_classes_course ON live_classes(course_id);
CREATE INDEX idx_live_classes_status ON live_classes(status);
CREATE INDEX idx_notifications_user ON notifications(user_id);
CREATE INDEX idx_notifications_read ON notifications(is_read);

 -- Show success message
-- SELECT 'Updated database created successfully!' as message; 