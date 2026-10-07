-- Run these commands in phpMyAdmin or MySQL console to create the database and tables.

CREATE DATABASE IF NOT EXISTS studenthub_db;
USE studenthub_db;

-- 1. Students Table (Stores registered student accounts)
CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    enrollment_id VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL,
    password_hash VARCHAR(255) NOT NULL, 
    course VARCHAR(50) NOT NULL,
    year VARCHAR(10) NOT NULL,
    gender VARCHAR(10) NOT NULL
);

-- 2. Events Table (Stores available events)
CREATE TABLE IF NOT EXISTS events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL
);

-- 3. Registrations Table (Stores which student registered for which event)
CREATE TABLE IF NOT EXISTS registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE
);
