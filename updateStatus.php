<?php
include 'includes/auth.php';
include 'config/db.php';

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: dashboard.php");
    exit;
}

$task_id = (int) $_GET['id'];
$new_status = trim($_GET['status']);
$user_id = $_SESSION['user_id'];

if ($new_status !== "Pending" && $new_status !== "Completed") {
    header("Location: dashboard.php");
    exit;
}

$stmt = mysqli_prepare($conn, "UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "sii", $new_status, $task_id, $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: dashboard.php");
exit;