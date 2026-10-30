<?php
require_once "db.php";

$fullName = "";
$email = "";
$enrollment = "";
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $enrollment = trim($_POST["enrollment_number"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($fullName === "") {
        $errors[] = "Full name is required.";
    } elseif (mb_strlen($fullName) > 100) {
        $errors[] = "Full name must be 100 characters or fewer.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email address.";
    }

    if ($enrollment === "") {
        $errors[] = "Enrollment number is required.";
    } elseif (mb_strlen($enrollment) > 30) {
        $errors[] = "Enrollment number must be 30 characters or fewer.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    if (!$errors) {
        $check = $pdo->prepare(
            "SELECT id FROM users WHERE email = :email OR enrollment_number = :enrollment LIMIT 1"
        );
        $check->execute(["email" => $email, "enrollment" => $enrollment]);

        if ($check->fetch()) {
            $errors[] = "That email or enrollment number is already registered.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            try {
                $insert = $pdo->prepare(
                    "INSERT INTO users (full_name, email, enrollment_number, password_hash)
                     VALUES (:full_name, :email, :enrollment, :password_hash)"
                );
                $insert->execute([
                    "full_name" => $fullName,
                    "email" => $email,
                    "enrollment" => $enrollment,
                    "password_hash" => $passwordHash
                ]);
                $success = "Registration successful. Your account has been created.";
                $fullName = $email = $enrollment = "";
            } catch (PDOException $e) {
                if ($e->getCode() === "23000") {
                    $errors[] = "That email or enrollment number is already registered.";
                } else {
                    $errors[] = "Registration could not be completed. Please try again.";
                }
            }
        }
    }
}

function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Secure Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <h1>StudentHub</h1>
    <p>Secure User Registration</p>
</header>
<main>
    <section class="card">
        <h2>Create an Account</h2>
        <?php if ($success): ?>
            <p class="message success"><?= h($success) ?></p>
        <?php endif; ?>
        <?php if ($errors): ?>
            <div class="message error" role="alert">
                <p>Please correct the following:</p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= h($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="post" action="register.php">
            <label for="full_name">Full Name</label>
            <input id="full_name" name="full_name" type="text" maxlength="100"
                   value="<?= h($fullName) ?>" autocomplete="name" required>
            <label for="email">Email Address</label>
            <input id="email" name="email" type="email" maxlength="150"
                   value="<?= h($email) ?>" autocomplete="email" required>
            <label for="enrollment_number">Enrollment Number</label>
            <input id="enrollment_number" name="enrollment_number" type="text" maxlength="30"
                   value="<?= h($enrollment) ?>" required>
            <label for="password">Password</label>
            <input id="password" name="password" type="password" minlength="8"
                   autocomplete="new-password" required>
            <label for="confirm_password">Confirm Password</label>
            <input id="confirm_password" name="confirm_password" type="password" minlength="8"
                   autocomplete="new-password" required>
            <button type="submit">Register</button>
        </form>
    </section>
</main>
<footer><p>AIUE203: Web Technology | Practical 9</p></footer>
</body>
</html>
