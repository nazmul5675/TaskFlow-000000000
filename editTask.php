<?php
include 'includes/auth.php';
include 'config/db.php';

if (!isset($_GET['id']) || $_GET['id'] == "") {
    header("Location: dashboard.php");
    exit;
}

$task_id = (int) $_GET['id'];
$user_id = $_SESSION['user_id'];

$title       = "";
$description = "";
$priority    = "Medium";
$status      = "Pending";
$due_date    = "";
$message     = "";
$messageType = "";

/* Load current task */
$stmt = mysqli_prepare($conn, "SELECT id, title, description, priority, status, due_date FROM tasks WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) != 1) {
    mysqli_stmt_close($stmt);
    header("Location: dashboard.php");
    exit;
}

$task = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$title       = $task['title'];
$description = $task['description'];
$priority    = $task['priority'];
$status      = $task['status'];
$due_date    = $task['due_date'];

/* Update task */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $priority    = trim($_POST['priority']);
    $status      = trim($_POST['status']);
    $due_date    = trim($_POST['due_date']);

    if ($title == "") {
        $message = "Task title is required.";
        $messageType = "error";
    } else {
        if ($due_date == "") {
            $due_date = null;
        }

        $updateStmt = mysqli_prepare($conn, "UPDATE tasks SET title = ?, description = ?, priority = ?, status = ?, due_date = ? WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($updateStmt, "sssssii", $title, $description, $priority, $status, $due_date, $task_id, $user_id);

        if (mysqli_stmt_execute($updateStmt)) {
            $_SESSION['success'] = "Task updated successfully.";
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Something went wrong while updating the task.";
            $messageType = "error";
        }

        mysqli_stmt_close($updateStmt);
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="min-h-screen py-10 px-4">
    <div class="max-w-2xl mx-auto">

        <!-- Back link -->
        <a href="dashboard.php" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors mb-6">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Back to Dashboard
        </a>

        <!-- Card -->
        <div class="tf-card p-7 animate-scale-in">

            <!-- Header -->
            <div class="flex items-center gap-3 mb-7">
                <span class="stat-icon bg-emerald-50 w-11 h-11 rounded-xl">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </span>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Edit Task</h1>
                    <p class="text-slate-500 text-sm mt-0.5">Update the details of your task</p>
                </div>
            </div>

            <hr class="tf-divider mt-0 mb-6">

            <!-- Alert -->
            <?php if ($message != ""): ?>
                <div class="tf-alert <?php echo $messageType; ?> mb-5 animate-slide-down">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" class="space-y-5">

                <!-- Title -->
                <div>
                    <label class="tf-label" for="title">Task title <span class="text-red-400">*</span></label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="<?php echo htmlspecialchars($title); ?>"
                        class="tf-input"
                        placeholder="e.g. Design homepage wireframe"
                    >
                </div>

                <!-- Description -->
                <div>
                    <label class="tf-label" for="description">Description <span class="text-slate-400 font-normal normal-case">(optional)</span></label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="tf-input resize-y"
                        placeholder="Add any notes or details about this task…"
                    ><?php echo htmlspecialchars($description); ?></textarea>
                    <p class="text-xs text-slate-400 mt-1">Max 1000 characters</p>
                </div>

                <!-- Priority / Status / Due Date -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="tf-label" for="priority">Priority</label>
                        <select id="priority" name="priority" class="tf-input">
                            <option value="Low"    <?php echo $priority == "Low"    ? "selected" : ""; ?>>🔵 Low</option>
                            <option value="Medium" <?php echo $priority == "Medium" ? "selected" : ""; ?>>🟡 Medium</option>
                            <option value="High"   <?php echo $priority == "High"   ? "selected" : ""; ?>>🔴 High</option>
                        </select>
                    </div>
                    <div>
                        <label class="tf-label" for="status">Status</label>
                        <select id="status" name="status" class="tf-input">
                            <option value="Pending"   <?php echo $status == "Pending"   ? "selected" : ""; ?>>⏳ Pending</option>
                            <option value="Completed" <?php echo $status == "Completed" ? "selected" : ""; ?>>✅ Completed</option>
                        </select>
                    </div>
                    <div>
                        <label class="tf-label" for="due_date">Due date</label>
                        <input
                            type="date"
                            id="due_date"
                            name="due_date"
                            value="<?php echo htmlspecialchars($due_date ?? ''); ?>"
                            class="tf-input"
                        >
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3 pt-1">
                    <button type="submit" class="btn btn-success flex-1 justify-content-center py-2.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Changes
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary py-2.5">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>