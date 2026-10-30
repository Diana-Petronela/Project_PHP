<?php
// shared top bar, included on every logged-in page

// first letter of the name for the avatar circle, e.g. "Demo User" -> "D"
$initial = strtoupper(substr($_SESSION['user_name'] ?? '?', 0, 1));
?>
<header class="topbar">
    <a class="brand" href="index.php">HelpDesk Mini</a>
    <nav>
        <span class="user">
            <span class="avatar"><?= h($initial) ?></span>
            <?= h($_SESSION['user_name'] ?? '') ?>
        </span>
        <?php require __DIR__ . '/theme-toggle-button.php'; ?>
        <a class="btn btn-ghost btn-sm" href="logout.php">Log out</a>
    </nav>
</header>
