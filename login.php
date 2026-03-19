<?php
session_start();
include 'config/db.php';

$email = "";
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email == "" || $password == "") {
        $message = "Email and password are required.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Enter a valid email address.";
        $messageType = "error";
    } elseif (strlen($email) > 100) {
        $message = "Email must be under 100 characters.";
        $messageType = "error";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, name, email, password FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_name']  = $user['name'];
                $_SESSION['user_email'] = $user['email'];

                header("Location: dashboard.php");
                exit;
            } else {
                $message = "Invalid email or password.";
                $messageType = "error";
            }
        } else {
            $message = "Invalid email or password.";
            $messageType = "error";
        }

        mysqli_stmt_close($stmt);
    }
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
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Welcome back</h1>
            <p class="text-slate-500 mt-1.5 text-sm">Sign in to your TaskFlow account</p>
        </div>

        <!-- Alert -->
        <?php if ($message != ""): ?>
            <div class="tf-alert <?php echo $messageType; ?> mb-5 animate-slide-down">
                <?php if ($messageType === 'error'): ?>
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                <?php endif; ?>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" autocomplete="off" class="space-y-5">

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
                        placeholder="Enter your password"
                    >
                    <button
                        type="button"
                        class="toggle-password"
                        data-toggle-password
                        data-target="password"
                        title="Toggle password visibility"
                    >
                        <!-- Eye open -->
                        <svg class="icon-eye w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <!-- Eye off -->
                        <svg class="icon-eye-off w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary btn-block mt-1">
                Sign in
            </button>
        </form>

        <!-- Cross link -->
        <p class="text-center text-sm text-slate-500 mt-6">
            Don't have an account?
            <a href="register.php" class="text-indigo-600 font-semibold hover:text-indigo-700 transition-colors">
                Create one
            </a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>