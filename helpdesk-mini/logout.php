<?php

require_once __DIR__ . '/helpers.php';

start_session();

// clear session data and drop the cookie too
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie('PHPSESSID', '', time() - 42000, $params['path'], $params['domain']);
}

session_destroy();

redirect('login.php');
