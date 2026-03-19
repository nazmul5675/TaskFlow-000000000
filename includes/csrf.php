<?php
/* =============================================================
   TaskFlow — CSRF Protection Helpers
   =============================================================
   generate_csrf_token() : creates/returns a token stored in the session
   verify_csrf_token($t) : verifies the submitted token with hash_equals
   csrf_field()          : prints a ready-to-use hidden <input> field
   ============================================================= */

function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(string $submitted): bool {
    if (empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $submitted);
}

function csrf_field(): string {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}
?>
