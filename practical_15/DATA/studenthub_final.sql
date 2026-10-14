-- ======================================================================
-- STUDENT HUB PORTAL - Practical 15 Consolidated Master Database Script
-- Full Schema & Initial Seed Data for Complete Local Deployment
-- ======================================================================

CREATE DATABASE IF NOT EXISTS studenthub_db;
USE studenthub_db;

-- --------------------------------------------------------
-- Table 1: students (Accounts for Students and Administrators)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    enrollment_id VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    course VARCHAR(50) NOT NULL,
    year VARCHAR(10) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    role VARCHAR(20) DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- Table 2: events (Campus Events, Workshops & Festivals)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    poster VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- Table 3: registrations (Event Enrollments)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- Table 4: audit_logs (Security & Admin Activity Trail)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(100) NOT NULL,
    user_role VARCHAR(20) NOT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45) DEFAULT '127.0.0.1',
    log_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- SEED DATA
-- --------------------------------------------------------

-- 1. Default Administrator Account (admin@studenthub.com / admin123)
INSERT INTO students (full_name, enrollment_id, email, mobile, password_hash, course, year, gender, role)
VALUES ('System Admin', 'ADM001', 'admin@studenthub.com', '9876543210', '$2y$10$lQzLeI4KI6mMEBJHdxDS1ORCFM.7np7WswC1TzbXvYZ/x3D0BDgFK', 'Administration', 'Final', 'Other', 'admin')
ON DUPLICATE KEY UPDATE role = 'admin';

-- 2. Default Student Accounts (Password: student123)
INSERT INTO students (full_name, enrollment_id, email, mobile, password_hash, course, year, gender, role)
VALUES 
('Aarav Sharma', 'ENR101', 'aarav@studenthub.com', '9871112233', '$2y$10$ZgKm01KU1SkNPGG.R9CTd.6IdMlJ4TO8ARrBXtT1B9IggqBB1W3vC', 'Computer Science', '2nd Year', 'Male', 'student'),
('Diya Patel', 'ENR102', 'diya@studenthub.com', '9874445566', '$2y$10$ZgKm01KU1SkNPGG.R9CTd.6IdMlJ4TO8ARrBXtT1B9IggqBB1W3vC', 'Information Technology', '3rd Year', 'Female', 'student')
ON DUPLICATE KEY UPDATE role = 'student';

-- 3. Default Events
INSERT IGNORE INTO events (event_id, title, category, description, event_date, poster)
VALUES 
(1, 'TechFest Hackathon 2026', 'Technical', '24-hour coding challenge and product development hackathon.', '2026-11-15', NULL),
(2, 'Web Development Workshop', 'Workshop', 'Hands-on practical full-stack web development session with PHP and MySQL.', '2026-10-25', NULL),
(3, 'Annual Cultural Fiesta', 'Cultural', 'Music performances, theatrical dance, and talent competition.', '2026-12-05', NULL);

-- 4. Default Registrations
INSERT IGNORE INTO registrations (registration_id, student_id, event_id)
VALUES 
(1, 1, 1),
(2, 2, 1),
(3, 3, 2);

-- 5. Default Audit Log Entry
INSERT INTO audit_logs (user_email, user_role, action, details)
VALUES 
('system@studenthub.com', 'system', 'PORTAL_INIT', 'StudentHub Final Integrated Portal initialized successfully.');
