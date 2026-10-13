-- Run this in phpMyAdmin to add poster column to existing events table
USE studenthub_db;

-- Add poster column to events table (stores the uploaded image filename)
ALTER TABLE events ADD COLUMN IF NOT EXISTS poster VARCHAR(255) DEFAULT NULL;
