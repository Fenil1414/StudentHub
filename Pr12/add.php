<?php
require_once "db.php";

$title = "";
$description = "";
$eventDate = "";
$venue = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $eventDate = $_POST["event_date"] ?? "";
    $venue = trim($_POST["venue"] ?? "");

    $validDate = DateTime::createFromFormat("!Y-m-d", $eventDate);

    if (
        $title === "" || $description === "" || $venue === "" ||
        !$validDate || $validDate->format("Y-m-d") !== $eventDate
    ) {
        $error = "Please complete all fields with valid details.";
    } elseif (
        empty($_FILES["poster"]) ||
        $_FILES["poster"]["error"] !== UPLOAD_ERR_OK
    ) {
        $error = "Please select an event poster.";
    } elseif ($_FILES["poster"]["size"] > 2 * 1024 * 1024) {
        $error = "The poster must be 2 MB or smaller.";
    } else {
        $tmp = $_FILES["poster"]["tmp_name"];
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);

        $allowed = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp"
        ];

        if (!isset($allowed[$mime]) || getimagesize($tmp) === false) {
            $error = "Only valid JPG, PNG, or WebP image files are allowed.";
        } else {
            $poster = bin2hex(random_bytes(16)) . "." . $allowed[$mime];
            $destination = __DIR__ . "/uploads/" . $poster;

            if (!move_uploaded_file($tmp, $destination)) {
                $error = "Poster upload failed. Check the uploads folder permissions.";
            } else {
                $stmt = $conn->prepare("
                    INSERT INTO pr12_events
                    (title, description, event_date, venue, poster)
                    VALUES (?, ?, ?, ?, ?)
                ");

                $stmt->bind_param(
                    "sssss",
                    $title,
                    $description,
                    $eventDate,
                    $venue,
                    $poster
                );

                if ($stmt->execute()) {
                    $stmt->close();
                    header("Location: index.php?message=" . urlencode("Event added successfully."));
                    exit;
                }

                $stmt->close();
                if (is_file($destination)) {
                    unlink($destination);
                }
                $error = "Unable to save the event.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="form-card">
    <h1>Add Event</h1>

    <?php if ($error): ?>
        <p class="error"><?= h($error) ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label for="title">Event Title</label>
        <input id="title" name="title" maxlength="150" required value="<?= h($title) ?>">

        <label for="description">Description</label>
        <textarea id="description" name="description" required><?= h($description) ?></textarea>

        <label for="event_date">Event Date</label>
        <input id="event_date" type="date" name="event_date" required value="<?= h($eventDate) ?>">

        <label for="venue">Venue</label>
        <input id="venue" name="venue" maxlength="150" required value="<?= h($venue) ?>">

        <label for="poster">Event Poster (JPG, PNG, WebP; max 2 MB)</label>
        <input id="poster" type="file" name="poster" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required>

        <button type="submit">Save Event</button>
    </form>

    <p><a href="index.php">Back to Events</a></p>
</main>
</body>
</html>