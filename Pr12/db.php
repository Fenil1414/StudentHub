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
        "information_schema", "mysql",
        "performance_schema", "sys"
    ])) {
        continue;
    }

    $safe = str_replace("`", "``", $candidate);
    $s = $conn->query("SHOW TABLES FROM `$safe` LIKE 'students'");
    $c = $conn->query("SHOW TABLES FROM `$safe` LIKE 'courses'");

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
    CREATE TABLE IF NOT EXISTS pr12_events (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        description TEXT NOT NULL,
        event_date DATE NOT NULL,
        venue VARCHAR(150) NOT NULL,
        poster VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB
");

if (!is_dir(__DIR__ . "/uploads")) {
    mkdir(__DIR__ . "/uploads", 0755, true);
}

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>