<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

start_session();
require_login();

$pdo = get_db();
$id = (int) ($_GET['id'] ?? 0);

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php require __DIR__ . '/partials/theme-init.php'; ?>
    <title><?= h($ticket['title']) ?> - HelpDesk Mini</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php require __DIR__ . '/partials/nav.php'; ?>

<main class="container container-wide">
    <a class="back-link" href="index.php">&larr; Back to my tickets</a>

    <div class="ticket-layout">
        <div class="ticket-detail" data-ticket-id="<?= (int) $ticket['id'] ?>">
            <h1><?= h($ticket['title']) ?></h1>

            <p class="meta">
                Created <?= h($ticket['created_at']) ?> &middot; Last updated <?= h($ticket['updated_at']) ?>
            </p>

            <div class="status-wrap">
                <select id="status" class="status-select <?= status_class($ticket['status']) ?>"
                        data-ticket-id="<?= (int) $ticket['id'] ?>"
                        data-current-status="<?= h($ticket['status']) ?>">
                    <?php foreach (['Open', 'In Progress', 'On Hold', 'Closed'] as $status): ?>
                        <option value="<?= h($status) ?>" <?= $status === $ticket['status'] ? 'selected' : '' ?>>
                            <?= h($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="status-saved" aria-hidden="true"></span>
            </div>

            <hr>

            <h2>Description</h2>
            <p class="description"><?= nl2br(h($ticket['description'])) ?></p>

            <div class="form-actions">
                <a class="btn btn-primary" href="ticket_edit.php?id=<?= (int) $ticket['id'] ?>">Edit ticket</a>
                <form method="post" action="ticket_delete.php" class="inline-form"
                      onsubmit="return confirm('Delete this ticket?');">
                    <input type="hidden" name="id" value="<?= (int) $ticket['id'] ?>">
                    <button type="submit" class="btn btn-danger">Delete ticket</button>
                </form>
            </div>
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

<script src="assets/app.js"></script>
<script src="assets/notes.js"></script>
<script src="assets/theme-toggle.js"></script>
</body>
</html>
