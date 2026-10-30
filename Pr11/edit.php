<?php
require_once "db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT * FROM pr11_students WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    header("Location: index.php?message=" . urlencode("Student not found."));
    exit;
}

$name = $student["name"];
$email = $student["email"];
$course = $student["course"];
$semester = $student["semester"];
$phone = $student["phone"];
$error = "";

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
            UPDATE pr11_students
            SET name = ?, email = ?, course = ?,
                semester = ?, phone = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "sssisi",
            $name,
            $email,
            $course,
            $semester,
            $phone,
            $id
        );

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: index.php?message=" . urlencode("Student updated successfully."));
            exit;
        }

        $error = $conn->errno === 1062
            ? "That email address is already used by another student."
            : "Unable to update the student. Please try again.";

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
    <title>Edit Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<main class="form-card">
    <h1>Edit Student</h1>

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

        <button type="submit">Update Student</button>
    </form>

    <p><a href="index.php">Back to Student List</a></p>
</main>

</body>
</html>