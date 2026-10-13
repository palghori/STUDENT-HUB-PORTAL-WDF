-- ============================================================
-- STUDENT HUB PORTAL - Practical 14 Database Schema & Seed Data
-- Topics: Role-Based Access Control, Dynamic Menu, Audit Logs
-- ============================================================

USE studenthub_db;

-- 1. Ensure 'role' column exists in students table
ALTER TABLE students ADD COLUMN IF NOT EXISTS role VARCHAR(20) DEFAULT 'student';

-- 2. Create Audit Log table to record important system/admin actions
CREATE TABLE IF NOT EXISTS audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(100) NOT NULL,
    user_role VARCHAR(20) NOT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45) DEFAULT '127.0.0.1',
    log_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Insert or update Admin User account
-- Default Login: admin@studenthub.com / Password: admin123
INSERT INTO students (full_name, enrollment_id, email, mobile, password_hash, course, year, gender, role)
VALUES ('System Admin', 'ADM001', 'admin@studenthub.com', '9876543210', '$2y$10$lQzLeI4KI6mMEBJHdxDS1ORCFM.7np7WswC1TzbXvYZ/x3D0BDgFK', 'Administration', 'Final', 'Other', 'admin')
ON DUPLICATE KEY UPDATE role = 'admin';

-- 4. Insert or update Student User accounts
-- Default Login: aarav@studenthub.com / Password: student123
INSERT INTO students (full_name, enrollment_id, email, mobile, password_hash, course, year, gender, role)
VALUES 
('Aarav Sharma', 'ENR101', 'aarav@studenthub.com', '9871112233', '$2y$10$ZgKm01KU1SkNPGG.R9CTd.6IdMlJ4TO8ARrBXtT1B9IggqBB1W3vC', 'Computer Science', '2nd Year', 'Male', 'student'),
('Diya Patel', 'ENR102', 'diya@studenthub.com', '9874445566', '$2y$10$ZgKm01KU1SkNPGG.R9CTd.6IdMlJ4TO8ARrBXtT1B9IggqBB1W3vC', 'Information Technology', '3rd Year', 'Female', 'student')
ON DUPLICATE KEY UPDATE role = 'student';

-- 5. Insert Sample Events (if table is empty)
INSERT IGNORE INTO events (event_id, title, category, description, event_date)
VALUES 
(1, 'TechFest Hackathon 2026', 'Technical', '24-hour coding challenge and innovation competition', '2026-11-15'),
(2, 'Web Development Workshop', 'Workshop', 'Hands-on practical on modern full-stack web development', '2026-10-25'),
(3, 'Annual Cultural Fest', 'Cultural', 'Music, dance, drama and stage performances', '2026-12-05');

-- 6. Insert Sample Registrations
INSERT IGNORE INTO registrations (registration_id, student_id, event_id)
VALUES 
(1, 1, 1),
(2, 2, 1),
(3, 3, 2);

-- 7. Insert Initial Audit Log Record
INSERT INTO audit_logs (user_email, user_role, action, details)
VALUES 
('system@studenthub.com', 'system', 'SYSTEM_INIT', 'Practical 14 Database initialized with RBAC and Audit Logging');
