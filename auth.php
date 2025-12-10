<?php
session_start();

function isAdminAuthenticated(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}

function requireAdminAuthentication(): void
{
    if (!isAdminAuthenticated()) {
        header('Location: index.php');
        exit;
    }
}
