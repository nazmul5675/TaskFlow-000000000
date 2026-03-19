<?php
/* =============================================================
   TaskFlow — Database Configuration
   =============================================================
   Priority order for credentials:
   1. Real environment variables (set by Railway / any PaaS)
   2. config/.env.php  (PHP file — use on InfinityFree or shared hosting)
   3. Hardcoded local-dev fallback (XAMPP defaults)
   ============================================================= */

/* ── 1. Load .env.php if it exists (InfinityFree / shared hosting) ── */
$envFile = __DIR__ . '/.env.php';
if (file_exists($envFile)) {
    require_once $envFile;
}

/* ── 2. Resolve credentials ──────────────────────────────────────────
   getenv() picks up real server env vars (Railway, Heroku, etc.)
   DB_ENV_* constants can be defined inside config/.env.php
   The ?? fallback chain ends at safe XAMPP defaults.
   ------------------------------------------------------------------ */
$db_host = getenv('DB_HOST') ?: (defined('DB_HOST') ? DB_HOST : 'localhost');
$db_port = getenv('DB_PORT') ?: (defined('DB_PORT') ? DB_PORT : 3306);
$db_name = getenv('DB_NAME') ?: (defined('DB_NAME') ? DB_NAME : 'taskflow_db');
$db_user = getenv('DB_USER') ?: (defined('DB_USER') ? DB_USER : 'root');
$db_pass = getenv('DB_PASS') ?: (defined('DB_PASS') ? DB_PASS : '');

/* ── 3. Connect ──────────────────────────────────────────────────── */
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, (int) $db_port);

if (!$conn) {
    /* In production show a generic message; do not expose credentials */
    http_response_code(500);
    die("Database connection failed. Please check your configuration.");
}

/* ── 4. Force UTF-8 multibyte charset ────────────────────────────── */
mysqli_set_charset($conn, 'utf8mb4');
?>