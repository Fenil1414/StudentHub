CREATE TABLE IF NOT EXISTS pr11_students (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    course VARCHAR(100) NOT NULL,
    semester INT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO pr11_students
(id, name, email, course, semester, phone)
VALUES
(1, 'Aarav Shah', 'student1@example.com', 'AIML', 1, '9876500001'),
(2, 'Diya Patel', 'student2@example.com', 'Computer Engineering', 2, '9876500002'),
(3, 'Rohan Mehta', 'student3@example.com', 'IT', 3, '9876500003'),
(4, 'Ananya Desai', 'student4@example.com', 'Data Science', 4, '9876500004'),
(5, 'Vivaan Joshi', 'student5@example.com', 'AIML', 5, '9876500005'),
(6, 'Isha Trivedi', 'student6@example.com', 'Computer Engineering', 6, '9876500006'),
(7, 'Arjun Shah', 'student7@example.com', 'IT', 1, '9876500007'),
(8, 'Kavya Patel', 'student8@example.com', 'Data Science', 2, '9876500008'),
(9, 'Reyansh Mehta', 'student9@example.com', 'AIML', 3, '9876500009'),
(10, 'Saanvi Desai', 'student10@example.com', 'Computer Engineering', 4, '9876500010'),
(11, 'Aditya Joshi', 'student11@example.com', 'IT', 5, '9876500011'),
(12, 'Myra Shah', 'student12@example.com', 'Data Science', 6, '9876500012'),
(13, 'Krish Patel', 'student13@example.com', 'AIML', 1, '9876500013'),
(14, 'Pari Mehta', 'student14@example.com', 'Computer Engineering', 2, '9876500014'),
(15, 'Dhruv Desai', 'student15@example.com', 'IT', 3, '9876500015'),
(16, 'Aadhya Joshi', 'student16@example.com', 'Data Science', 4, '9876500016'),
(17, 'Kabir Shah', 'student17@example.com', 'AIML', 5, '9876500017'),
(18, 'Kiara Patel', 'student18@example.com', 'Computer Engineering', 6, '9876500018'),
(19, 'Dev Mehta', 'student19@example.com', 'IT', 1, '9876500019'),
(20, 'Sara Desai', 'student20@example.com', 'Data Science', 2, '9876500020');