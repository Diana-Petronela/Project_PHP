<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

start_session();
require_login();

$pdo = get_db();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

// ownership check - wrong user's ticket id just 404s below
$stmt = $pdo->prepare('SELECT * FROM tickets WHERE id = ? AND user_id = ?');
$stmt->execute([$id, current_user_id()]);
$ticket = $stmt->fetch();

if ($ticket === false) {
    http_response_code(404);
    echo 'Ticket not found.';
    exit;
}

$stmt = $pdo->prepare('SELECT id, note, created_at FROM ticket_notes WHERE ticket_id = ? ORDER BY created_at ASC');
$stmt->execute([$ticket['id']]);
$notes = $stmt->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? '';

    $allowedStatuses = ['Open', 'In Progress', 'On Hold', 'Closed'];

    if ($title === '') {
        $errors[] = 'Title is required.';
    }

    if ($description === '') {
        $errors[] = 'Description is required.';
    }

    if (!in_array($status, $allowedStatuses, true)) {
        $errors[] = 'Invalid status.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'UPDATE tickets SET title = ?, description = ?, status = ? WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([$title, $description, $status, $id, current_user_id()]);

        redirect('ticket_view.php?id=' . $id);
    }

    // show back what they typed if validation failed
    $ticket['title'] = $title;
    $ticket['description'] = $description;
    $ticket['status'] = $status;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php require __DIR__ . '/partials/theme-init.php'; ?>
    <title>Edit ticket - HelpDesk Mini</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php require __DIR__ . '/partials/nav.php'; ?>

<main class="container container-wide">
    <h1>Edit ticket</h1>

    <?php if (!empty($errors)): ?>
        <ul class="errors">
            <?php foreach ($errors as $error): ?>
                <li><?= h($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="ticket-layout">
        <div class="form-card">
            <form method="post" action="ticket_edit.php?id=<?= (int) $ticket['id'] ?>" class="ticket-form">
                <input type="hidden" name="id" value="<?= (int) $ticket['id'] ?>">

                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="<?= h($ticket['title']) ?>" required>

                <label for="description">Description</label>
                <textarea id="description" name="description" rows="5" required><?= h($ticket['description']) ?></textarea>

                <label for="status">Status</label>
                <select id="status" name="status">
                    <?php foreach (['Open', 'In Progress', 'On Hold', 'Closed'] as $status): ?>
                        <option value="<?= h($status) ?>" <?= $status === $ticket['status'] ? 'selected' : '' ?>>
                            <?= h($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <a class="btn btn-ghost" href="ticket_view.php?id=<?= (int) $ticket['id'] ?>">Cancel</a>
                </div>
            </form>
        </div>

        <aside class="notes-panel">
            <h2>Working notes</h2>

            <div class="notes-list" id="notes-list">
                <?php if (empty($notes)): ?>
                    <p class="notes-empty" id="notes-empty">No notes yet - log the troubleshooting steps here.</p>
                <?php else: ?>
                    <?php foreach ($notes as $note): ?>
                        <div class="note-item">
                            <p class="note-text"><?= nl2br(h($note['note'])) ?></p>
                            <span class="note-time"><?= h($note['created_at']) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <form id="notes-form" class="notes-form" data-ticket-id="<?= (int) $ticket['id'] ?>">
                <textarea id="note-input" name="note" rows="2" placeholder="Add a troubleshooting step..." required></textarea>
                <button type="submit" class="btn btn-primary btn-sm">Send</button>
            </form>
        </aside>
    </div>
</main>

<script src="assets/notes.js"></script>
<script src="assets/theme-toggle.js"></script>
</body>
</html>
