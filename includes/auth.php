<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* Load CSRF helpers — auth.php is always included first on protected pages */
require_once __DIR__ . '/csrf.php';
?>