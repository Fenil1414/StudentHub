-- StudentHub Practical 8: MySQL database schema
CREATE DATABASE IF NOT EXISTS studenthub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studenthub;

CREATE TABLE IF NOT EXISTS courses (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(100) NOT NULL UNIQUE,
    course_code VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    enrollment_no VARCHAR(30) NOT NULL UNIQUE,
    course_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_students_course FOREIGN KEY (course_id) REFERENCES courses(course_id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

INSERT IGNORE INTO courses (course_name, course_code) VALUES
('Artificial Intelligence and Machine Learning', 'AIML'),
('Computer Engineering', 'CE'),
('Information Technology', 'IT');

INSERT IGNORE INTO students (full_name, email, enrollment_no, course_id) VALUES
('Aarav Patel', 'aarav@example.com', '25AIML001', 1),
('Diya Shah', 'diya@example.com', '25AIML002', 1),
('Rohan Mehta', 'rohan@example.com', '25CE003', 2);
