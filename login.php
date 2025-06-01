<?php
// Include database configuration and authentication utilities
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Initialize error message
$error = '';

// Check if the form has been submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token (cross-site request forgery protection)
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    // Sanitize username input
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $password = $_POST['password']; // Do not sanitize password (hash will not match)

    // Validate length of inputs
    if (strlen($username) < 3 || strlen($password) < 6) {
        $error = "Invalid username or password!";
    } else {
        try {
            // Retrieve user record by username
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            // Verify password hash against stored hash
            if ($user && password_verify($password, $user['password'])) {
                // ✅ Login successful
                session_regenerate_id(true); // prevent session fixation attacks
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Redirect to profile page (or previous page)
                header("Location: " . ($_SESSION['redirect_url'] ?? 'profile.php'));
                exit();
            } else {
                // ❌ Login failed
                $error = "Invalid username or password!";
            }
        } catch (PDOException $e) {
            // Log the error internally; show a generic message to user
            error_log("Login error: " . $e->getMessage());
            $error = "Login failed. Please try again later.";
        }
    }
}

// Include site header
require_once 'includes/header.php';
?>

<!-- ========================= -->
<!-- HTML Login Form -->
<!-- ========================= -->
<div class="auth-form">
    <h2>Login</h2>

    <!-- Show error if exists -->
    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Login form -->
    <form method="POST">
        <!-- CSRF protection -->
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" class="btn">Login</button>
    </form>

    <!-- Link to registration -->
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php require_once 'includes/footer.php'; ?>
