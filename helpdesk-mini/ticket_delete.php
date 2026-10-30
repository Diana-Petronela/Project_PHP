<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

start_session();
require_login();

// POST only - a GET link could get pre-fetched and delete by accident
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$pdo = get_db();

// user_id in the WHERE clause too, so you can't delete someone else's ticket
$stmt = $pdo->prepare('DELETE FROM tickets WHERE id = ? AND user_id = ?');
$stmt->execute([$id, current_user_id()]);

redirect('index.php');
