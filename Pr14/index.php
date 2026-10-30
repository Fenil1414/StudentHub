
<?php
require "auth_check.php";

pr14_require_role();

$role = strtolower($_SESSION["role"]);

if ($role === "admin") {
    header("Location: admin.php");
    exit;
}

if ($role === "student") {
    header("Location: student.php");
    exit;
}

http_response_code(403);
echo "Invalid user role.";
?>
