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

$ticketId = (int) ($input['ticket_id'] ?? 0);
$note = trim($input['note'] ?? '');

if ($note === '') {
    json_error(422, 'Note cannot be empty.');
}

if (strlen($note) > 2000) {
    json_error(422, 'Note is too long (max 2000 characters).');
}

$pdo = get_db();

// ownership check - can only add notes to your own ticket
$stmt = $pdo->prepare('SELECT id FROM tickets WHERE id = ? AND user_id = ?');
$stmt->execute([$ticketId, current_user_id()]);

if ($stmt->fetch() === false) {
    json_error(404, 'Ticket not found.');
}

$stmt = $pdo->prepare('INSERT INTO ticket_notes (ticket_id, note) VALUES (?, ?)');
$stmt->execute([$ticketId, $note]);

$noteId = $pdo->lastInsertId();

// read it back so the timestamp matches what a page reload would show
$stmt = $pdo->prepare('SELECT id, note, created_at FROM ticket_notes WHERE id = ?');
$stmt->execute([$noteId]);
$savedNote = $stmt->fetch();

echo json_encode(['ok' => true, 'note' => $savedNote]);
