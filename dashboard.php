<?php
include 'includes/auth.php';
include 'config/db.php';

$user_id = $_SESSION['user_id'];

/* Summary card counts */
$totalQuery = mysqli_prepare($conn, "SELECT COUNT(*) AS total_tasks FROM tasks WHERE user_id = ?");
mysqli_stmt_bind_param($totalQuery, "i", $user_id);
mysqli_stmt_execute($totalQuery);
$totalResult = mysqli_stmt_get_result($totalQuery);
$totalData   = mysqli_fetch_assoc($totalResult);
$totalTasks  = $totalData['total_tasks'];

$completedQuery = mysqli_prepare($conn, "SELECT COUNT(*) AS completed_tasks FROM tasks WHERE user_id = ? AND status = 'Completed'");
mysqli_stmt_bind_param($completedQuery, "i", $user_id);
mysqli_stmt_execute($completedQuery);
$completedResult = mysqli_stmt_get_result($completedQuery);
$completedData   = mysqli_fetch_assoc($completedResult);
$completedTasks  = $completedData['completed_tasks'];

$pendingQuery = mysqli_prepare($conn, "SELECT COUNT(*) AS pending_tasks FROM tasks WHERE user_id = ? AND status = 'Pending'");
mysqli_stmt_bind_param($pendingQuery, "i", $user_id);
mysqli_stmt_execute($pendingQuery);
$pendingResult = mysqli_stmt_get_result($pendingQuery);
$pendingData   = mysqli_fetch_assoc($pendingResult);
$pendingTasks  = $pendingData['pending_tasks'];

$overdueQuery = mysqli_prepare($conn, "SELECT COUNT(*) AS overdue_tasks FROM tasks WHERE user_id = ? AND status != 'Completed' AND due_date IS NOT NULL AND due_date != '0000-00-00' AND due_date < CURDATE()");
mysqli_stmt_bind_param($overdueQuery, "i", $user_id);
mysqli_stmt_execute($overdueQuery);
$overdueResult = mysqli_stmt_get_result($overdueQuery);
$overdueData   = mysqli_fetch_assoc($overdueResult);
$overdueTasks  = $overdueData['overdue_tasks'];

/* Search + filter + sort */
$search          = isset($_GET['search'])   ? trim($_GET['search'])   : "";
$status_filter   = isset($_GET['status'])   ? trim($_GET['status'])   : "";
$priority_filter = isset($_GET['priority']) ? trim($_GET['priority']) : "";
$sort            = isset($_GET['sort'])     ? trim($_GET['sort'])     : "latest";

/* Flash message */
$flashMessage = "";
if (isset($_SESSION['success'])) {
    $flashMessage = $_SESSION['success'];
    unset($_SESSION['success']);
}

$sql    = "SELECT id, title, description, priority, status, due_date, created_at FROM tasks WHERE user_id = ?";
$params = [];
$types  = "i";
$params[] = $user_id;

if ($search != "") {
    $sql .= " AND title LIKE ?";
    $types .= "s";
    $searchTerm = "%" . $search . "%";
    $params[] = $searchTerm;
}
if ($status_filter != "") {
    $sql .= " AND status = ?";
    $types .= "s";
    $params[] = $status_filter;
}
if ($priority_filter != "") {
    $sql .= " AND priority = ?";
    $types .= "s";
    $params[] = $priority_filter;
}

if ($sort == "oldest") {
    $sql .= " ORDER BY id ASC";
} elseif ($sort == "due_asc") {
    $sql .= " ORDER BY due_date ASC, id DESC";
} elseif ($sort == "due_desc") {
    $sql .= " ORDER BY due_date DESC, id DESC";
} elseif ($sort == "priority_high") {
    $sql .= " ORDER BY FIELD(priority, 'High', 'Medium', 'Low'), id DESC";
} elseif ($sort == "priority_low") {
    $sql .= " ORDER BY FIELD(priority, 'Low', 'Medium', 'High'), id DESC";
} else {
    $sql .= " ORDER BY id DESC";
}

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<?php include 'includes/header.php'; ?>

<!-- ── Top Navigation ──────────────────────────────── -->
<nav class="top-nav">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
        <!-- Logo -->
        <a href="dashboard.php" class="logo-mark text-lg">
            <span class="logo-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 11l3 3L22 4"/>
                    <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
            </span>
            TaskFlow
        </a>

        <!-- Right: user + actions -->
        <div class="flex items-center gap-2 sm:gap-3">
            <span class="hidden sm:inline-flex items-center gap-2 text-sm text-slate-500 font-medium">
                <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 font-bold text-xs flex items-center justify-center uppercase select-none">
                    <?php echo htmlspecialchars(substr($_SESSION['user_name'], 0, 1)); ?>
                </span>
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>
            <a href="addTask.php" class="btn btn-primary text-sm px-3 py-1.5 gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span class="hidden sm:inline">New Task</span>
                <span class="sm:hidden">Add</span>
            </a>
            <a href="logout.php" class="btn btn-secondary text-sm px-3 py-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span class="hidden sm:inline">Logout</span>
            </a>
        </div>
    </div>
