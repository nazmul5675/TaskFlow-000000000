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
    $_SESSION['success'] = "";  /* clear any leftover flash */
    header("Location: dashboard.php");
    exit;
}

$task_id    = isset($_POST['id'])     ? (int) $_POST['id']         : 0;
$new_status = isset($_POST['status']) ? trim($_POST['status'])      : '';
$user_id    = $_SESSION['user_id'];

$allowedStatuses = ["Pending", "Completed"];

if ($task_id <= 0 || !in_array($new_status, $allowedStatuses)) {
    header("Location: dashboard.php");
    exit;
}

$stmt = mysqli_prepare($conn, "UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "sii", $new_status, $task_id, $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$_SESSION['success'] = "Task status updated successfully.";
header("Location: dashboard.php");
exit;
?>