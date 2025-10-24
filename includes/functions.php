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
        //echo "is_login: " . is_login() .  ", user_role(): " . user_role();
        header('Location: ../auth/login.php');
        exit;
    } else {
        //echo "You are admin";
    }
}

function uuid()
{
    return bin2hex(random_bytes(16));
}
