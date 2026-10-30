
<?php
require_once __DIR__ . "/../PR10/auth.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function pr14_require_role($requiredRole = null) {
    $role = strtolower($_SESSION["role"] ?? "");

    if (empty($_SESSION["user_id"]) || $role === "") {
        header("Location: ../PR10/login.php");
        exit;
    }

    if ($requiredRole !== null && $role !== strtolower($requiredRole)) {
        http_response_code(403);
        echo "<h1>403 - Access Denied</h1>";
        echo "<p>You do not have permission to access this page.</p>";
        echo '<a href="index.php">Back to Dashboard</a>';
        exit;
    }
}
?>
