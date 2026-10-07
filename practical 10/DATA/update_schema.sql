-- Run in phpMyAdmin if not already created
-- This adds a 'role' column to the students table for role-based access

USE studenthub_db;

-- Add role column to students table (student or admin)
ALTER TABLE students ADD COLUMN IF NOT EXISTS role VARCHAR(20) DEFAULT 'student';

-- (Optional) Insert a test admin account manually
-- Password is: admin123
INSERT INTO students (full_name, enrollment_id, email, mobile, password_hash, course, year, gender, role)
VALUES ('Admin User', 'ADMIN001', 'admin@studenthub.com', '9999999999', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', '1', 'Male', 'admin')
ON DUPLICATE KEY UPDATE role = 'admin';
