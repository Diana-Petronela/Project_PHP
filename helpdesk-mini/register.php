<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

start_session();

// already logged in? nothing to do here
if (current_user_id() !== null) {
    redirect('index.php');
}

$errors = [];
$name = '';
$email = '';

// GET = just viewing the form, POST = submitting it
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // server-side validation - never trust the client alone
    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if (empty($errors)) {
        $pdo = get_db();

        // is this email already taken?
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);

        if ($stmt->fetch() !== false) {
            $errors[] = 'This email is already registered.';
        } else {
            // never store the raw password - bcrypt hash only
            $hash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare(
                'INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)'
            );
            $stmt->execute([$name, $email, $hash]);

            redirect('login.php?registered=1');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php require __DIR__ . '/partials/theme-init.php'; ?>
    <title>Register - HelpDesk Mini</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-box">
        <div class="top-row">
            <span class="brand">HelpDesk Mini</span>
            <?php require __DIR__ . '/partials/theme-toggle-button.php'; ?>
        </div>
        <h1>Create an account</h1>

        <?php if (!empty($errors)): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li><?= h($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="post" action="register.php" novalidate>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?= h($name) ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= h($email) ?>" required>

            <label for="password">Password (min 6 characters)</label>
            <input type="password" id="password" name="password" minlength="6" required>

            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Log in</a></p>
    </div>
</div>

<script src="assets/theme-toggle.js"></script>
</body>
</html>
