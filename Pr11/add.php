<?php
require_once "db.php";

$error = "";

$name = "";
$email = "";
$course = "";
$semester = "";
$phone = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $semester = (int)($_POST["semester"] ?? 0);
    $phone = trim($_POST["phone"] ?? "");

    if (
        $name === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        $course === "" ||
        $semester < 1 ||
        $semester > 8 ||
        $phone === ""
    ) {
        $error = "Please enter valid details in every field.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO pr11_students
            (name, email, course, semester, phone)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssis",
            $name,
            $email,
            $course,
            $semester,
            $phone
        );

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: index.php?message=" . urlencode("Student added successfully."));
            exit;
        }

        $error = $conn->errno === 1062
            ? "This email address is already registered."
            : "Unable to add the student. Please try again.";

        $stmt->close();
    }
}

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<main class="form-card">
    <h1>Add Student</h1>

    <?php if ($error): ?>
        <p class="error"><?= h($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="name">Full Name</label>
        <input id="name" name="name" required value="<?= h($name) ?>">

        <label for="email">Email</label>
        <input id="email" name="email" type="email" required value="<?= h($email) ?>">

        <label for="course">Course</label>
        <select id="course" name="course" required>
            <option value="">Select Course</option>
            <?php foreach (["AIML", "Computer Engineering", "IT", "Data Science"] as $c): ?>
                <option value="<?= h($c) ?>" <?= $course === $c ? "selected" : "" ?>>
                    <?= h($c) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="semester">Semester (1–8)</label>
        <input id="semester" name="semester" type="number" min="1" max="8" required value="<?= h($semester) ?>">

        <label for="phone">Phone</label>
        <input id="phone" name="phone" type="tel" required value="<?= h($phone) ?>">

        <button type="submit">Save Student</button>
    </form>

    <p><a href="index.php">Back to Student List</a></p>
</main>

</body>
</html>