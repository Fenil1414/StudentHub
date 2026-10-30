<?php
mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli("localhost", "root", "");

if ($conn->connect_error) {
    die("Database connection failed. Start MySQL in XAMPP and check db.php.");
}

$conn->set_charset("utf8mb4");

$result = $conn->query("SHOW DATABASES");
$databases = [];

while ($row = $result->fetch_row()) {
    $databases[] = $row[0];
}

$dbName = null;

foreach ($databases as $candidate) {
    if (in_array($candidate, [
        "information_schema",
        "mysql",
        "performance_schema",
        "sys"
    ])) {
        continue;
    }

    $safe = str_replace("`", "``", $candidate);

    $students = $conn->query(
        "SHOW TABLES FROM `$safe` LIKE 'students'"
    );

    $courses = $conn->query(
        "SHOW TABLES FROM `$safe` LIKE 'courses'"
    );

    if (
        $students && $courses &&
        $students->num_rows > 0 &&
        $courses->num_rows > 0
    ) {
        $dbName = $candidate;
        break;
    }
}

if (!$dbName) {
    die(
        "StudentHub database not found. " .
        "Import your Practical 8 database first."
    );
}

$conn->select_db($dbName);

$conn->query("
    CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(190) NOT NULL UNIQUE,
        enrollment_number VARCHAR(40) NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('student','admin') NOT NULL DEFAULT 'student',
        last_login DATETIME NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB
");

$columns = [];
$result = $conn->query("SHOW COLUMNS FROM users");

while ($row = $result->fetch_assoc()) {
    $columns[] = $row["Field"];
}

if (!in_array("role", $columns)) {
    $conn->query("
        ALTER TABLE users
        ADD role ENUM('student','admin')
        NOT NULL DEFAULT 'student'
    ");
}

if (!in_array("last_login", $columns)) {
    $conn->query("
        ALTER TABLE users
        ADD last_login DATETIME NULL
    ");
}

$conn->query("
    CREATE TABLE IF NOT EXISTS remember_tokens (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        token_hash CHAR(64) NOT NULL UNIQUE,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (user_id),
        CONSTRAINT fk_remember_user
            FOREIGN KEY (user_id)
            REFERENCES users(id)
            ON DELETE CASCADE
    ) ENGINE=InnoDB
");
?>