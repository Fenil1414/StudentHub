<?php
require __DIR__ . '/database.php';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $enrollment = trim($_POST['enrollment_no'] ?? '');
    $courseId = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $enrollment === '' || !$courseId) {
        $error = 'Please enter a name, valid email, enrollment number, and select a course.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO students (full_name, email, enrollment_no, course_id) VALUES (:name, :email, :enrollment, :course_id)');
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':enrollment' => $enrollment,
                ':course_id' => $courseId
            ]);
            $message = 'Student added successfully.';
        } catch (PDOException $e) {
            $error = 'Could not add student. The email or enrollment number may already exist.';
        }
    }
}

$courses = $pdo->query('SELECT course_id, course_name FROM courses ORDER BY course_name')->fetchAll();
$stmt = $pdo->prepare('SELECT s.student_id, s.full_name, s.email, s.enrollment_no, c.course_name FROM students s INNER JOIN courses c ON s.course_id = c.course_id ORDER BY s.student_id DESC');
$stmt->execute();
$students = $stmt->fetchAll();
function h($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>StudentHub | Database Demo</title><link rel="stylesheet" href="style.css">
<style>
.db-wrap{max-width:1050px;margin:40px auto;padding:24px}.db-card{padding:22px;border:1px solid #d8dee9;border-radius:14px;margin:18px 0;background:var(--card-bg,#fff)}.db-form{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:14px}.db-form label{display:grid;gap:6px}.db-form input,.db-form select{padding:11px;border:1px solid #9aa4b2;border-radius:8px}.db-form button{padding:12px 18px;border:0;border-radius:8px;cursor:pointer}.db-table{width:100%;border-collapse:collapse;overflow:auto}.db-table th,.db-table td{text-align:left;padding:10px;border-bottom:1px solid #d8dee9}.db-msg{padding:12px;border-radius:8px;background:#e4f7e9}.db-err{padding:12px;border-radius:8px;background:#ffe8e8}.db-scroll{overflow-x:auto}
</style></head><body>
<header class="site-header"><nav class="navbar"><a class="logo" href="index.html">StudentHub</a><div class="nav-links"><a href="index.html">Home</a><a href="students_db.php" aria-current="page">Database Demo</a><a href="students.html">Student Directory</a></div></nav></header>
<main class="db-wrap"><h1>Student Database</h1><p>Practical 8: MySQL schema, PDO connection, relational data, and prepared statements.</p>
<?php if ($message): ?><p class="db-msg" role="status"><?= h($message) ?></p><?php endif; ?>
<?php if ($error): ?><p class="db-err" role="alert"><?= h($error) ?></p><?php endif; ?>
<section class="db-card"><h2>Add Student</h2><form method="post" action="students_db.php" class="db-form">
<label>Full name<input name="full_name" required maxlength="100" value="<?= h($_POST['full_name'] ?? '') ?>"></label>
<label>Email<input type="email" name="email" required maxlength="150" value="<?= h($_POST['email'] ?? '') ?>"></label>
<label>Enrollment number<input name="enrollment_no" required maxlength="30" value="<?= h($_POST['enrollment_no'] ?? '') ?>"></label>
<label>Course<select name="course_id" required><option value="">Select course</option><?php foreach ($courses as $course): ?><option value="<?= h($course['course_id']) ?>" <?= (string)($_POST['course_id'] ?? '') === (string)$course['course_id'] ? 'selected' : '' ?>><?= h($course['course_name']) ?></option><?php endforeach; ?></select></label>
<div><button type="submit">Save Student</button></div></form></section>
<section class="db-card"><h2>Saved Students</h2><div class="db-scroll"><table class="db-table"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Enrollment</th><th>Course</th></tr></thead><tbody>
<?php foreach ($students as $student): ?><tr><td><?= h($student['student_id']) ?></td><td><?= h($student['full_name']) ?></td><td><?= h($student['email']) ?></td><td><?= h($student['enrollment_no']) ?></td><td><?= h($student['course_name']) ?></td></tr><?php endforeach; ?>
<?php if (!$students): ?><tr><td colspan="5">No student records found.</td></tr><?php endif; ?></tbody></table></div></section>
</main></body></html>
