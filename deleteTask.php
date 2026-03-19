<?php
include 'includes/auth.php';
include 'config/db.php';

/* ── Accept POST requests only ── */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

/* ── CSRF check ── */
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    header("Location: dashboard.php");
    exit;
}

$task_id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$user_id = $_SESSION['user_id'];

if ($task_id <= 0) {
    header("Location: dashboard.php");
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM tasks WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$_SESSION['success'] = "Task deleted successfully.";
header("Location: dashboard.php");
exit;
?>