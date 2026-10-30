<?php
require_once __DIR__ . "/db.php";

$demoUsers = [
    [
        "Student Demo",
        "pr10.student@studenthub.local",
        "STU-PR10-DEMO",
        "Student@123",
        "student"
    ],
    [
        "Admin Demo",
        "pr10.admin@studenthub.local",
        "ADM-PR10-DEMO",
        "Admin@123",
        "admin"
    ]
];

foreach ($demoUsers as $user) {
    [
        $name,
        $email,
        $enrollment,
        $password,
        $role
    ] = $user;

    $check = $conn->prepare(
        "SELECT id FROM users WHERE email = ? LIMIT 1"
    );

    $check->bind_param("s", $email);
    $check->execute();

    $exists = $check->get_result()->fetch_assoc();
    $check->close();

    if (!$exists) {
        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $insert = $conn->prepare("
            INSERT INTO users
            (full_name, email, enrollment_number, password_hash, role)
            VALUES (?, ?, ?, ?, ?)
        ");

        $insert->bind_param(
            "sssss",
            $name,
            $email,
            $enrollment,
            $passwordHash,
            $role
        );

        if ($insert->execute()) {
            echo "Created demo account: " .
                htmlspecialchars($email) . "<br>";
        } else {
            echo "Could not create account: " .
                htmlspecialchars($email) . "<br>";
        }

        $insert->close();
    } else {
        echo "Demo account already exists: " .
            htmlspecialchars($email) . "<br>";
    }
}

echo "<p>Setup complete. Delete seed_demo_users.php now.</p>";
echo "<p><a href='login.php'>Open Login Page</a></p>";
?>