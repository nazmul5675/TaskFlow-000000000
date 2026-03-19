<?php
include 'includes/auth.php';
include 'config/db.php';

$title       = "";
$description = "";
$priority    = "Medium";
$status      = "Pending";
$due_date    = "";
$message     = "";
$messageType = "";

$allowedPriorities = ["Low", "Medium", "High"];
$allowedStatuses   = ["Pending", "Completed"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* ── CSRF check ── */
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message     = "Invalid request. Please try again.";
        $messageType = "error";
    } else {

        $title       = trim($_POST['title']);
        $description = trim($_POST['description']);
        $priority    = trim($_POST['priority']);
        $status      = trim($_POST['status']);
        $due_date    = trim($_POST['due_date']);
        $user_id     = $_SESSION['user_id'];

        if ($title == "") {
            $message = "Task title is required.";
            $messageType = "error";
        } elseif (strlen($title) < 3) {
            $message = "Task title must be at least 3 characters.";
            $messageType = "error";
        } elseif (strlen($title) > 255) {
            $message = "Task title must be under 255 characters.";
            $messageType = "error";
        } elseif (strlen($description) > 1000) {
            $message = "Description must be under 1000 characters.";
            $messageType = "error";
        } elseif (!in_array($priority, $allowedPriorities)) {
            $message = "Invalid priority selected.";
            $messageType = "error";
        } elseif (!in_array($status, $allowedStatuses)) {
            $message = "Invalid status selected.";
            $messageType = "error";
        } elseif ($due_date != "" && !preg_match("/^\d{4}-\d{2}-\d{2}$/", $due_date)) {
            $message = "Enter a valid due date.";
            $messageType = "error";
        } elseif ($due_date != "" && $status != "Completed" && $due_date < date("Y-m-d")) {
            $message = "Due date cannot be in the past for a pending task.";
            $messageType = "error";
        } else {
            if ($due_date == "") {
                $due_date = null;
            }

            $stmt = mysqli_prepare($conn, "INSERT INTO tasks (user_id, title, description, priority, status, due_date) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "isssss", $user_id, $title, $description, $priority, $status, $due_date);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = "Task added successfully.";
                mysqli_stmt_close($stmt);
                header("Location: dashboard.php");
                exit;
            } else {
                $message     = "Something went wrong while adding the task.";
                $messageType = "error";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="min-h-screen py-10 px-4">
    <div class="max-w-2xl mx-auto">

        <a href="dashboard.php" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors mb-6">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Back to Dashboard
        </a>

        <div class="tf-card p-7 animate-scale-in">
            <div class="flex items-center gap-3 mb-7">
                <span class="stat-icon bg-indigo-50 w-11 h-11 rounded-xl">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                </span>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Add New Task</h1>
                    <p class="text-slate-500 text-sm mt-0.5">Create a task and keep track of your work</p>
                </div>
            </div>

            <hr class="tf-divider mt-0 mb-6">

            <?php if ($message != ""): ?>
                <div class="tf-alert <?php echo $messageType; ?> mb-5 animate-slide-down">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <?php echo csrf_field(); ?>

                <div>
                    <label class="tf-label" for="title">Task title <span class="text-red-400">*</span></label>
                    <input type="text" id="title" name="title"
                           value="<?php echo htmlspecialchars($title); ?>"
                           class="tf-input" placeholder="e.g. Design homepage wireframe">
                </div>

                <div>
                    <label class="tf-label" for="description">Description <span class="text-slate-400 font-normal normal-case">(optional)</span></label>
                    <textarea id="description" name="description" rows="4"
                              class="tf-input resize-y"
                              placeholder="Add any notes or details about this task…"><?php echo htmlspecialchars($description); ?></textarea>
                    <p class="text-xs text-slate-400 mt-1">Max 1000 characters</p>
                </div>

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
                        <input type="date" id="due_date" name="due_date"
                               value="<?php echo htmlspecialchars($due_date); ?>"
                               class="tf-input">
                    </div>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="submit" class="btn btn-primary flex-1 justify-center py-2.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Add Task
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary py-2.5">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>