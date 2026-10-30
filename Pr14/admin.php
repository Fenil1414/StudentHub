
<?php
require "auth_check.php";
pr14_require_role("admin");

$name = $_SESSION["name"] ?? "Administrator";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - StudentHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "menu.php"; ?>

<main class="content">
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($name); ?>!</p>

    <div class="cards">
        <div class="card">
            <h2>Student Management</h2>
            <p>Add, edit, delete and view student records.</p>
            <a href="../PR11/index.php">Manage Students</a>
        </div>

        <div class="card">
            <h2>Event Management</h2>
            <p>Create and manage college events.</p>
            <a href="../PR12/index.php">Manage Events</a>
        </div>

        <div class="card">
            <h2>User Management</h2>
            <p>Manage access and user roles through the login system.</p>
        </div>

        <div class="card">
            <h2>Administration</h2>
            <p>Access administrator-only dashboard features.</p>
        </div>
    </div>
</main>

</body>
</html>
