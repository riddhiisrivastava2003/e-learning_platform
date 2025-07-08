-- ELMS Database Migration Script
-- Run this if you have an existing database without the status column

USE `edutech_pro`;

-- Add status column to users table if it doesn't exist
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `status` ENUM('active', 'inactive', 'pending') DEFAULT 'active' AFTER `address`;

-- Add google_id column to users table if it doesn't exist
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `google_id` VARCHAR(255) UNIQUE AFTER `id`;

-- Add created_at and updated_at columns if they don't exist
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `status`,
ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Add contact_messages table if it doesn't exist
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

-- Add missing columns to courses table if they don't exist
ALTER TABLE `courses` 
ADD COLUMN IF NOT EXISTS `total_lessons` INT DEFAULT 0 AFTER `duration`,
ADD COLUMN IF NOT EXISTS `total_students` INT DEFAULT 0 AFTER `total_lessons`,
ADD COLUMN IF NOT EXISTS `rating` DECIMAL(3,2) DEFAULT 0.00 AFTER `total_students`,
ADD COLUMN IF NOT EXISTS `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft' AFTER `rating`,
ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `status`,
ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Update existing users to have active status
UPDATE `users` SET `status` = 'active' WHERE `status` IS NULL;

-- Insert default admin user if not exists
INSERT IGNORE INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `status`) 
VALUES ('admin', 'admin@edutechpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', 'active');

-- Insert sample teacher if not exists
INSERT IGNORE INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `status`) 
VALUES ('teacher1', 'teacher@edutechpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'teacher', 'active');

-- Insert sample student if not exists
INSERT IGNORE INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `status`) 
VALUES ('student1', 'student@edutechpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', 'student', 'active');

-- Show success message
SELECT 'Database migration completed successfully!' as message; 