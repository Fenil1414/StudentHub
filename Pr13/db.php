
<?php
$host = "localhost";
$user = "root";
$password = "";

$conn = new mysqli($host, $user, $password);

if ($conn->connect_error) {
    die("Database connection failed");
}

$result = $conn->query("SHOW DATABASES");
$dbName = "";

while ($row = $result->fetch_assoc()) {
    $name = $row["Database"];

    if (in_array($name, ["information_schema", "mysql", "performance_schema", "phpmyadmin"])) {
        continue;
    }

    $check = $conn->query("SHOW TABLES FROM `$name`");

    $tables = [];

    while ($table = $check->fetch_array()) {
        $tables[] = $table[0];
    }

    if (in_array("students", $tables) && in_array("courses", $tables)) {
        $dbName = $name;
        break;
    }
}

if ($dbName == "") {
    die("Existing Practical 8 database not found.");
}

$conn->select_db($dbName);

$conn->query("
    CREATE TABLE IF NOT EXISTS pr13_students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        course VARCHAR(100) NOT NULL
    )
");
?>
