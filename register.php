<?php
session_start();
include 'config/db.php';

$name = "";
$email = "";
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($name == "" || $email == "" || $password == "") {
        $message = "All fields are required.";
        $messageType = "error";
    } else {
        $checkStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($checkStmt, "s", $email);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) > 0) {
            $message = "Email already exists.";
            $messageType = "error";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertStmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($insertStmt, "sss", $name, $email, $hashedPassword);

            if (mysqli_stmt_execute($insertStmt)) {
                $_SESSION['success'] = "Registration successful.";
                header("Location: register.php");
                exit;
            } else {
                $message = "Something went wrong.";
                $messageType = "error";
            }

            mysqli_stmt_close($insertStmt);
        }

        mysqli_stmt_close($checkStmt);
    }
}

if (isset($_SESSION['success'])) {
    $message = $_SESSION['success'];
    $messageType = "success";
    unset($_SESSION['success']);
}
?>

<?php include 'includes/header.php'; ?>

<div class="min-h-screen flex items-center justify-center py-10">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-2xl font-bold text-blue-600 text-center mb-2">Create Account</h1>
        <p class="text-gray-500 text-center mb-6">Register for TaskFlow</p>

        <form method="POST" autocomplete="off" class="space-y-4">
            <div>
                <label class="block mb-1 font-medium">Name</label>
                <input
                    type="text"
                    name="name"
                    value="<?php echo htmlspecialchars($name); ?>"
                    autocomplete="off"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2"
                    placeholder="Enter your name"
                >
            </div>

            <div>
                <label class="block mb-1 font-medium">Email</label>
                <input
                    type="email"
                    name="email"
                    value="<?php echo htmlspecialchars($email); ?>"
                    autocomplete="off"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2"
                    placeholder="Enter your email"
                >
            </div>

            <div>
                <label class="block mb-1 font-medium">Password</label>
                <input
                    type="password"
                    name="password"
                    autocomplete="new-password"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2"
                    placeholder="Enter your password"
                >
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700"
            >
                Register
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