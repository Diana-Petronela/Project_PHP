<?php

require_once __DIR__ . '/config.php';

// One shared PDO connection per request - static keeps it alive across calls.
function get_db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            // throw on DB errors instead of failing silently
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // fetch rows as assoc arrays, not numeric ones
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // real server-side prepared statements, not emulated
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    return $pdo;
}
