<?php
require_once __DIR__ . "/auth.php";

requireRole("student");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<main class="card">
    <h1>Student Dashboard</h1>

    <p>
        Welcome,
        <strong><?= h($_SESSION["full_name"]) ?></strong>!
    </p>

    <p>Email: <?= h($_SESSION["email"]) ?></p>

    <p>
        You are logged in with a student account.
        The admin dashboard is restricted.
    </p>

    <p>
        <a href="admin_dashboard.php">
            Test admin access restriction
        </a>
    </p>

    <form action="logout.php" method="post">
        <button type="submit">Log Out</button>
    </form>
</main>

</body>
</html>