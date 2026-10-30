<?php

// escapes HTML before printing anything a user typed - prevents XSS.
// always wrap user input in h() before echoing it.
function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// starts the session if it's not already running
function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// logged-in user's id, or null if nobody is logged in
function current_user_id(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

// blocks the page unless someone's logged in
function require_login(): void
{
    if (current_user_id() === null) {
        header('Location: login.php');
        exit;
    }
}

// "In Progress" -> "status-in-progress", matches the CSS classes
function status_class(string $status): string
{
    return 'status-' . strtolower(str_replace(' ', '-', $status));
}

// redirects and stops the script - exit is required or PHP keeps running
function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}
