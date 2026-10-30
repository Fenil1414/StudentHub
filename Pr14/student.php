
<?php
require "auth_check.php";
pr14_require_role("student");

$name = $_SESSION["name"] ?? "Student";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard - StudentHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "menu.php"; ?>

<main class="content">
    <h1>Student Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($name); ?>!</p>

    <div class="cards">
        <div class="card">
            <h2>My Profile</h2>
            <p>View your student information.</p>
        </div>

        <div class="card">
            <h2>College Events</h2>
            <p>View upcoming college events.</p>
            <a href="../PR12/index.php">View Events</a>
        </div>

        <div class="card">
            <h2>Student Resources</h2>
            <p>Access student-related information.</p>
        </div>
    </div>
</main>

</body>
</html>
