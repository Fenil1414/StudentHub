<?php
require_once "db.php";

$search = trim($_GET["search"] ?? "");
$courseFilter = trim($_GET["course"] ?? "");

$sql = "
    SELECT id, name, email, course, semester, phone
    FROM pr11_students
    WHERE (name LIKE ? OR email LIKE ?)
";

$searchTerm = "%" . $search . "%";

if ($courseFilter !== "") {
    $sql .= " AND course = ?";
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);

if ($courseFilter !== "") {
    $stmt->bind_param(
        "sss",
        $searchTerm,
        $searchTerm,
        $courseFilter
    );
} else {
    $stmt->bind_param(
        "ss",
        $searchTerm,
        $searchTerm
    );
}

$stmt->execute();
$students = $stmt->get_result();

$coursesResult = $conn->query(
    "SELECT DISTINCT course FROM pr11_students ORDER BY course"
);

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <header>
        <h1>Student Management System</h1>
        <p>Manage student records using PHP and MySQL.</p>
    </header>

    <a class="button" href="add.php">+ Add Student</a>

    <?php if (isset($_GET["message"])): ?>
        <p class="success"><?= h($_GET["message"]) ?></p>
    <?php endif; ?>

    <form method="get" class="filters">
        <input
            type="text"
            name="search"
            placeholder="Search by name or email"
            value="<?= h($search) ?>"
        >

        <select name="course">
            <option value="">All Courses</option>

            <?php while ($row = $coursesResult->fetch_assoc()): ?>
                <option
                    value="<?= h($row["course"]) ?>"
                    <?= $courseFilter === $row["course"] ? "selected" : "" ?>
                >
                    <?= h($row["course"]) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Search / Filter</button>
        <a class="button secondary" href="index.php">Reset</a>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Semester</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($students->num_rows > 0): ?>
                    <?php while ($student = $students->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int)$student["id"] ?></td>
                            <td><?= h($student["name"]) ?></td>
                            <td><?= h($student["email"]) ?></td>
                            <td><?= h($student["course"]) ?></td>
                            <td><?= (int)$student["semester"] ?></td>
                            <td><?= h($student["phone"]) ?></td>
                            <td class="actions">
                                <a href="edit.php?id=<?= (int)$student["id"] ?>">
                                    Edit
                                </a>

                                <form
                                    action="delete.php"
                                    method="post"
                                    onsubmit="return confirm('Delete this student record?');"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$student["id"] ?>"
                                    >
                                    <button class="danger" type="submit">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No students found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>