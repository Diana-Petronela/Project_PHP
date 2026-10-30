<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

start_session();

if (current_user_id() !== null) {
    redirect('index.php');
}

$errors = [];
$email = '';
$justRegistered = isset($_GET['registered']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Email and password are required.';
    } else {
        $pdo = get_db();
        $stmt = $pdo->prepare('SELECT id, name, password_hash FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // checks the password against the stored hash - hashing can't be reversed
        if ($user === false || !password_verify($password, $user['password_hash'])) {
            // vague on purpose - don't let this form reveal which emails exist
            $errors[] = 'Invalid email or password.';
        } else {
            // new session id on login, prevents session fixation
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            redirect('index.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php require __DIR__ . '/partials/theme-init.php'; ?>
    <title>Log in - HelpDesk Mini</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-box">
        <div class="top-row">
            <span class="brand">HelpDesk Mini</span>
            <?php require __DIR__ . '/partials/theme-toggle-button.php'; ?>
        </div>
        <h1>Log in</h1>

        <?php if ($justRegistered): ?>
            <p class="success">Account created. You can log in now.</p>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li><?= h($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="post" action="login.php" novalidate>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= h($email) ?>" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn btn-primary btn-block">Log in</button>
        </form>

        <p>No account yet? <a href="register.php">Register</a></p>
    </div>
</div>

<script src="assets/theme-toggle.js"></script>
</body>
</html>
