<?php
// Practical 7: server-side form validation and CSV/JSON storage.
function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
$errors = [];
$name = trim((string)($_POST['name'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$studentId = trim((string)($_POST['student_id'] ?? ''));
$course = trim((string)($_POST['course'] ?? ''));
$allowedCourses = ['AIML', 'CSE', 'CE', 'IT'];
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); $errors[] = 'Please submit the registration form first.'; }
if (mb_strlen($name) < 2 || mb_strlen($name) > 80) $errors[] = 'Name must be between 2 and 80 characters.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 120) $errors[] = 'Enter a valid email address.';
if (strlen($studentId) < 3 || strlen($studentId) > 30) $errors[] = 'Student ID must be between 3 and 30 characters.';
if (!in_array($course, $allowedCourses, true)) $errors[] = 'Select a valid course.';
$record = ['name' => $name, 'email' => $email, 'student_id' => $studentId, 'course' => $course, 'submitted_at' => date('c')];
if (!$errors) {
  $dataDir = __DIR__ . '/data';
  if (!is_dir($dataDir) && !mkdir($dataDir, 0750, true)) $errors[] = 'Unable to prepare data storage.';
  if (!$errors) {
    $jsonPath = $dataDir . '/registrations.json';
    $csvPath = $dataDir . '/registrations.csv';
    $records = [];
    if (is_file($jsonPath)) {
      $decoded = json_decode((string)file_get_contents($jsonPath), true);
      if (is_array($decoded)) $records = $decoded;
    }
    $records[] = $record;
    $jsonWritten = file_put_contents($jsonPath, json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
    $newCsv = !is_file($csvPath) || filesize($csvPath) === 0;
    $fp = fopen($csvPath, 'ab');
    if ($fp) {
      if (flock($fp, LOCK_EX)) {
        if ($newCsv) fputcsv($fp, ['name', 'email', 'student_id', 'course', 'submitted_at']);
        fputcsv($fp, array_values($record));
        flock($fp, LOCK_UN);
      }
      fclose($fp);
    } else { $errors[] = 'Unable to write CSV storage.'; }
    if ($jsonWritten === false) $errors[] = 'Unable to write JSON storage.';
  }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Registration Result - StudentHub</title><link rel="stylesheet" href="style.css"></head><body>
<header><h1>StudentHub</h1><p>Registration Processing</p></header><main id="main-content"><section class="panel">
<?php if ($errors): ?><h2>Registration not completed</h2><p>Please correct the following issue(s):</p><ul class="errors"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul><p><a href="register.html">Return to registration form</a></p>
<?php else: ?><h2>Registration submitted successfully</h2><p>The form passed server-side validation. The registration was saved to the CSV and JSON data files.</p><dl><dt>Name</dt><dd><?= e($name) ?></dd><dt>Email</dt><dd><?= e($email) ?></dd><dt>Student ID</dt><dd><?= e($studentId) ?></dd><dt>Course</dt><dd><?= e($course) ?></dd></dl><p><a href="register.html">Add another registration</a> | <a href="index.html">Home</a></p><?php endif; ?>
</section></main><footer><p>&copy; 2026 StudentHub</p></footer></body></html>
