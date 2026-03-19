<?php
include 'includes/auth.php';
include 'config/db.php';

$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn, "SELECT id, title, description, priority, status, due_date, created_at FROM tasks WHERE user_id = ? ORDER BY id DESC");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<?php include 'includes/header.php'; ?>

<div class="min-h-screen bg-gray-100 py-10">
    <div class="max-w-5xl mx-auto px-4">
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

                                        <?php if (!empty($task['due_date'])): ?>
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
                    <p class="text-gray-400 mt-2">Click “Add Task” to create your first task.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>