<?php
require_once "db.php";

$result = $conn->query(
    "SELECT * FROM pr12_events ORDER BY event_date ASC, id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub Event Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container">
    <h1>StudentHub Event Management</h1>
    <p>Manage college events and their posters.</p>

    <a class="button" href="add.php">+ Add Event</a>

    <?php if (isset($_GET["message"])): ?>
        <p class="success"><?= h($_GET["message"]) ?></p>
    <?php endif; ?>

    <div class="events">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($event = $result->fetch_assoc()): ?>
                <article class="event-card">
                    <?php if ($event["poster"]): ?>
                        <img
                            class="poster"
                            src="uploads/<?= h($event["poster"]) ?>"
                            alt="Poster for <?= h($event["title"]) ?>"
                        >
                    <?php else: ?>
                        <div class="no-poster">No poster uploaded</div>
                    <?php endif; ?>

                    <div class="event-content">
                        <h2><?= h($event["title"]) ?></h2>
                        <p><?= nl2br(h($event["description"])) ?></p>
                        <p><strong>Date:</strong> <?= h($event["event_date"]) ?></p>
                        <p><strong>Venue:</strong> <?= h($event["venue"]) ?></p>

                        <div class="actions">
                            <a class="button" href="edit.php?id=<?= (int)$event["id"] ?>">Edit</a>

                            <form action="delete.php" method="post"
                                  onsubmit="return confirm('Delete this event?');">
                                <input type="hidden" name="id" value="<?= (int)$event["id"] ?>">
                                <button class="danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No events have been added yet.</p>
        <?php endif; ?>
    </div>
</main>
</body>
</html>