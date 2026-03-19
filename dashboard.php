<?php
include 'includes/auth.php';
include 'config/db.php';

$user_id = $_SESSION['user_id'];

/* Summary card counts */
$totalQuery = mysqli_prepare($conn, "SELECT COUNT(*) AS total_tasks FROM tasks WHERE user_id = ?");
mysqli_stmt_bind_param($totalQuery, "i", $user_id);
mysqli_stmt_execute($totalQuery);
$totalResult = mysqli_stmt_get_result($totalQuery);
$totalData = mysqli_fetch_assoc($totalResult);
$totalTasks = $totalData['total_tasks'];

$completedQuery = mysqli_prepare($conn, "SELECT COUNT(*) AS completed_tasks FROM tasks WHERE user_id = ? AND status = 'Completed'");
mysqli_stmt_bind_param($completedQuery, "i", $user_id);
mysqli_stmt_execute($completedQuery);
$completedResult = mysqli_stmt_get_result($completedQuery);
$completedData = mysqli_fetch_assoc($completedResult);
$completedTasks = $completedData['completed_tasks'];

$pendingQuery = mysqli_prepare($conn, "SELECT COUNT(*) AS pending_tasks FROM tasks WHERE user_id = ? AND status = 'Pending'");
mysqli_stmt_bind_param($pendingQuery, "i", $user_id);
mysqli_stmt_execute($pendingQuery);
$pendingResult = mysqli_stmt_get_result($pendingQuery);
$pendingData = mysqli_fetch_assoc($pendingResult);
$pendingTasks = $pendingData['pending_tasks'];

/* Search + filter */
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : "";
$priority_filter = isset($_GET['priority']) ? trim($_GET['priority']) : "";

/* Flash message */
$flashMessage = "";
if (isset($_SESSION['success'])) {
    $flashMessage = $_SESSION['success'];
    unset($_SESSION['success']);
}

$sql = "SELECT id, title, description, priority, status, due_date, created_at 
        FROM tasks 
        WHERE user_id = ?";

$params = [];
$types = "i";
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

$sql .= " ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<?php include 'includes/header.php'; ?>

<div class="min-h-screen bg-gray-100 py-10">
    <div class="max-w-5xl mx-auto px-4">

        <!-- Welcome Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-blue-600">Dashboard</h1>
                    <p class="text-gray-600 mt-1">
                        Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </p>
                </div>

                <div class="flex gap-3">
                    <a href="addTask.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Add Task
                    </a>
                    <a href="logout.php" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm text-gray-500">Total Tasks</p>
                <h3 class="text-3xl font-bold text-blue-600 mt-2">
                    <?php echo $totalTasks; ?>
                </h3>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm text-gray-500">Completed Tasks</p>
                <h3 class="text-3xl font-bold text-green-600 mt-2">
                    <?php echo $completedTasks; ?>
                </h3>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm text-gray-500">Pending Tasks</p>
                <h3 class="text-3xl font-bold text-yellow-500 mt-2">
                    <?php echo $pendingTasks; ?>
                </h3>
            </div>
        </div>

        <!-- Flash Message -->
        <?php if ($flashMessage != ""): ?>
            <div class="bg-green-100 text-green-700 rounded-2xl shadow-lg p-4 mb-6">
                <?php echo htmlspecialchars($flashMessage); ?>
            </div>
        <?php endif; ?>

        <!-- Search and Filter -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block mb-1 font-medium">Search</label>
                    <input
                        type="text"
                        name="search"
                        value="<?php echo htmlspecialchars($search); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2"
                        placeholder="Search by title"
                    >
                </div>

                <div>
                    <label class="block mb-1 font-medium">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="">All</option>
                        <option value="Pending" <?php echo $status_filter == "Pending" ? "selected" : ""; ?>>Pending</option>
                        <option value="Completed" <?php echo $status_filter == "Completed" ? "selected" : ""; ?>>Completed</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Priority</label>
                    <select name="priority" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="">All</option>
                        <option value="Low" <?php echo $priority_filter == "Low" ? "selected" : ""; ?>>Low</option>
                        <option value="Medium" <?php echo $priority_filter == "Medium" ? "selected" : ""; ?>>Medium</option>
                        <option value="High" <?php echo $priority_filter == "High" ? "selected" : ""; ?>>High</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button
                        type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                    >
                        Apply
                    </button>

                    <a
                        href="dashboard.php"
                        class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300"
                    >
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Task List -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-2xl font-semibold mb-4">Your Tasks</h2>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="space-y-4">
                    <?php while ($task = mysqli_fetch_assoc($result)): ?>
                        <div class="border border-gray-200 rounded-xl p-4">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800">
                                        <?php echo htmlspecialchars($task['title']); ?>
                                    </h3>

                                    <p class="text-gray-600 mt-2">
                                        <?php echo nl2br(htmlspecialchars($task['description'])); ?>
                                    </p>

                                    <div class="mt-3 flex flex-wrap gap-2 text-sm">
                                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full">
                                            Priority: <?php echo htmlspecialchars($task['priority']); ?>
                                        </span>

                                        <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full">
                                            Status: <?php echo htmlspecialchars($task['status']); ?>
                                        </span>

                                        <?php if (!empty($task['due_date']) && $task['due_date'] != '0000-00-00'): ?>
                                            <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full">
                                                Due: <?php echo htmlspecialchars($task['due_date']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <?php if ($task['status'] == "Pending"): ?>
                                        <a
                                            href="updateStatus.php?id=<?php echo $task['id']; ?>&status=Completed"
                                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700"
                                        >
                                            Mark Completed
                                        </a>
                                    <?php else: ?>
                                        <a
                                            href="updateStatus.php?id=<?php echo $task['id']; ?>&status=Pending"
                                            class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600"
                                        >
                                            Mark Pending
                                        </a>
                                    <?php endif; ?>

                                    <a
                                        href="editTask.php?id=<?php echo $task['id']; ?>"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                                    >
                                        Edit
                                    </a>

                                    <a
                                        href="deleteTask.php?id=<?php echo $task['id']; ?>"
                                        onclick="return confirm('Are you sure you want to delete this task?')"
                                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700"
                                    >
                                        Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-10">
                    <p class="text-gray-500 text-lg">No tasks found.</p>
                    <p class="text-gray-400 mt-2">Try changing your search/filter or add a new task.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>