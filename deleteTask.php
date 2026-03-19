<?php
include 'includes/auth.php';
include 'config/db.php';

if (!isset($_GET['id']) || $_GET['id'] == "") {
    header("Location: dashboard.php");
    exit;
}

$task_id = (int) $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn, "DELETE FROM tasks WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$_SESSION['success'] = "Task deleted successfully.";
header("Location: dashboard.php");
exit;