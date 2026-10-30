<?php

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

start_session();

// called via fetch, so we reply with JSON instead of redirecting
header('Content-Type: application/json');

function json_error(int $code, string $message): void
{
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $message]);
    exit;
}

if (current_user_id() === null) {
    json_error(401, 'Not logged in.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error(405, 'Method not allowed.');
}

// JSON body, not a form post, so it won't be in $_POST - read it ourselves
$input = json_decode(file_get_contents('php://input'), true);

$id = (int) ($input['id'] ?? 0);
$status = $input['status'] ?? '';
$allowedStatuses = ['Open', 'In Progress', 'On Hold', 'Closed'];

if (!in_array($status, $allowedStatuses, true)) {
    json_error(422, 'Invalid status.');
}

$pdo = get_db();

// ownership check again, same as every other ticket-modifying endpoint
$stmt = $pdo->prepare('UPDATE tickets SET status = ? WHERE id = ? AND user_id = ?');
$stmt->execute([$status, $id, current_user_id()]);

if ($stmt->rowCount() === 0) {
    // same error whether it's missing or someone else's - don't leak which
    json_error(404, 'Ticket not found.');
}

echo json_encode(['ok' => true, 'status' => $status]);
