<?php
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php?message=" . urlencode("Invalid student ID."));
    exit;
}

$stmt = $conn->prepare(
    "DELETE FROM pr11_students WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$deleted = $stmt->affected_rows;
$stmt->close();

$message = $deleted > 0
    ? "Student deleted successfully."
    : "Student not found.";

header("Location: index.php?message=" . urlencode($message));
exit;
?>