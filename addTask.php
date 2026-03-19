<?php
include 'includes/auth.php';
include 'config/db.php';

$title = "";
$description = "";
$priority = "Medium";
$status = "Pending";
$due_date = "";
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $priority = trim($_POST['priority']);
    $status = trim($_POST['status']);
    $due_date = trim($_POST['due_date']);
    $user_id = $_SESSION['user_id'];

    if ($title == "") {
        $message = "Task title is required.";
        $messageType = "error";
    } else {
        if ($due_date == "") {
            $due_date = null;
        }

        $stmt = mysqli_prepare($conn, "INSERT INTO tasks (user_id, title, description, priority, status, due_date) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isssss", $user_id, $title, $description, $priority, $status, $due_date);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Task added successfully.";
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Something went wrong while adding task.";
            $messageType = "error";
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="min-h-screen py-10 bg-gray-100">
    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-blue-600">Add Task</h1>
                <p class="text-gray-500">Create a new task in TaskFlow</p>
            </div>

            <a href="dashboard.php" class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">
                Back
            </a>
        </div>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block mb-1 font-medium">Task Title</label>
                <input
                    type="text"
                    name="title"
                    value="<?php echo htmlspecialchars($title); ?>"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2"
                    placeholder="Enter task title"
                >
            </div>

            <div>
                <label class="block mb-1 font-medium">Description</label>
                <textarea
                    name="description"
                    rows="4"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2"
                    placeholder="Enter task description"
                ><?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block mb-1 font-medium">Priority</label>
                    <select name="priority" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="Low" <?php echo $priority == "Low" ? "selected" : ""; ?>>Low</option>
                        <option value="Medium" <?php echo $priority == "Medium" ? "selected" : ""; ?>>Medium</option>
                        <option value="High" <?php echo $priority == "High" ? "selected" : ""; ?>>High</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="Pending" <?php echo $status == "Pending" ? "selected" : ""; ?>>Pending</option>
                        <option value="Completed" <?php echo $status == "Completed" ? "selected" : ""; ?>>Completed</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Due Date</label>
                    <input
                        type="date"
                        name="due_date"
                        value="<?php echo htmlspecialchars($due_date); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2"
                    >
                </div>
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700"
            >
                Add Task
            </button>
        </form>

        <?php if ($message != ""): ?>
            <div class="mt-6 p-4 rounded-lg <?php echo $messageType == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>