<?php

function is_user_logged_in() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function get_current_username() {
    return $_SESSION['username'] ?? null;
}

function get_current_user_email() {
    return $_SESSION['user_email'] ?? null;
}

function get_current_user_full_name() {
    return $_SESSION['user_full_name'] ?? null;
}

function require_login($redirect_to = null) {
    if (!is_user_logged_in()) {
        if ($redirect_to) {
            $_SESSION['redirect_after_login'] = $redirect_to;
        } else {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        }
        header("Location: login.php");
        exit();
    }
}

function redirect_if_logged_in($redirect_to = 'index.php') {
    if (is_user_logged_in()) {
        header("Location: " . $redirect_to);
        exit();
    }
}
?>
