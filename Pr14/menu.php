
<?php
$role = strtolower($_SESSION["role"] ?? "");

$menus = [
    "admin" => [
        ["Dashboard", "admin.php"],
        ["Manage Students", "../PR11/index.php"],
        ["Manage Events", "../PR12/index.php"],
        ["Logout", "../PR10/logout.php"]
    ],

    "student" => [
        ["Dashboard", "student.php"],
        ["View Events", "../PR12/index.php"],
        ["Logout", "../PR10/logout.php"]
    ]
];

$menuItems = $menus[$role] ?? [];
?>

<nav class="sidebar">
    <h2>StudentHub</h2>

    <?php foreach ($menuItems as $item): ?>
        <a href="<?php echo htmlspecialchars($item[1]); ?>">
            <?php echo htmlspecialchars($item[0]); ?>
        </a>
    <?php endforeach; ?>
</nav>
