<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

start_session();
require_login();

$errors = [];
$title = '';
$description = '';
$workingNotes = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // notes typed before the ticket exists travel as a JSON array in a
    // hidden field - decode them and drop anything empty or too long
    $decodedNotes = json_decode($_POST['working_notes'] ?? '[]', true);
    if (is_array($decodedNotes)) {
        foreach ($decodedNotes as $note) {
            $note = trim((string) $note);
            if ($note !== '' && strlen($note) <= 2000) {
                $workingNotes[] = $note;
            }
        }
    }

    if ($title === '') {
        $errors[] = 'Title is required.';
    }

    if ($description === '') {
        $errors[] = 'Description is required.';
    }

    if (empty($errors)) {
        $pdo = get_db();
        $stmt = $pdo->prepare(
            'INSERT INTO tickets (user_id, title, description, status) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([current_user_id(), $title, $description, 'Open']);

        $ticketId = $pdo->lastInsertId();

        if (!empty($workingNotes)) {
            $stmt = $pdo->prepare('INSERT INTO ticket_notes (ticket_id, note) VALUES (?, ?)');
            foreach ($workingNotes as $note) {
                $stmt->execute([$ticketId, $note]);
            }
        }

        redirect('index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php require __DIR__ . '/partials/theme-init.php'; ?>
    <title>New ticket - HelpDesk Mini</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php require __DIR__ . '/partials/nav.php'; ?>

<main class="container container-wide">
    <h1>New ticket</h1>

    <?php if (!empty($errors)): ?>
        <ul class="errors">
            <?php foreach ($errors as $error): ?>
                <li><?= h($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="ticket-layout">
        <div class="form-card">
            <form method="post" action="ticket_new.php" class="ticket-form">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="<?= h($title) ?>" required>

                <label for="description">Description</label>
                <textarea id="description" name="description" rows="5" required><?= h($description) ?></textarea>

                <input type="hidden" name="working_notes" id="working-notes-input" value="<?= h(json_encode($workingNotes)) ?>">

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Create ticket</button>
                    <a class="btn btn-ghost" href="index.php">Cancel</a>
                </div>
            </form>
        </div>

        <aside class="notes-panel">
            <h2>Working notes</h2>
            <p class="notes-hint">Optional - saved together with the ticket.</p>

            <div class="notes-list" id="notes-list">
                <p class="notes-empty" id="notes-empty">No notes yet.</p>
            </div>

            <form id="staged-notes-form" class="notes-form">
                <textarea id="note-input" rows="2" placeholder="Add a troubleshooting step..."></textarea>
                <button type="submit" class="btn btn-primary btn-sm">Send</button>
            </form>
        </aside>
    </div>
</main>

<script src="assets/notes-staged.js"></script>
<script src="assets/theme-toggle.js"></script>
</body>
</html>
