<?php
require_once __DIR__ . "/auth.php";

if (!empty($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
$message = "";

if (!empty($_SESSION["timed_out"])) {
    $message = "Your session expired after 15 minutes of inactivity. Please sign in again.";
    unset($_SESSION["timed_out"]);
}

if (isset($_GET["loggedout"])) {
    $message = "You have been logged out successfully.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        $password === ""
    ) {
        $error = "Enter a valid email address and password.";
    } else {
        $stmt = $conn->prepare("
            SELECT id, full_name, email, password_hash, role
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (
            $user &&
            password_verify($password, $user["password_hash"])
        ) {
            setAuthenticatedUser($user);

            $userId = (int)$user["id"];

            $update = $conn->prepare("
                UPDATE users
                SET last_login = NOW()
                WHERE id = ?
            ");

            $update->bind_param("i", $userId);
            $update->execute();
            $update->close();

            if (!empty($_POST["remember"])) {
                issueRememberToken($userId);
            } else {
                clearRememberCookie();
            }

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<main class="card">
    <h1>StudentHub Login</h1>
    <p>Sign in to continue to your dashboard.</p>

    <?php if ($message): ?>
        <div class="notice">
            <?= h($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error">
            <?= h($error) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="login.php">
        <label for="email">Email Address</label>
        <input
            id="email"
            name="email"
            type="email"
            required
            autocomplete="username"
        >

        <label for="password">Password</label>
        <input
            id="password"
            name="password"
            type="password"
            required
            autocomplete="current-password"
        >

        <label class="check">
            <input type="checkbox" name="remember" value="1">
            Remember me for 30 days
        </label>

        <button type="submit">Log In</button>
    </form>

    <p class="small">
        Use the demo account credentials provided in README/setup instructions.
    </p>
</main>

</body>
</html>