</nav>

<!-- ── Page Content ────────────────────────────────── -->
<main class="max-w-6xl mx-auto px-4 sm:px-6 py-8">

    <!-- Welcome -->
    <div class="mb-7 animate-fade-in">
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">
            Good <?php
                $hour = (int) date('H');
                if ($hour < 12) echo "morning";
                elseif ($hour < 18) echo "afternoon";
                else echo "evening";
            ?>, <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?> 👋
        </h1>
        <p class="text-slate-500 mt-1 text-sm">Here's an overview of your tasks today.</p>
    </div>

    <!-- Flash Toast -->
    <?php if ($flashMessage != ""): ?>
        <div id="flash-toast" class="flash-toast success mb-6">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <?php echo htmlspecialchars($flashMessage); ?>
        </div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-7">

        <!-- Total -->
        <div class="stat-card animate-slide-up" style="animation-delay:0.05s">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total</p>
                <span class="stat-icon bg-indigo-50">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800"><?php echo $totalTasks; ?></p>
            <p class="text-xs text-slate-400 mt-1">All tasks</p>
        </div>

        <!-- Completed -->
        <div class="stat-card animate-slide-up" style="animation-delay:0.10s">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Done</p>
                <span class="stat-icon bg-emerald-50">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-emerald-600"><?php echo $completedTasks; ?></p>
            <p class="text-xs text-slate-400 mt-1">Completed</p>
        </div>

        <!-- Pending -->
        <div class="stat-card animate-slide-up" style="animation-delay:0.15s">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Pending</p>
                <span class="stat-icon bg-amber-50">
                    <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-amber-500"><?php echo $pendingTasks; ?></p>
            <p class="text-xs text-slate-400 mt-1">In progress</p>
        </div>

        <!-- Overdue -->
        <div class="stat-card animate-slide-up" style="animation-delay:0.20s">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Overdue</p>
                <span class="stat-icon bg-red-50">
                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-red-600"><?php echo $overdueTasks; ?></p>
            <p class="text-xs text-slate-400 mt-1">Needs attention</p>
        </div>
    </div>

    <!-- Filter Row -->
    <div class="tf-card mb-6 p-5 animate-fade-in">
        <form method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label class="tf-label" for="search">Search</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="<?php echo htmlspecialchars($search); ?>"
                            class="tf-input pl-9"
                            placeholder="Search tasks…"
                        >
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="tf-label" for="status">Status</label>
                    <select id="status" name="status" class="tf-input">
                        <option value="">All statuses</option>
                        <option value="Pending"   <?php echo $status_filter == "Pending"   ? "selected" : ""; ?>>Pending</option>
                        <option value="Completed" <?php echo $status_filter == "Completed" ? "selected" : ""; ?>>Completed</option>
                    </select>
                </div>

                <!-- Priority -->
                <div>
                    <label class="tf-label" for="priority">Priority</label>
                    <select id="priority" name="priority" class="tf-input">
                        <option value="">All priorities</option>
                        <option value="Low"    <?php echo $priority_filter == "Low"    ? "selected" : ""; ?>>Low</option>
                        <option value="Medium" <?php echo $priority_filter == "Medium" ? "selected" : ""; ?>>Medium</option>
                        <option value="High"   <?php echo $priority_filter == "High"   ? "selected" : ""; ?>>High</option>
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <label class="tf-label" for="sort">Sort by</label>
                    <select id="sort" name="sort" class="tf-input">
                        <option value="latest"        <?php echo $sort == "latest"        ? "selected" : ""; ?>>Latest first</option>
                        <option value="oldest"        <?php echo $sort == "oldest"        ? "selected" : ""; ?>>Oldest first</option>
                        <option value="due_asc"       <?php echo $sort == "due_asc"       ? "selected" : ""; ?>>Due date ↑</option>
                        <option value="due_desc"      <?php echo $sort == "due_desc"      ? "selected" : ""; ?>>Due date ↓</option>
                        <option value="priority_high" <?php echo $sort == "priority_high" ? "selected" : ""; ?>>High priority first</option>
                        <option value="priority_low"  <?php echo $sort == "priority_low"  ? "selected" : ""; ?>>Low priority first</option>
                    </select>
                </div>
            </div>

            <!-- Filter actions -->
            <div class="flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary text-sm px-4 py-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    Apply filters
                </button>
                <a href="dashboard.php" class="btn btn-secondary text-sm px-4 py-2">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Task List -->
    <div class="tf-card p-5 sm:p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="section-title">
                Your Tasks
                <?php
                    $count = mysqli_num_rows($result);
                    if ($count > 0):
                ?>
                <span class="ml-2 text-xs font-semibold bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-full"><?php echo $count; ?></span>
                <?php endif; ?>
            </h2>
        </div>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="space-y-3 stagger">
                <?php while ($task = mysqli_fetch_assoc($result)): ?>
                    <?php
                        $isOverdue = (
                            !empty($task['due_date']) &&
                            $task['due_date'] != '0000-00-00' &&
                            $task['status'] != 'Completed' &&
                            strtotime($task['due_date']) < strtotime(date('Y-m-d'))
                        );

                        /* Priority badge class */
                        $priorityClass = 'badge-low';
                        if ($task['priority'] == 'High')   $priorityClass = 'badge-high';
                        if ($task['priority'] == 'Medium') $priorityClass = 'badge-medium';

                        /* Status badge class */
                        $statusClass = $task['status'] == 'Completed' ? 'badge-completed' : 'badge-pending';

                        /* Due date formatted */
                        $dueDateFormatted = '';
                        if (!empty($task['due_date']) && $task['due_date'] != '0000-00-00') {
                            $dueDateFormatted = date('M j, Y', strtotime($task['due_date']));
                        }
                    ?>
                    <div class="task-card <?php echo $isOverdue ? 'overdue-card' : ''; ?>">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">

                            <!-- Left: content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start gap-2 flex-wrap">
                                    <h3 class="text-base font-semibold text-slate-800 break-words leading-snug <?php echo $task['status'] == 'Completed' ? 'line-through text-slate-400' : ''; ?>">
                                        <?php echo htmlspecialchars($task['title']); ?>
                                    </h3>
                                    <?php if ($isOverdue): ?>
                                        <span class="badge badge-overdue self-center">
                                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                                            Overdue
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($task['description'])): ?>
                                    <p class="text-slate-500 text-sm mt-1.5 break-words line-clamp-2 leading-relaxed">
                                        <?php echo htmlspecialchars($task['description']); ?>
                                    </p>
                                <?php endif; ?>

                                <!-- Badges row -->
                                <div class="flex flex-wrap gap-1.5 mt-3">
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($task['status']); ?>
                                    </span>
                                    <span class="badge <?php echo $priorityClass; ?>">
                                        <?php echo htmlspecialchars($task['priority']); ?> priority
                                    </span>
                                    <?php if ($dueDateFormatted): ?>
                                        <span class="badge badge-due">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                            </svg>
                                            <?php echo $dueDateFormatted; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Right: actions -->
                            <div class="flex flex-row sm:flex-col lg:flex-row gap-2 flex-shrink-0 self-start">
                                <?php if ($task['status'] == "Pending"): ?>
                                    <form action="updateStatus.php" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                        <input type="hidden" name="status" value="Completed">
                                        <button type="submit" class="btn btn-success text-xs px-3 py-1.5 w-full">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Done
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form action="updateStatus.php" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                        <input type="hidden" name="status" value="Pending">
                                        <button type="submit" class="btn btn-warning text-xs px-3 py-1.5 w-full">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/>
                                            </svg>
                                            Reopen
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <a href="editTask.php?id=<?php echo $task['id']; ?>"
                                   class="btn btn-secondary text-xs px-3 py-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Edit
                                </a>

                                <form action="deleteTask.php" method="POST" class="inline delete-form">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                    <button type="submit" class="btn btn-danger text-xs px-3 py-1.5 w-full">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

        <?php else: ?>
            <!-- Empty state -->
            <div class="empty-state animate-fade-in">
                <div class="empty-icon mx-auto">
                    <svg class="w-10 h-10 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-700">No tasks found</h3>
                <p class="text-slate-400 text-sm mt-1 mb-5">
                    <?php if ($search != "" || $status_filter != "" || $priority_filter != ""): ?>
                        Try adjusting your filters, or
                    <?php else: ?>
                        You haven't created any tasks yet.
                    <?php endif; ?>
                </p>
                <a href="addTask.php" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Create your first task
                </a>
            </div>
        <?php endif; ?>
    </div>

</main>

<?php include 'includes/footer.php'; ?>