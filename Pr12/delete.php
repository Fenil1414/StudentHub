<?php
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php?message=" . urlencode("Invalid event ID."));
    exit;
}

$stmt = $conn->prepare(
    "SELECT poster FROM pr12_events WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$event) {
    header("Location: index.php?message=" . urlencode("Event not found."));
    exit;
}

$stmt = $conn->prepare("DELETE FROM pr12_events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$deleted = $stmt->affected_rows;
$stmt->close();

if ($deleted > 0 && !empty($event["poster"])) {
    $posterPath = __DIR__ . "/uploads/" . basename($event["poster"]);

    if (is_file($posterPath)) {
        unlink($posterPath);
    }
}

$message = $deleted > 0
    ? "Event deleted successfully."
    : "Unable to delete the event.";

header("Location: index.php?message=" . urlencode($message));
exit;
?>