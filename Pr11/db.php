<?php
mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli("localhost", "root", "");

if ($conn->connect_error) {
    die("Database connection failed. Start MySQL in XAMPP.");
}

$conn->set_charset("utf8mb4");

$result = $conn->query("SHOW DATABASES");
$dbName = null;

while ($row = $result->fetch_row()) {
    $candidate = $row[0];

    if (in_array($candidate, [
        "information_schema",
        "mysql",
        "performance_schema",
        "sys"
    ])) {
        continue;
    }

    $safe = str_replace("`", "``", $candidate);

    $s = $conn->query(
        "SHOW TABLES FROM `$safe` LIKE 'students'"
    );

    $c = $conn->query(
        "SHOW TABLES FROM `$safe` LIKE 'courses'"
    );

    if ($s && $c && $s->num_rows > 0 && $c->num_rows > 0) {
        $dbName = $candidate;
        break;
    }
}

if (!$dbName) {
    die("StudentHub database with students and courses was not found.");
}

$conn->select_db($dbName);

$conn->query("
    CREATE TABLE IF NOT EXISTS pr11_students (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        course VARCHAR(100) NOT NULL,
        semester INT NOT NULL,
        phone VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB
");

$countResult = $conn->query(
    "SELECT COUNT(*) AS total FROM pr11_students"
);
$count = (int)$countResult->fetch_assoc()["total"];

if ($count === 0) {
    $names = [
        "Aarav Shah", "Diya Patel", "Rohan Mehta",
        "Ananya Desai", "Vivaan Joshi", "Isha Trivedi",
        "Arjun Shah", "Kavya Patel", "Reyansh Mehta",
        "Saanvi Desai", "Aditya Joshi", "Myra Shah",
        "Krish Patel", "Pari Mehta", "Dhruv Desai",
        "Aadhya Joshi", "Kabir Shah", "Kiara Patel",
        "Dev Mehta", "Sara Desai"
    ];

    $courses = ["AIML", "Computer Engineering", "IT", "Data Science"];

    $stmt = $conn->prepare("
        INSERT INTO pr11_students
        (name, email, course, semester, phone)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($names as $i => $name) {
        $email = "student" . ($i + 1) . "@example.com";
        $course = $courses[$i % count($courses)];
        $semester = ($i % 6) + 1;
        $phone = "98765" . str_pad((string)($i + 1), 5, "0", STR_PAD_LEFT);

        $stmt->bind_param(
            "sssis",
            $name,
            $email,
            $course,
            $semester,
            $phone
        );

        $stmt->execute();
    }

    $stmt->close();
}
?>