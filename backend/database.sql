-- Trinity Academy Faculty Performance Tracker Database
-- Created: October 31, 2025

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS faculty_tracker;
USE faculty_tracker;

-- =====================================================
-- TABLE: users (for all roles: student, faculty, hod)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'faculty', 'hod') NOT NULL,
    enrollment_no VARCHAR(50) DEFAULT NULL,
    class VARCHAR(100) DEFAULT NULL,
    subject VARCHAR(255) DEFAULT NULL,
    department VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: feedbacks
-- =====================================================
CREATE TABLE IF NOT EXISTS feedbacks (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    faculty_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    rating DECIMAL(2,1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comments TEXT,
    feedback_date DATE NOT NULL,
    feedback_time TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_student (student_id),
    INDEX idx_faculty (faculty_id),
    INDEX idx_rating (rating),
    INDEX idx_feedback_date (feedback_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: complaints
-- =====================================================
CREATE TABLE IF NOT EXISTS complaints (
    complaint_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    faculty_id INT DEFAULT NULL,
    description TEXT NOT NULL,
    complaint_date DATE NOT NULL,
    complaint_time TIME NOT NULL,
    status ENUM('pending', 'resolved', 'in_progress') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_student (student_id),
    INDEX idx_faculty (faculty_id),
    INDEX idx_status (status),
    INDEX idx_complaint_date (complaint_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERT SAMPLE DATA FOR TESTING
-- =====================================================

-- Insert HOD account (password: hod123)
INSERT INTO users (name, email, password, role, department) 
VALUES ('Dr. Rajesh Kumar', 'hod@trinity.edu', '$2y$10$YWDEjqRZqGxC1EKvx0LmSOqz4C5qA2bGJ3GJG1WvZQBHJhJwKQGEe', 'hod', 'Computer Engineering');

-- Insert Faculty members (password: faculty123)
INSERT INTO users (name, email, password, role, subject, department) VALUES
('Prof. Amit Sharma', 'amit.sharma@trinity.edu', '$2y$10$YWDEjqRZqGxC1EKvx0LmSOqz4C5qA2bGJ3GJG1WvZQBHJhJwKQGEe', 'faculty', 'Data Structures', 'Computer Engineering'),
('Prof. Priya Mehta', 'priya.mehta@trinity.edu', '$2y$10$YWDEjqRZqGxC1EKvx0LmSOqz4C5qA2bGJ3GJG1WvZQBHJhJwKQGEe', 'faculty', 'Database Management', 'Computer Engineering'),
('Prof. Rahul Verma', 'rahul.verma@trinity.edu', '$2y$10$YWDEjqRZqGxC1EKvx0LmSOqz4C5qA2bGJ3GJG1WvZQBHJhJwKQGEe', 'faculty', 'Web Development', 'Computer Engineering'),
('Prof. Sneha Patel', 'sneha.patel@trinity.edu', '$2y$10$YWDEjqRZqGxC1EKvx0LmSOqz4C5qA2bGJ3GJG1WvZQBHJhJwKQGEe', 'faculty', 'Machine Learning', 'Computer Engineering'),
('Prof. Vikram Singh', 'vikram.singh@trinity.edu', '$2y$10$YWDEjqRZqGxC1EKvx0LmSOqz4C5qA2bGJ3GJG1WvZQBHJhJwKQGEe', 'faculty', 'Operating Systems', 'Computer Engineering');

-- Insert Sample Students (password: student123)
INSERT INTO users (name, email, password, role, enrollment_no, class) VALUES
('Rahul Deshmukh', 'rahul.d@student.trinity.edu', '$2y$10$YWDEjqRZqGxC1EKvx0LmSOqz4C5qA2bGJ3GJG1WvZQBHJhJwKQGEe', 'student', 'BE001', 'BE Computer Engineering'),
('Ananya Kulkarni', 'ananya.k@student.trinity.edu', '$2y$10$YWDEjqRZqGxC1EKvx0LmSOqz4C5qA2bGJ3GJG1WvZQBHJhJwKQGEe', 'student', 'BE002', 'BE Computer Engineering'),
('Arjun Malhotra', 'arjun.m@student.trinity.edu', '$2y$10$YWDEjqRZqGxC1EKvx0LmSOqz4C5qA2bGJ3GJG1WvZQBHJhJwKQGEe', 'student', 'BE003', 'BE Computer Engineering');

-- Insert Sample Feedbacks
INSERT INTO feedbacks (student_id, faculty_id, subject, rating, comments, feedback_date, feedback_time) VALUES
(2, 3, 'Data Structures', 5.0, 'Date: 2025-10-15, Time: 10:30\nQ1: Concepts are very clear\nQ2: Excellent understanding\nQ3: Great improvement in logical thinking\nQ4: Programming skills enhanced significantly\nQ5: Very engaging teaching style\nQ6: Homework is relevant and helpful', '2025-10-15', '10:30:00'),
(3, 3, 'Data Structures', 4.0, 'Date: 2025-10-16, Time: 11:00\nQ1: Good basic concepts\nQ2: Good understanding\nQ3: Logical thinking improved\nQ4: Programming skills better\nQ5: Engaging class\nQ6: Homework is good', '2025-10-16', '11:00:00'),
(4, 4, 'Database Management', 5.0, 'Date: 2025-10-17, Time: 14:00\nQ1: Crystal clear concepts\nQ2: Perfect understanding\nQ3: Excellent logical improvement\nQ4: Great programming enhancement\nQ5: Very interactive classes\nQ6: Homework very relevant', '2025-10-17', '14:00:00'),
(2, 5, 'Web Development', 4.0, 'Date: 2025-10-18, Time: 15:30\nQ1: Good concepts\nQ2: Good grasp\nQ3: Thinking improved\nQ4: Skills enhanced\nQ5: Good engagement\nQ6: Assignments are helpful', '2025-10-18', '15:30:00'),
(3, 6, 'Machine Learning', 3.0, 'Date: 2025-10-19, Time: 09:00\nQ1: Concepts are okay\nQ2: Average understanding\nQ3: Some improvement in thinking\nQ4: Skills development is moderate\nQ5: Class could be more engaging\nQ6: Homework is okay', '2025-10-19', '09:00:00');

-- Insert Sample Complaints
INSERT INTO complaints (student_id, faculty_id, description, complaint_date, complaint_time, status) VALUES
(2, 7, 'Date: 2025-10-20, Time: 10:00\nType: Faculty Issues\nPriority: Medium\nTitle: Teaching pace too fast\n\nThe lectures are moving too quickly and students are having difficulty following along. More time needed for practice.', '2025-10-20', '10:00:00', 'pending'),
(3, NULL, 'Date: 2025-10-21, Time: 11:30\nType: Infrastructure\nPriority: High\nTitle: Lab equipment not working\n\nComputers in Lab 3 are not functioning properly. This is affecting practical sessions significantly.', '2025-10-21', '11:30:00', 'pending'),
(4, 4, 'Date: 2025-10-22, Time: 13:00\nType: Faculty Issues\nPriority: Low\nTitle: Assignment deadlines too short\n\nThe assignment deadlines are very tight. Students need more time to complete quality work.', '2025-10-22', '13:00:00', 'in_progress');

-- =====================================================
-- VIEWS FOR EASY DATA RETRIEVAL
-- =====================================================

-- View: Complete feedback information with user details
CREATE OR REPLACE VIEW v_feedback_details AS
SELECT 
    f.feedback_id,
    f.student_id,
    s.name AS student_name,
    s.email AS student_email,
    f.faculty_id,
    fac.name AS faculty_name,
    fac.email AS faculty_email,
    fac.subject AS faculty_subject,
    f.subject,
    f.rating,
    f.comments,
    f.feedback_date,
    f.feedback_time,
    f.created_at AS timestamp
FROM feedbacks f
JOIN users s ON f.student_id = s.user_id
JOIN users fac ON f.faculty_id = fac.user_id
ORDER BY f.created_at DESC;

-- View: Complete complaint information with user details
CREATE OR REPLACE VIEW v_complaint_details AS
SELECT 
    c.complaint_id,
    c.student_id,
    s.name AS student_name,
    s.email AS student_email,
    c.faculty_id,
    fac.name AS faculty_name,
    fac.email AS faculty_email,
    c.description,
    c.complaint_date,
    c.complaint_time,
    c.status,
    c.created_at AS date,
    c.updated_at
FROM complaints c
JOIN users s ON c.student_id = s.user_id
LEFT JOIN users fac ON c.faculty_id = fac.user_id
ORDER BY c.created_at DESC;

-- View: Faculty performance summary
CREATE OR REPLACE VIEW v_faculty_performance AS
SELECT 
    u.user_id AS faculty_id,
    u.name AS faculty_name,
    u.subject,
    u.department,
    COUNT(DISTINCT f.feedback_id) AS total_feedbacks,
    ROUND(AVG(f.rating), 2) AS average_rating,
    COUNT(DISTINCT c.complaint_id) AS total_complaints
FROM users u
LEFT JOIN feedbacks f ON u.user_id = f.faculty_id
LEFT JOIN complaints c ON u.user_id = c.faculty_id
WHERE u.role = 'faculty'
GROUP BY u.user_id, u.name, u.subject, u.department;

-- =====================================================
-- STORED PROCEDURES (Optional but helpful)
-- =====================================================

DELIMITER //

-- Procedure: Get faculty performance statistics
CREATE PROCEDURE sp_get_faculty_stats(IN faculty_user_id INT)
BEGIN
    SELECT 
        COUNT(f.feedback_id) AS total_feedbacks,
        ROUND(AVG(f.rating), 2) AS avg_rating,
        MAX(f.rating) AS max_rating,
        MIN(f.rating) AS min_rating,
        COUNT(DISTINCT f.student_id) AS unique_students,
        (SELECT COUNT(*) FROM complaints WHERE faculty_id = faculty_user_id) AS total_complaints
    FROM feedbacks f
    WHERE f.faculty_id = faculty_user_id;
END//

-- Procedure: Get recent feedbacks for a faculty
CREATE PROCEDURE sp_get_recent_feedbacks(IN faculty_user_id INT, IN limit_count INT)
BEGIN
    SELECT * FROM v_feedback_details
    WHERE faculty_id = faculty_user_id
    ORDER BY timestamp DESC
    LIMIT limit_count;
END//

DELIMITER ;

-- =====================================================
-- INDEXES FOR PERFORMANCE OPTIMIZATION
-- =====================================================

-- Additional composite indexes for common queries
CREATE INDEX idx_feedback_faculty_date ON feedbacks(faculty_id, feedback_date);
CREATE INDEX idx_complaint_faculty_status ON complaints(faculty_id, status);
CREATE INDEX idx_user_role_email ON users(role, email);

-- =====================================================
-- SECURITY: Create application user (recommended)
-- =====================================================

-- Create a dedicated MySQL user for the application (optional but recommended for security)
-- Uncomment and modify the following lines:

-- CREATE USER IF NOT EXISTS 'faculty_app'@'localhost' IDENTIFIED BY 'SecurePassword123!';
-- GRANT SELECT, INSERT, UPDATE ON faculty_tracker.* TO 'faculty_app'@'localhost';
-- FLUSH PRIVILEGES;

-- =====================================================
-- DATABASE INFORMATION
-- =====================================================

-- Show all tables
SHOW TABLES;

-- Show database size
SELECT 
    table_schema AS 'Database',
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = 'faculty_tracker'
GROUP BY table_schema;
