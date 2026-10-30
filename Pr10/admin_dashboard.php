<?php
require_once __DIR__ . "/auth.php";

requireRole("admin");

$totalRow = $conn->query(
    "SELECT COUNT(*) AS total FROM users"
)->fetch_assoc();

$stmt = $conn->prepare(
    "SELECT last_login FROM users WHERE id = ?"
);

$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();

$lastRow = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<main class="card">
    <h1>Admin Dashboard</h1>

    <p>
        Welcome,
        <strong><?= h($_SESSION["full_name"]) ?></strong>!
    </p>

    <p>
        Total registered users:
        <strong><?= (int)$totalRow["total"] ?></strong>
    </p>

    <p>
        Last login:
        <?= h($lastRow["last_login"] ?? "Not available") ?>
    </p>

    <p>
        This page is accessible only to administrator accounts.
    </p>

    <form action="logout.php" method="post">
        <button type="submit">Log Out</button>
    </form>
</main>

</body>
</html>