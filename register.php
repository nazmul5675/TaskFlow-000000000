<?php
session_start();
include 'config/db.php';

$name = "";
$email = "";
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($name == "" || $email == "" || $password == "") {
        $message = "All fields are required.";
        $messageType = "error";
    } elseif (strlen($name) < 2) {
        $message = "Name must be at least 2 characters.";
        $messageType = "error";
    } elseif (strlen($name) > 100) {
        $message = "Name must be under 100 characters.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Enter a valid email address.";
        $messageType = "error";
    } elseif (strlen($email) > 100) {
        $message = "Email must be under 100 characters.";
        $messageType = "error";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $messageType = "error";
    } elseif (strlen($password) > 255) {
        $message = "Password is too long.";
        $messageType = "error";
    } else {
        $checkStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($checkStmt, "s", $email);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) > 0) {
            $message = "An account with this email already exists.";
            $messageType = "error";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertStmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($insertStmt, "sss", $name, $email, $hashedPassword);

            if (mysqli_stmt_execute($insertStmt)) {
                $_SESSION['success'] = "Account created successfully! You can now log in.";
                header("Location: register.php");
                exit;
            } else {
                $message = "Something went wrong. Please try again.";
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

<div class="min-h-screen flex flex-col items-center justify-center px-4 py-16">

    <!-- Logo -->
    <a href="login.php" class="logo-mark mb-8 animate-fade-in">
        <span class="logo-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 11l3 3L22 4"/>
                <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
            </svg>
        </span>
        TaskFlow
    </a>

    <!-- Auth Card -->
    <div class="auth-card w-full">
        <!-- Header -->
        <div class="text-center mb-7">
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Create your account</h1>
            <p class="text-slate-500 mt-1.5 text-sm">Start managing your tasks with TaskFlow</p>
        </div>

        <!-- Alert -->
        <?php if ($message != ""): ?>
            <div class="tf-alert <?php echo $messageType; ?> mb-5 animate-slide-down">
                <?php if ($messageType === 'success'): ?>
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                <?php else: ?>
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                <?php endif; ?>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" autocomplete="off" class="space-y-5">

            <!-- Name -->
            <div>
                <label class="tf-label" for="name">Full name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?php echo htmlspecialchars($name); ?>"
                    autocomplete="off"
                    class="tf-input"
                    placeholder="Jane Doe"
                >
            </div>

            <!-- Email -->
            <div>
                <label class="tf-label" for="email">Email address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo htmlspecialchars($email); ?>"
                    autocomplete="off"
                    class="tf-input"
                    placeholder="you@example.com"
                >
            </div>

            <!-- Password -->
            <div>
                <label class="tf-label" for="password">Password</label>
                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="new-password"
                        class="tf-input pr-11"
                        placeholder="Min. 6 characters"
                    >
                    <button
                        type="button"
                        class="toggle-password"
                        data-toggle-password
                        data-target="password"
                        title="Toggle password visibility"
                    >
                        <svg class="icon-eye w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg class="icon-eye-off w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary btn-block mt-1">
                Create account
            </button>
        </form>

        <!-- Cross link -->
        <p class="text-center text-sm text-slate-500 mt-6">
            Already have an account?
            <a href="login.php" class="text-indigo-600 font-semibold hover:text-indigo-700 transition-colors">
                Sign in
            </a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>