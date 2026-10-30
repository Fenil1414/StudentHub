<?php
require_once "db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM pr12_events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$event) {
    header("Location: index.php?message=" . urlencode("Event not found."));
    exit;
}

$title = $event["title"];
$description = $event["description"];
$eventDate = $event["event_date"];
$venue = $event["venue"];
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $eventDate = $_POST["event_date"] ?? "";
    $venue = trim($_POST["venue"] ?? "");
    $poster = $event["poster"];
    $newPosterPath = null;

    $validDate = DateTime::createFromFormat("!Y-m-d", $eventDate);

    if (
        $title === "" || $description === "" || $venue === "" ||
        !$validDate || $validDate->format("Y-m-d") !== $eventDate
    ) {
        $error = "Please complete all fields with valid details.";
    } else {
        $file = $_FILES["poster"] ?? null;

        if ($file && $file["error"] !== UPLOAD_ERR_NO_FILE) {
            if ($file["error"] !== UPLOAD_ERR_OK) {
                $error = "The poster upload failed.";
            } elseif ($file["size"] > 2 * 1024 * 1024) {
                $error = "The poster must be 2 MB or smaller.";
            } else {
                $tmp = $file["tmp_name"];
                $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);

                $allowed = [
                    "image/jpeg" => "jpg",
                    "image/png" => "png",
                    "image/webp" => "webp"
                ];

                if (!isset($allowed[$mime]) || getimagesize($tmp) === false) {
                    $error = "Only valid JPG, PNG, or WebP images are allowed.";
                } else {
                    $poster = bin2hex(random_bytes(16)) . "." . $allowed[$mime];
                    $newPosterPath = __DIR__ . "/uploads/" . $poster;

                    if (!move_uploaded_file($tmp, $newPosterPath)) {
                        $error = "Unable to upload the new poster.";
                    }
                }
            }
        }

        if ($error === "") {
            $stmt = $conn->prepare("
                UPDATE pr12_events
                SET title = ?, description = ?, event_date = ?,
                    venue = ?, poster = ?
                WHERE id = ?
            ");

            $stmt->bind_param(
                "sssssi",
                $title,
                $description,
                $eventDate,
                $venue,
                $poster,
                $id
            );

            if ($stmt->execute()) {
                $stmt->close();

                if (
                    $newPosterPath !== null &&
                    !empty($event["poster"])
                ) {
                    $oldPath = __DIR__ . "/uploads/" . basename($event["poster"]);

                    if (is_file($oldPath)) {
                        unlink($oldPath);
                    }
                }

                header("Location: index.php?message=" . urlencode("Event updated successfully."));
                exit;
            }

            $stmt->close();

            if ($newPosterPath !== null && is_file($newPosterPath)) {
                unlink($newPosterPath);
            }

            $error = "Unable to update the event.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="form-card">
    <h1>Edit Event</h1>

    <?php if ($error): ?>
        <p class="error"><?= h($error) ?></p>
    <?php endif; ?>

    <?php if ($event["poster"]): ?>
        <p>Current poster:</p>
        <img
            class="current-poster"
            src="uploads/<?= h($event["poster"]) ?>"
            alt="Current event poster"
        >
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

        <label for="poster">Replace Poster (optional; max 2 MB)</label>
        <input id="poster" type="file" name="poster" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

        <button type="submit">Update Event</button>
    </form>

    <p><a href="index.php">Back to Events</a></p>
</main>
</body>
</html>