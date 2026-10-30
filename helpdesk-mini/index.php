<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

start_session();
require_login();

$pdo = get_db();

// filtered by user_id in the query itself, not just hidden in the HTML
$stmt = $pdo->prepare(
    'SELECT id, title, status, created_at FROM tickets WHERE user_id = ? ORDER BY created_at DESC'
);
$stmt->execute([current_user_id()]);
$tickets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php require __DIR__ . '/partials/theme-init.php'; ?>
    <title>My tickets - HelpDesk Mini</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php require __DIR__ . '/partials/nav.php'; ?>

<main class="container">
    <div class="page-header">
        <h1>My tickets</h1>
        <a class="btn btn-primary" href="ticket_new.php">+ New ticket</a>
    </div>

    <?php if (empty($tickets)): ?>
        <div class="empty-state">
            <p>You have no tickets yet.</p>
            <a class="btn btn-primary" href="ticket_new.php">Create your first ticket</a>
        </div>
    <?php else: ?>
        <div class="tickets-card">
            <table class="tickets-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr data-ticket-id="<?= (int) $ticket['id'] ?>">
                            <td><a href="ticket_view.php?id=<?= (int) $ticket['id'] ?>"><?= h($ticket['title']) ?></a></td>
                            <td>
                                <span class="status-wrap">
                                    <select class="status-select <?= status_class($ticket['status']) ?>"
                                            data-ticket-id="<?= (int) $ticket['id'] ?>"
                                            data-current-status="<?= h($ticket['status']) ?>">
                                        <?php foreach (['Open', 'In Progress', 'On Hold', 'Closed'] as $status): ?>
                                            <option value="<?= h($status) ?>" <?= $status === $ticket['status'] ? 'selected' : '' ?>>
                                                <?= h($status) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="status-saved" aria-hidden="true"></span>
                                </span>
                            </td>
                            <td class="created"><?= h($ticket['created_at']) ?></td>
                            <td>
                                <div class="row-actions">
                                    <a class="btn btn-ghost btn-sm" href="ticket_edit.php?id=<?= (int) $ticket['id'] ?>">Edit</a>
                                    <form method="post" action="ticket_delete.php" class="inline-form"
                                          onsubmit="return confirm('Delete this ticket?');">
                                        <input type="hidden" name="id" value="<?= (int) $ticket['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<script src="assets/app.js"></script>
<script src="assets/theme-toggle.js"></script>
</body>
</html>
