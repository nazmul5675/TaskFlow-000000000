<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<?php include 'includes/header.php'; ?>

<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-2xl p-8 text-center">
        <h1 class="text-3xl font-bold text-blue-600 mb-3">Dashboard</h1>
        <p class="text-gray-700 mb-2">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
        <p class="text-gray-500 mb-4">You are logged in.</p>

        <a href="logout.php" class="inline-block bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
            Logout
        </a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>