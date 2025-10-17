<?php
function is_login()
{
    return isset($_SESSION['user']);
}

function user_role()
{
    return $_SESSION['user']['role'] ?? 'guest';
}

function require_role($roles)
{
    if (!is_login() || !in_array(user_role(), (array)$roles)) {
        header('Location: ../login.php');
        exit;
    }
}
