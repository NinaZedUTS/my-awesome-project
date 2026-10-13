<?php

ini_set(
    'session.use_strict_mode',
    '1'
);

ini_set(
    'session.use_only_cookies',
    '1'
);


session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);


session_start();


function requireLogin()
{
    if (
        empty($_SESSION['logged_in']) ||
        $_SESSION['logged_in'] !== true
    ) {

        header('Location: login.php');

        exit;
    }
}